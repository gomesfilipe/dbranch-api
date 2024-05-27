<?php

namespace App\Services;

use App\Enums\Algorithm;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Jobs\StoreRunsJob;
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

    public function createManyAsync(array $data): void
    {
        $slice = 5000;

        collect($data)->chunk($slice)
            ->each(fn (Collection $slicedData) => StoreRunsJob::dispatch($slicedData->toArray()));
    }

    public function results(InstanceType $instanceType, Metric $metric, array $params = []): array
    {
        return $this->runRepository
            ->results($instanceType, $metric, $params)
            ->map(fn (Run $item) => $item->toArray())
            ->groupBy(['vertices', 'edges'])
            ->map(function (Collection $verticeItem, int $verticeGroup)
            {
                return $verticeItem->map(function (Collection $edgeItem, string $edgeGroup) use ($verticeGroup)
                {
                    $values = $edgeItem->reduce(function (array $carry, array $item) use ($edgeGroup, $verticeGroup)
                    {
                        $algorithm = Algorithm::from($item['algorithm'])->name;

                        return array_merge($carry, [
                            $algorithm => floatval($item['value']),
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

    public function gapResults(InstanceType $instanceType, array $params = []): array
    {
        $metric = Metric::MIN;
        $results = $this->results($instanceType, $metric, $params);

        return collect($results)->map(function (array $item)
        {
                $excludeColumns = [
                    'vertices',
                    'edges',
                ];

                $optimalKey = Algorithm::EXACT->name;
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
            Algorithm::EXACT->name => 2,
            Algorithm::MORENO_ET_AL->name => 3,
            Algorithm::BEP_ANDERSON->name => 4,
            Algorithm::BEP->name => 5,
            Algorithm::PR_BEP->name => 6,
            Algorithm::R_BEP_ANDERSON->name => 7,
            Algorithm::R_BEP->name => 8,
            Algorithm::R_PR_BEP->name => 9,
            default => 10,
        };
    }

    private function gap(float $ref, float $value): ?float
    {
        return $ref === 0.0
            ? null
            : round(100.0 * ($value - $ref) / $ref, 1);
    }

    public function compareDiffs(Algorithm $algorithmA, Algorithm $algorithmB): array
    {
        return $this->runRepository->compareDiffs($algorithmA, $algorithmB)
            ->map(function (\stdClass $item)
            {
                $item = (array) $item;
                $item['diff'] = intval($item['diff']);
                return $item;
            })
            ->toArray();
    }
}
