<?php

namespace App\Enums;

enum InstanceType: string
{
    case MEDIUM = 'Medium Instance';

    case LARGE = 'Large Instance';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function groupBy(): array
    {
        return match ($this) {
            self::MEDIUM => [
                'vertices',
                'algorithm',
            ],
            self::LARGE => [
                'vertices',
                'edges',
                'algorithm',
            ],
        };
    }

    public function operator(): string
    {
        return match ($this) {
            self::MEDIUM => '<',
            self::LARGE => '>=',
        };
    }

    public static function delimiter(): int
    {
        return 600;
    }

    public static function field(): string
    {
        return 'category';
    }

    public static function orderByRaw(): string
    {
        $medium = self::MEDIUM->value;
        $large = self::LARGE->value;
        $field = self::field();

        return "
            CASE
                WHEN $field = '$medium' THEN 0
                WHEN $field = '$large' THEN 1
                ELSE 2
            END
        ";
    }
}
