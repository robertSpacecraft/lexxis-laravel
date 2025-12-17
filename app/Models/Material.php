<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use App\Models\PrintJob;


class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'material_type',
        'brand',
        'supplier',
        'shore_a',
        'shore_scale',
        'shore_value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'shore_a' => 'integer',
        'shore_value' => 'integer',
        'is_active' => 'boolean',
    ];
    public function productVariants(){
        return $this->hasMany(ProductVariant::class);
    }

    public function printJobs(){
        return $this->hasMany(PrintJob::class);
    }
}
