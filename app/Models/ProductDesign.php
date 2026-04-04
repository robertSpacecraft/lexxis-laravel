<?php

namespace App\Models;

use App\Enums\ProductDesignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDesign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'material_id',
        'color_name',
        'size_eu',
        'unit_price',
        'pricing_breakdown',
        'status',
    ];

    protected $casts = [
        'size_eu' => 'decimal:1',
        'unit_price' => 'decimal:2',
        'pricing_breakdown' => 'array',
        'status' => ProductDesignStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isDraft(): bool
    {
        return ($this->status?->value ?? (string) $this->status) === ProductDesignStatus::Draft->value;
    }
}
