<?php

namespace Database\Seeders;

use App\Enums\CartItemType;
use App\Enums\PrintFileStatus;
use App\Enums\PrintJobStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Material;
use App\Models\PrintFile;
use App\Models\PrintFileAnalysis;
use App\Models\PrintJob;
use App\Models\ProductDesign;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoPrintFilesAndJobsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()
            ->whereIn('email', [
                'demo@lexxis.test',
                'maria@lexxis.test',
                'carlos@lexxis.test',
            ])
            ->get()
            ->keyBy('email');

        $materials = Material::query()
            ->whereIn('slug', [
                'tpu-flex-95a',
                'tpu-comfort-90a',
                'pla-display',
            ])
            ->get()
            ->keyBy('slug');

        if ($users->isEmpty() || $materials->isEmpty()) {
            $this->command?->warn('Faltan usuarios o materiales para sembrar print files/jobs demo.');
            return;
        }

        $jobs = [];

        foreach ($this->demoPrintFiles() as $payload) {
            $user = $users->get($payload['email']);
            $material = $materials->get($payload['material_slug']);

            if (!$user || !$material) {
                $this->command?->warn("Se omite print file demo {$payload['original_name']} por faltar usuario o material.");
                continue;
            }

            $printFile = PrintFile::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'original_name' => $payload['original_name'],
                ],
                [
                    'storage_path' => "demo/print-files/{$user->id}/{$payload['original_name']}",
                    'mime_type' => $payload['mime_type'],
                    'file_extension' => $payload['file_extension'],
                    'file_size' => $payload['file_size'],
                    'status' => $payload['file_status'],
                    'notes' => 'Archivo demo sin binario físico. Usado para poblar vistas y flujos locales.',
                    'metadata' => [
                        'source' => 'demo_seed',
                        'has_physical_file' => false,
                    ],
                ]
            );

            PrintFileAnalysis::updateOrCreate(
                ['print_file_id' => $printFile->id],
                [
                    'estimated_volume_cm3' => $payload['estimated_volume_cm3'],
                    'estimated_material_g' => $payload['estimated_material_g'],
                    'estimated_time_min' => $payload['estimated_time_min'],
                    'analysis_source' => 'demo_seed',
                    'dimensions_mm' => $payload['dimensions_mm'],
                    'triangle_count' => $payload['triangle_count'],
                    'analysis_details' => [
                        'notes' => [
                            'Estimación demo creada por seeder.',
                            'No requiere archivo físico para navegación local.',
                        ],
                    ],
                    'manual_review_required' => false,
                    'review_reasons' => [],
                ]
            );

            $job = PrintJob::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'print_file_id' => $printFile->id,
                    'material_id' => $material->id,
                    'technology' => $payload['technology'],
                    'color_name' => $payload['color_name'],
                ],
                [
                    'quantity' => $payload['quantity'],
                    'infill_percent' => $payload['infill_percent'],
                    'scale_percent' => $payload['scale_percent'],
                    'estimated_material_g' => $payload['estimated_material_g'],
                    'estimated_time_min' => $payload['estimated_time_min'],
                    'estimated_volume_cm3' => $payload['estimated_volume_cm3'],
                    'analysis_source' => 'demo_seed',
                    'unit_price' => $payload['unit_price'],
                    'pricing_breakdown' => [
                        'source' => 'demo_seed',
                        'material' => $payload['estimated_material_g'],
                        'time' => $payload['estimated_time_min'],
                    ],
                    'status' => $payload['job_status'],
                ]
            );

            $jobs[$payload['cart_key']] = $job;
        }

        $this->seedActiveCarts($users, $jobs);
    }

    private function demoPrintFiles(): array
    {
        return [
            [
                'email' => 'demo@lexxis.test',
                'cart_key' => 'demo-print-job',
                'original_name' => 'demo_suela_future.stl',
                'mime_type' => 'model/stl',
                'file_extension' => 'stl',
                'file_size' => 428000,
                'file_status' => PrintFileStatus::Reviewed,
                'material_slug' => 'tpu-flex-95a',
                'technology' => 'fdm',
                'color_name' => 'Negro',
                'quantity' => 1,
                'infill_percent' => 15,
                'scale_percent' => 100,
                'estimated_volume_cm3' => 46.20,
                'estimated_material_g' => 57.30,
                'estimated_time_min' => 240,
                'dimensions_mm' => ['x' => 285, 'y' => 96, 'z' => 34],
                'triangle_count' => 18500,
                'unit_price' => 39.90,
                'job_status' => PrintJobStatus::InCart,
            ],
            [
                'email' => 'maria@lexxis.test',
                'cart_key' => 'maria-print-job',
                'original_name' => 'demo_upper_urban.3mf',
                'mime_type' => 'model/3mf',
                'file_extension' => '3mf',
                'file_size' => 780000,
                'file_status' => PrintFileStatus::Reviewed,
                'material_slug' => 'tpu-comfort-90a',
                'technology' => 'fdm',
                'color_name' => 'Arena',
                'quantity' => 1,
                'infill_percent' => 15,
                'scale_percent' => 100,
                'estimated_volume_cm3' => 52.10,
                'estimated_material_g' => 62.50,
                'estimated_time_min' => 285,
                'dimensions_mm' => ['x' => 260, 'y' => 110, 'z' => 78],
                'triangle_count' => 24600,
                'unit_price' => 44.90,
                'job_status' => PrintJobStatus::Priced,
            ],
            [
                'email' => 'carlos@lexxis.test',
                'cart_key' => 'carlos-print-job',
                'original_name' => 'demo_display_mocca.obj',
                'mime_type' => 'model/obj',
                'file_extension' => 'obj',
                'file_size' => 512000,
                'file_status' => PrintFileStatus::Uploaded,
                'material_slug' => 'pla-display',
                'technology' => 'fdm',
                'color_name' => 'Mocca',
                'quantity' => 1,
                'infill_percent' => 40,
                'scale_percent' => 100,
                'estimated_volume_cm3' => 38.40,
                'estimated_material_g' => 49.20,
                'estimated_time_min' => 210,
                'dimensions_mm' => ['x' => 180, 'y' => 95, 'z' => 105],
                'triangle_count' => 13200,
                'unit_price' => 34.90,
                'job_status' => PrintJobStatus::ReviewPending,
            ],
        ];
    }

    private function seedActiveCarts($users, array $jobs): void
    {
        $demoUser = $users->get('demo@lexxis.test');
        $mariaUser = $users->get('maria@lexxis.test');

        if ($demoUser) {
            $cart = Cart::firstOrCreate([
                'user_id' => $demoUser->id,
                'status' => 'active',
            ]);

            $variant = ProductVariant::query()->where('sku', 'FLOW-BLK-42-95A')->first();
            if ($variant) {
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_variant_id' => $variant->id,
                        'product_design_id' => null,
                        'print_job_id' => null,
                    ],
                    [
                        'type' => CartItemType::ProductVariant,
                        'quantity' => 1,
                        'unit_price' => $variant->price,
                        'subtotal' => $variant->price,
                        'metadata' => ['source' => 'demo_seed'],
                    ]
                );
            }

            $design = ProductDesign::query()
                ->where('user_id', $demoUser->id)
                ->where('status', 'in_cart')
                ->first();

            if ($design) {
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_variant_id' => null,
                        'product_design_id' => $design->id,
                        'print_job_id' => null,
                    ],
                    [
                        'type' => CartItemType::ProductDesign,
                        'quantity' => 1,
                        'unit_price' => $design->unit_price,
                        'subtotal' => $design->unit_price,
                        'metadata' => ['source' => 'demo_seed'],
                    ]
                );
            }

            if (isset($jobs['demo-print-job'])) {
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_variant_id' => null,
                        'product_design_id' => null,
                        'print_job_id' => $jobs['demo-print-job']->id,
                    ],
                    [
                        'type' => CartItemType::PrintJob,
                        'quantity' => 1,
                        'unit_price' => $jobs['demo-print-job']->unit_price,
                        'subtotal' => $jobs['demo-print-job']->unit_price,
                        'metadata' => ['source' => 'demo_seed'],
                    ]
                );
            }
        }

        if ($mariaUser) {
            $cart = Cart::firstOrCreate([
                'user_id' => $mariaUser->id,
                'status' => 'active',
            ]);

            $variant = ProductVariant::query()->where('sku', 'URBAN-WHT-43-95A')->first();
            if ($variant) {
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_variant_id' => $variant->id,
                        'product_design_id' => null,
                        'print_job_id' => null,
                    ],
                    [
                        'type' => CartItemType::ProductVariant,
                        'quantity' => 2,
                        'unit_price' => $variant->price,
                        'subtotal' => number_format((float) $variant->price * 2, 2, '.', ''),
                        'metadata' => ['source' => 'demo_seed'],
                    ]
                );
            }
        }
    }
}
