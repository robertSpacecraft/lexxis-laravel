<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintFileAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'print_file_id',
        'estimated_volume_cm3',
        'estimated_material_g',
        'estimated_time_min',
        'analysis_source',
        'dimensions_mm',
        'triangle_count',
        'analysis_details',
        'manual_review_required',
        'review_reasons',
    ];

    protected $casts = [
        'estimated_volume_cm3' => 'decimal:2',
        'estimated_material_g' => 'decimal:2',
        'estimated_time_min' => 'integer',
        'dimensions_mm' => 'array',
        'triangle_count' => 'integer',
        'analysis_details' => 'array',
        'manual_review_required' => 'boolean',
        'review_reasons' => 'array',
    ];

    public function printFile()
    {
        return $this->belongsTo(PrintFile::class);
    }
}
