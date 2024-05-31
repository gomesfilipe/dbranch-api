<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum InstanceGroup: string
{
    use EnumTrait;

    case SPD_RF2 = 'Spd RF2';

    case LEIGHTON = 'Leighton';

    case LIKE_MERABET = 'Like Merabet';

    public function groupBy(?InstanceType $instanceType = null): array
    {
        $default = [
            'vertices',
            'edges',
            'algorithm',
        ];

        return match ($this) {
            self::SPD_RF2 => $instanceType?->groupBy(),
            default => null,
        } ?? $default;
    }
}
