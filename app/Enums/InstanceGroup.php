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

    public function basePath(): string
    {
        return str_replace(' ', '_', mb_strtolower($this->name));
    }

    public function resultsPath(): string
    {
        return path_join('results', $this->basePath());
    }

    public function resultsFiles(bool $useSmallestRandom = true): array
    {
        $files = glob($this->resultsPath() . '/*.json');

        $randomDir = $useSmallestRandom
            ? 'smallest_random'
            : 'all_random';

        $randomFiles = glob(path_join($this->resultsPath(), "$randomDir/*"));

        return array_merge($files, $randomFiles);
    }
}
