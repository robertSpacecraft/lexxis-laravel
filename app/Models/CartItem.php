<?php

namespace App\Models;

use App\Enums\CartItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'product_design_id',
        'print_job_id',
        'type',
        'quantity',
        'unit_price',
        'subtotal',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'metadata' => 'array',
        'type' => CartItemType::class,
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function productDesign()
    {
        return $this->belongsTo(ProductDesign::class);
    }

    public function printJob()
    {
        return $this->belongsTo(PrintJob::class);
    }

    public function isProductVariant(): bool
    {
        return ($this->type?->value ?? (string) $this->type) === CartItemType::ProductVariant->value;
    }

    public function isProductDesign(): bool
    {
        return ($this->type?->value ?? (string) $this->type) === CartItemType::ProductDesign->value;
    }

    public function isPrintJob(): bool
    {
        return ($this->type?->value ?? (string) $this->type) === CartItemType::PrintJob->value;
    }
}
