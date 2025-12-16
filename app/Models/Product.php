<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory; //Me permite crear factories para tests, seeders, tinker.
    use SoftDeletes; //Para no borrar un producto eliminando el histórico.
    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'type',
        'base_price',
        'is_active',
    ];

    //Para el tipado automático
    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    //Función para acceder a todos los Product
    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }

    public function images(){
        return $this->hasMany(ProductImage::class);
    }
}
