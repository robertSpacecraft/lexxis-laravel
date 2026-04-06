<?php

namespace App\Services;

use App\Models\PrintFile;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use ZipArchive;

class PrintFileAnalysisService
{
    public function analyze(PrintFile $printFile, array $options = []): array
    {
        $relativePath = $printFile->storage_path;

        abort_unless($relativePath && Storage::disk('local')->exists($relativePath), 404, 'El archivo no existe.');

        $absolutePath = Storage::disk('local')->path($relativePath);
        $extension = strtolower((string) $printFile->file_extension);

        $quantity = max(1, (int) ($options['quantity'] ?? 1));
        $scalePercent = max(1, (int) ($options['scale_percent'] ?? 100));
        $infillPercent = max(1, (int) ($options['infill_percent'] ?? 15));
        $technology = strtolower((string) ($options['technology'] ?? 'fdm'));
        $materialType = $options['material_type'] ?? null;

        $result = match ($extension) {
            'stl' => $this->analyzeGeometryFile($absolutePath, 'stl', $quantity, $scalePercent, $infillPercent, $technology, $materialType),
            'obj' => $this->analyzeGeometryFile($absolutePath, 'obj', $quantity, $scalePercent, $infillPercent, $technology, $materialType),
            '3mf' => $this->analyzeGeometryFile($absolutePath, '3mf', $quantity, $scalePercent, $infillPercent, $technology, $materialType),
            'gcode' => $this->analyzeGcode($absolutePath, $quantity),
            default => [
                'estimated_volume_cm3' => null,
                'estimated_material_g' => null,
                'estimated_time_min' => null,
                'analysis_source' => 'unsupported',
                'analysis' => [
                    'notes' => ['Formato no soportado para análisis automático.'],
                ],
            ],
        };

        $manualReviewRequired = $this->requiresManualReview($result);
        $reviewReasons = $this->reviewReasons($result);

        $result['manual_review_required'] = $manualReviewRequired;
        $result['review_reasons'] = $reviewReasons;

        return $result;
    }

    private function requiresManualReview(array $result): bool
    {
        if (!empty($result['manual_review_required'])) {
            return true;
        }

        $material = $result['estimated_material_g'] ?? null;
        $time = $result['estimated_time_min'] ?? null;

        return $material === null || $time === null || $material <= 0 || $time <= 0;
    }

    private function reviewReasons(array $result): array
    {
        $reasons = [];

        $material = $result['estimated_material_g'] ?? null;
        $time = $result['estimated_time_min'] ?? null;
        $volume = $result['estimated_volume_cm3'] ?? null;
        $source = (string) ($result['analysis_source'] ?? 'unknown');

        if ($material === null || $material <= 0) {
            $reasons[] = 'No se ha podido estimar de forma fiable el material.';
        }

        if ($time === null || $time <= 0) {
            $reasons[] = 'No se ha podido estimar de forma fiable el tiempo de impresión.';
        }

        if (!str_starts_with($source, 'gcode_') && $volume === null) {
            $reasons[] = 'No se ha podido calcular el volumen del modelo.';
        }

        if (empty($reasons)) {
            $reasons[] = 'El archivo requiere validación manual por seguridad técnica.';
        }

        return array_values(array_unique($reasons));
    }

