<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'print_job_id',
        'item_name',
        'unit_price',
        'quantity',
        'subtotal',
        'metadata',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity'   => 'integer',
        'subtotal'   => 'decimal:2',
        'metadata'   => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function printJob()
    {
        return $this->belongsTo(PrintJob::class);
    }
}
