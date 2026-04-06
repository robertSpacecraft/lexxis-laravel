<?php

namespace App\Models;

use App\Enums\PrintJobStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'print_file_id',
        'material_id',
        'technology',
        'color_name',
        'quantity',
        'infill_percent',
        'scale_percent',
        'estimated_material_g',
        'estimated_time_min',
        'estimated_volume_cm3',
        'analysis_source',
        'unit_price',
        'pricing_breakdown',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'infill_percent' => 'integer',
        'scale_percent' => 'integer',
        'estimated_material_g' => 'decimal:2',
        'estimated_time_min' => 'integer',
        'estimated_volume_cm3' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'pricing_breakdown' => 'array',
        'status' => PrintJobStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function printFile()
    {
        return $this->belongsTo(PrintFile::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function isEditableByUser(): bool
    {
        $status = $this->status?->value ?? (string) $this->status;

        return in_array($status, [
            PrintJobStatus::Draft->value,
            PrintJobStatus::Priced->value,
        ], true);
    }

    public function isDeletableByUser(): bool
    {
        $status = $this->status?->value ?? (string) $this->status;

        return in_array($status, [
            PrintJobStatus::Draft->value,
            PrintJobStatus::Priced->value,
            PrintJobStatus::ReviewPending->value,
        ], true);
    }
}