    private function analyzeGeometryFile(
        string $absolutePath,
        string $type,
        int $quantity,
        int $scalePercent,
        int $infillPercent,
        string $technology,
        ?string $materialType
    ): array {
        $mesh = match ($type) {
            'stl' => $this->parseStl($absolutePath),
            'obj' => $this->parseObj($absolutePath),
            '3mf' => $this->parse3mf($absolutePath),
            default => null,
        };

        if (!$mesh || empty($mesh['triangles'])) {
            return [
                'estimated_volume_cm3' => null,
                'estimated_material_g' => null,
                'estimated_time_min' => null,
                'analysis_source' => "{$type}_analysis_failed",
                'analysis' => [
                    'notes' => ['No se pudieron extraer métricas geométricas fiables.'],
                ],
            ];
        }

        $scaleFactor = $scalePercent / 100;
        $volumeCm3PerUnit = round((float) $mesh['volume_cm3'] * ($scaleFactor ** 3), 2);
        $fillFactor = $this->fillFactor($infillPercent);
        $density = $this->densityForMaterialType($materialType);
        $materialGPerUnit = round($volumeCm3PerUnit * $density * $fillFactor, 2);

        $minutesPerCm3 = $this->minutesPerCm3($technology);
        $timeFactor = 0.55 + (($infillPercent / 100) * 0.75);
        $timeMinPerUnit = (int) max(1, round($volumeCm3PerUnit * $minutesPerCm3 * $timeFactor));

        $dimensionsMm = [
            'x' => round(((float) $mesh['bbox']['x'] ?? 0) * $scaleFactor, 2),
            'y' => round(((float) $mesh['bbox']['y'] ?? 0) * $scaleFactor, 2),
            'z' => round(((float) $mesh['bbox']['z'] ?? 0) * $scaleFactor, 2),
        ];

        return [
            'estimated_volume_cm3' => round($volumeCm3PerUnit * $quantity, 2),
            'estimated_material_g' => round($materialGPerUnit * $quantity, 2),
            'estimated_time_min' => (int) round($timeMinPerUnit * $quantity),
            'analysis_source' => "{$type}_geometry",
            'analysis' => [
                'notes' => [
                    'Estimación geométrica automática basada en volumen del modelo.',
                    'El volumen se ha ajustado según la escala y el relleno seleccionado.',
                ],
                'per_unit' => [
                    'volume_cm3' => $volumeCm3PerUnit,
                    'material_g' => $materialGPerUnit,
                    'time_min' => $timeMinPerUnit,
                ],
                'quantity' => $quantity,
                'scale_percent' => $scalePercent,
                'infill_percent' => $infillPercent,
                'dimensions_mm' => $dimensionsMm,
                'triangle_count' => (int) $mesh['triangles'],
                'material_density_g_cm3' => $density,
                'fill_factor' => $fillFactor,
            ],
        ];
    }

    private function analyzeGcode(string $absolutePath, int $quantity): array
    {
        $content = @file_get_contents($absolutePath);

        if ($content === false) {
            return [
                'estimated_volume_cm3' => null,
                'estimated_material_g' => null,
                'estimated_time_min' => null,
                'analysis_source' => 'gcode_analysis_failed',
                'analysis' => [
                    'notes' => ['No se pudo leer el GCODE.'],
                ],
            ];
        }

        $grams = $this->extractFirstFloat($content, [
            '/filament used \[g\]\s*=\s*([0-9]+(?:\.[0-9]+)?)/i',
            '/filament used\s*=\s*([0-9]+(?:\.[0-9]+)?)g/i',
            '/total filament used \[g\]\s*=\s*([0-9]+(?:\.[0-9]+)?)/i',
        ]);

        if ($grams === null) {
            $meters = $this->extractFirstFloat($content, [
                '/filament used\s*=\s*([0-9]+(?:\.[0-9]+)?)m/i',
                '/filament used \[m\]\s*=\s*([0-9]+(?:\.[0-9]+)?)/i',
            ]);

            if ($meters !== null) {
                $grams = round($meters * 2.98, 2);
            }
        }

        $timeSeconds = $this->extractFirstInt($content, [
            '/^;TIME:([0-9]+)/mi',
            '/estimated printing time.*?([0-9]+)\s*s/i',
        ]);

        $timeMin = $timeSeconds !== null ? (int) max(1, round($timeSeconds / 60)) : null;

        return [
            'estimated_volume_cm3' => null,
            'estimated_material_g' => $grams !== null ? round($grams * $quantity, 2) : null,
            'estimated_time_min' => $timeMin !== null ? (int) round($timeMin * $quantity) : null,
            'analysis_source' => 'gcode_embedded',
            'analysis' => [
                'notes' => [
                    'Estimación extraída del GCODE ya laminado.',
                    'La escala y el relleno no se recalculan automáticamente para GCODE.',
                ],
                'per_unit' => [
                    'material_g' => $grams,
                    'time_min' => $timeMin,
                ],
                'quantity' => $quantity,
            ],
        ];
    }

    private function parseStl(string $absolutePath): ?array
    {
        $handle = @fopen($absolutePath, 'rb');

        if (!$handle) {
            return null;
        }

        $header = fread($handle, 84);
        fclose($handle);

        if ($header === false || strlen($header) < 84) {
            return null;
        }

        $triangleCount = unpack('V', substr($header, 80, 4))[1] ?? 0;
        $expectedBinarySize = 84 + ($triangleCount * 50);
        $actualSize = filesize($absolutePath);

        if ($triangleCount > 0 && $actualSize === $expectedBinarySize) {
            return $this->parseBinaryStl($absolutePath, $triangleCount);
        }

        return $this->parseAsciiStl($absolutePath);
    }

