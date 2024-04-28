<?php

namespace App\Enums;

enum InstanceType: string
{
    case MEDIUM = 'Medium Instance';

    case LARGE = 'Large Instance';

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
}
