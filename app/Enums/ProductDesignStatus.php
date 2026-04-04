<?php

namespace App\Enums;

enum ProductDesignStatus: string
{
    case Draft = 'draft';
    case InCart = 'in_cart';
    case Ordered = 'ordered';
    case Archived = 'archived';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