    private function parseBinaryStl(string $absolutePath, int $triangleCount): ?array
    {
        $handle = @fopen($absolutePath, 'rb');
        if (!$handle) {
            return null;
        }

        fseek($handle, 84);

        $volumeMm3 = 0.0;
        $mins = ['x' => INF, 'y' => INF, 'z' => INF];
        $maxs = ['x' => -INF, 'y' => -INF, 'z' => -INF];

        for ($i = 0; $i < $triangleCount; $i++) {
            $chunk = fread($handle, 50);
            if ($chunk === false || strlen($chunk) < 50) {
                break;
            }

            $data = unpack('f12', substr($chunk, 0, 48));

            $v1 = [$data[4], $data[5], $data[6]];
            $v2 = [$data[7], $data[8], $data[9]];
            $v3 = [$data[10], $data[11], $data[12]];

            $this->expandBounds($mins, $maxs, $v1, $v2, $v3);
            $volumeMm3 += $this->triangleSignedVolumeMm3($v1, $v2, $v3);
        }

        fclose($handle);

        return [
            'triangles' => $triangleCount,
            'volume_cm3' => abs($volumeMm3) / 1000,
            'bbox' => [
                'x' => $maxs['x'] - $mins['x'],
                'y' => $maxs['y'] - $mins['y'],
                'z' => $maxs['z'] - $mins['z'],
            ],
        ];
    }

