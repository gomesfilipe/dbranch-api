<?php

namespace App\Enums;

use App\Traits\EnumTrait;
use Closure;

enum ValuesFromAlgorithmsMode: string
{
    use EnumTrait;

    case VALUE = 'Value';

    case TIME = 'Time';

    case PERCENTAGE_FROM_VERTICES = 'Percentage From Vertices';
    case OPTIMAL_PERCENTAGE_FROM_VALUES = 'Optimal Percentage From Values';

    public function field(): ?string
    {
        return match ($this) {
            self::VALUE => 'value',
            self::TIME => 'time',
            self::PERCENTAGE_FROM_VERTICES => 'percentage_from_vertices',
            self::OPTIMAL_PERCENTAGE_FROM_VALUES => 'optimal_percentage_from_values',
            default => null,
        };
    }

    public function fieldExpression(): string
    {
        $field = $this->field();
        $exact = Algorithm::EXACT->value;

        return match ($this) {
            self::VALUE, self::TIME => $field,
            self::PERCENTAGE_FROM_VERTICES => "value / vertices as $field",
            self::OPTIMAL_PERCENTAGE_FROM_VALUES => "(
                SELECT
                    CASE
                        WHEN runs.value = 0 THEN
                            1
                        ELSE
                            r.value / runs.value
                    END
                FROM runs as r
                WHERE r.algorithm = '$exact'
                  and r.instance = runs.instance
                  and r.instance_group = runs.instance_group
                  and r.d = runs.d
            ) as $field",
            default => null,
        };
    }

    public function convertFieldType(?string $fieldValue): float|int|string|null
    {
        if (is_null($fieldValue)) {
            return null;
        }

        return match ($this) {
            self::VALUE => intval($fieldValue),
            self::TIME, self::PERCENTAGE_FROM_VERTICES, self::OPTIMAL_PERCENTAGE_FROM_VALUES => floatval($fieldValue),
            default => $fieldValue,
        };
    }
}
