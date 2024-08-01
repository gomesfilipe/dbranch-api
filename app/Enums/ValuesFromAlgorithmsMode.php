<?php

namespace App\Enums;

use App\Traits\EnumTrait;
use Closure;

enum ValuesFromAlgorithmsMode: string
{
    use EnumTrait;

    case VALUE = 'Value';

    case TIME = 'Time';

    public function field(): ?string
    {
        return match ($this) {
            self::VALUE => 'value',
            self::TIME => 'time',
            default => null,
        };
    }

    public function convertFieldTypeCallback(?string $fieldValue): float|int|string|null
    {
        if (is_null($fieldValue)) {
            return null;
        }

        return match ($this) {
            self::VALUE => intval($fieldValue),
            self::TIME => floatval($fieldValue),
            default => $fieldValue,
        };
    }
}
