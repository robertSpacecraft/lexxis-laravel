<?php

namespace App\Enums;

enum CartItemType: string
{
    case ProductVariant = 'product_variant';
    case ProductDesign = 'product_design';
    case PrintJob = 'print_job';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
