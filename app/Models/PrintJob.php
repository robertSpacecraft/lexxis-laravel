<?php

namespace App\Models;

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
        'estimated_material_g',
        'estimated_time_min',
        'unit_price',
        'pricing_breakdown',
        'status',
    ];

    protected $casts = [
        'quantity'             => 'integer',
        'estimated_material_g'  => 'decimal:2',
        'estimated_time_min'    => 'integer',
        'unit_price'            => 'decimal:2',
        'pricing_breakdown'     => 'array',
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
}