    private function parseAsciiStl(string $absolutePath): ?array
    {
        $lines = @file($absolutePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return null;
        }

        $vertices = [];
        $triangleCount = 0;
        $volumeMm3 = 0.0;
        $mins = ['x' => INF, 'y' => INF, 'z' => INF];
        $maxs = ['x' => -INF, 'y' => -INF, 'z' => -INF];

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^vertex\s+([\-0-9eE\.]+)\s+([\-0-9eE\.]+)\s+([\-0-9eE\.]+)/', $line, $m)) {
                $vertices[] = [(float) $m[1], (float) $m[2], (float) $m[3]];

                if (count($vertices) === 3) {
                    $triangleCount++;
                    $this->expandBounds($mins, $maxs, $vertices[0], $vertices[1], $vertices[2]);
                    $volumeMm3 += $this->triangleSignedVolumeMm3($vertices[0], $vertices[1], $vertices[2]);
                    $vertices = [];
                }
            }
        }

        if ($triangleCount === 0) {
            return null;
        }

        return [
            'triangles' => $triangleCount,
            'volume_cm3' => abs($volumeMm3) / 1000,
            'bbox' => [
                'x' => $maxs['x'] - $mins['x'],
                'y' => $maxs['y'] - $mins['y'],
                'z' => $maxs['z'] - $mins['z'],
            ],
        ];
    }

    private function parseObj(string $absolutePath): ?array
    {
        $lines = @file($absolutePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return null;
        }

        $vertices = [];
        $triangles = [];
        $mins = ['x' => INF, 'y' => INF, 'z' => INF];
        $maxs = ['x' => -INF, 'y' => -INF, 'z' => -INF];

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, 'v ')) {
                $parts = preg_split('/\s+/', $line);
                if (count($parts) >= 4) {
                    $vertex = [(float) $parts[1], (float) $parts[2], (float) $parts[3]];
                    $vertices[] = $vertex;
                    $this->expandBounds($mins, $maxs, $vertex);
                }
            }

            if (str_starts_with($line, 'f ')) {
                $parts = preg_split('/\s+/', $line);
                $indices = [];

                foreach (array_slice($parts, 1) as $facePart) {
                    $index = explode('/', $facePart)[0];
                    if ($index !== '') {
                        $indices[] = (int) $index - 1;
                    }
                }

                if (count($indices) >= 3) {
                    $base = $indices[0];
                    for ($i = 1; $i < count($indices) - 1; $i++) {
                        $triangles[] = [$base, $indices[$i], $indices[$i + 1]];
                    }
                }
            }
        }

        if (empty($vertices) || empty($triangles)) {
            return null;
        }

        $volumeMm3 = 0.0;
        foreach ($triangles as $tri) {
            if (!isset($vertices[$tri[0]], $vertices[$tri[1]], $vertices[$tri[2]])) {
                continue;
            }

            $volumeMm3 += $this->triangleSignedVolumeMm3(
                $vertices[$tri[0]],
                $vertices[$tri[1]],
                $vertices[$tri[2]],
            );
        }

        return [
            'triangles' => count($triangles),
            'volume_cm3' => abs($volumeMm3) / 1000,
            'bbox' => [
                'x' => $maxs['x'] - $mins['x'],
                'y' => $maxs['y'] - $mins['y'],
                'z' => $maxs['z'] - $mins['z'],
            ],
        ];
    }

    private function parse3mf(string $absolutePath): ?array
    {
        $zip = new ZipArchive();

        if ($zip->open($absolutePath) !== true) {
            return null;
        }

        $modelXml = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if ($name && str_ends_with(strtolower($name), '.model')) {
                $modelXml = $zip->getFromIndex($i);
                break;
            }
        }

        $zip->close();

        if (!$modelXml) {
            return null;
        }

        $xml = @simplexml_load_string($modelXml);
        if (!$xml instanceof SimpleXMLElement) {
            return null;
        }

        $xml->registerXPathNamespace('m', 'http://schemas.microsoft.com/3dmanufacturing/core/2015/02');

        $verticesNodes = $xml->xpath('//m:vertex');
        $trianglesNodes = $xml->xpath('//m:triangle');

        if (!$verticesNodes || !$trianglesNodes) {
            return null;
        }

        $vertices = [];
        $mins = ['x' => INF, 'y' => INF, 'z' => INF];
        $maxs = ['x' => -INF, 'y' => -INF, 'z' => -INF];

        foreach ($verticesNodes as $node) {
            $vertex = [
                (float) $node['x'],
                (float) $node['y'],
                (float) $node['z'],
            ];
            $vertices[] = $vertex;
            $this->expandBounds($mins, $maxs, $vertex);
        }

        $volumeMm3 = 0.0;
        $trianglesCount = 0;

        foreach ($trianglesNodes as $node) {
            $i1 = (int) $node['v1'];
            $i2 = (int) $node['v2'];
            $i3 = (int) $node['v3'];

            if (!isset($vertices[$i1], $vertices[$i2], $vertices[$i3])) {
                continue;
            }

            $trianglesCount++;
            $volumeMm3 += $this->triangleSignedVolumeMm3(
                $vertices[$i1],
                $vertices[$i2],
                $vertices[$i3],
            );
        }

        if ($trianglesCount === 0) {
            return null;
        }

        return [
            'triangles' => $trianglesCount,
            'volume_cm3' => abs($volumeMm3) / 1000,
            'bbox' => [
                'x' => $maxs['x'] - $mins['x'],
                'y' => $maxs['y'] - $mins['y'],
                'z' => $maxs['z'] - $mins['z'],
            ],
        ];
    }

    private function expandBounds(array &$mins, array &$maxs, array ...$vertices): void
    {
        foreach ($vertices as $vertex) {
            $mins['x'] = min($mins['x'], $vertex[0]);
            $mins['y'] = min($mins['y'], $vertex[1]);
            $mins['z'] = min($mins['z'], $vertex[2]);

            $maxs['x'] = max($maxs['x'], $vertex[0]);
            $maxs['y'] = max($maxs['y'], $vertex[1]);
            $maxs['z'] = max($maxs['z'], $vertex[2]);
        }
    }

    private function triangleSignedVolumeMm3(array $v1, array $v2, array $v3): float
    {
        return (
                $v1[0] * $v2[1] * $v3[2]
                + $v2[0] * $v3[1] * $v1[2]
                + $v3[0] * $v1[1] * $v2[2]
                - $v1[0] * $v3[1] * $v2[2]
                - $v2[0] * $v1[1] * $v3[2]
                - $v3[0] * $v2[1] * $v1[2]
            ) / 6.0;
    }

    private function densityForMaterialType(?string $materialType): float
    {
        return match (strtolower((string) $materialType)) {
            'pla' => 1.24,
            'petg' => 1.27,
            'abs' => 1.04,
            'tpu' => 1.21,
            default => 1.20,
        };
    }

    private function fillFactor(int $infillPercent): float
    {
        return 0.35 + (($infillPercent / 100) * 0.65);
    }

    private function minutesPerCm3(string $technology): float
    {
        return match (strtolower($technology)) {
            'fdm' => 14.0,
            'sla' => 10.0,
            'sls' => 8.0,
            default => 14.0,
        };
    }

    private function extractFirstFloat(string $content, array $patterns): ?float
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return (float) $matches[1];
            }
        }

        return null;
    }

    private function extractFirstInt(string $content, array $patterns): ?int
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return (int) $matches[1];
            }
        }

        return null;
    }
}
