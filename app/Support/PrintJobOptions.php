<?php

namespace App\Support;

class PrintJobOptions
{
    public const TECHNOLOGIES = ['fdm'];

    public const INFILL_PERCENTS = [5, 15, 40];

    public const QUANTITY_MIN = 1;
    public const QUANTITY_MAX = 999;

    public const SCALE_PERCENT_MIN = 10;
    public const SCALE_PERCENT_MAX = 300;

    public const DEFAULT_TECHNOLOGY = 'fdm';
    public const DEFAULT_INFILL_PERCENT = 15;
    public const DEFAULT_SCALE_PERCENT = 100;
    public const DEFAULT_QUANTITY = 1;

    public static function technologies(): array
    {
        return self::TECHNOLOGIES;
    }

    public static function technologyOptions(): array
    {
        return array_map(
            fn (string $value) => [
                'value' => $value,
                'label' => strtoupper($value),
            ],
            self::TECHNOLOGIES
        );
    }

    public static function infillPercents(): array
    {
        return self::INFILL_PERCENTS;
    }

    public static function defaults(): array
    {
        return [
            'technology' => self::DEFAULT_TECHNOLOGY,
            'infill_percent' => self::DEFAULT_INFILL_PERCENT,
            'scale_percent' => self::DEFAULT_SCALE_PERCENT,
            'quantity' => self::DEFAULT_QUANTITY,
        ];
    }
}
