<?php

namespace App\Services;

use App\Enums\InstanceType;
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

    public function minResults(InstanceType $instanceType): array
    {
        return $this->runRepository
            ->minResults($instanceType)
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
                });
            })
            ->flatten(1)
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
}
