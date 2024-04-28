<?php

namespace App\Enums;

enum Metric: string
{
    case MIN = 'min';

    case MEAN = 'mean';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function optimalColumns(): array
    {
        return match ($this) {
            self::MIN => [
                'vertices',
                'algorithm',
                'edges',
                'min',
            ],
            self::MEAN => [
                'vertices',
                'algorithm',
                'edges',
                'mean',
            ],
        };
    }

    public function sqlMetric(): string
    {
        return match ($this) {
            self::MIN => 'MIN',
            self::MEAN => 'AVG',
        };
    }
}
