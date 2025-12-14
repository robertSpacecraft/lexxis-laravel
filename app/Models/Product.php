<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory; //Me permite crear factories para tests, seeders, tinker.
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
}
