<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'description',
        'is_active',
    ];

    protected $casts = [
        'shore_a' => 'integer',
        'is_active' => 'boolean',
    ];
    public function productVariant(){
        return $this->hasMany(ProductVariant::class);
    }

    public function printJobs(){
        return $this->hasMany(PrintJob::class);
    }
}
