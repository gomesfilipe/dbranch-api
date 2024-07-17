<?php

namespace App\Enums;

use App\Traits\EnumTrait;
use Illuminate\Support\Facades\DB;

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
                DB::raw('null as time'),
            ],
            self::MEAN => [
                'vertices',
                'algorithm',
                'edges',
                'mean',
                DB::raw('null as time'),
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
