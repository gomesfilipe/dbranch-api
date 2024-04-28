<?php

namespace App\Services;

use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Models\Run;
use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Support\Collection;

class RunService
{
    public function __construct(
        private readonly RunRepositoryInterface $runRepository,
    )
    {
        //
    }

    public function results(InstanceType $instanceType, Metric $metric): array
    {
        return $this->runRepository
            ->results($instanceType, $metric)
            ->map(fn (Run $item) => $item->toArray())
            ->groupBy(['vertices', 'edges'])
            ->map(function (Collection $verticeItem, int $verticeGroup)
            {
                return $verticeItem->map(function (Collection $edgeItem, string $edgeGroup) use ($verticeGroup)
                {
                    $values = $edgeItem->reduce(function (array $carry, array $item) use ($edgeGroup, $verticeGroup)
                    {
                        return array_merge($carry, [
                            $item['algorithm'] => floatval($item['value']),
                        ]);
                    }, []);

                    return collect([
                        'vertices' => $verticeGroup,
                        'edges' => floatval($edgeGroup),
                        ...$values,
                    ])
                    ->sortBy(fn (int|float $value, string $key) => $this->sortKeysCallback($key))
                    ->toArray();
                })
                ->toArray();
            })
            ->flatten(1)
            ->toArray();
    }

    public function gapResults(InstanceType $instanceType): array
    {
        $metric = Metric::MIN;
        $results = $this->results($instanceType, $metric);

        return collect($results)->map(function (array $item)
        {
                $excludeColumns = [
                    'vertices',
                    'edges',
                ];

                $optimalKey = 'Exact';
                $optimalValue = $item[$optimalKey];

                return collect($item)->map(function (int|float $value, string $key) use ($excludeColumns, $optimalValue)
                    {
                        return in_array($key, $excludeColumns)
                            ? $value
                            : $this->gap($optimalValue, $value);
                    })
                    ->filter(fn (int|float|null $value, string $key) => $key !== $optimalKey)
                    ->toArray();
        })
        ->toArray();
    }

    private function sortKeysCallback(string $key): int
    {
        return match ($key) {
            'vertices' => 0,
            'edges' => 1,
            'Exact' => 2,
            'Moreno Et Al' => 3,
            default => 4,
        };
    }

    private function gap(float $ref, float $value): ?float
    {
        return $ref === 0.0
            ? null
            : round(100.0 * ($value - $ref) / $ref, 1);
    }
}
