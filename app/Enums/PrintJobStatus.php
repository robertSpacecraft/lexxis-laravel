<?php

namespace App\Enums;

enum PrintJobStatus: string
{
    case Draft = 'draft';
    case InCart = 'in_cart';
    case Ordered = 'ordered';
    case Printing = 'printing';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public static function values(): array {
        return array_map(fn(self $c) => $c->value, self::cases());
    }
}
