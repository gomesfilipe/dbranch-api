<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum Metric: string
{
    use EnumTrait;

    case MIN = 'min';

    case MEAN = 'mean';

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
