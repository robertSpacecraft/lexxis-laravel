<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'material_id',
        'sku',
        'size_eu',
        'color_name',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'size_eu' => 'decimal:1',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function material(){
        return $this->belongsTo(Material::class);
    }
}
