<?php

namespace App\Services;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Enums\ValuesFromAlgorithmsMode;
use App\Jobs\StoreRunsJob;
use App\Models\Run;
use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RunService
{
    public function __construct(
        private readonly RunRepositoryInterface $runRepository,
    )
    {
        //
    }

    public function createManyAsync(array $data, int $slice = 5000): void
    {
        collect($data)->chunk($slice)
            ->each(fn (Collection $slicedData) => StoreRunsJob::dispatch($slicedData->toArray()));
    }

    // Os elementos de $results devem possuir os seguintes campos: 'vertices', 'edges', 'algorithm' e $resultField.
    public function generateAlgorithmsColumns(Collection $results, string $resultField, bool $includeTime = false): array
    {
        // Itens são instâncias de Model ou stdClass.
        return $results
            ->map(fn ($item) => $item instanceof Model
                ? $item->toArray()
                : (array) $item
            )
            ->groupBy(['vertices', 'edges'])
            ->map(function (Collection $verticeItem, int $verticeGroup) use ($resultField, $includeTime)
            {
                return $verticeItem->map(function (Collection $edgeItem, string $edgeGroup) use ($verticeGroup, $resultField, $includeTime)
                {
                    $values = $edgeItem->reduce(function (array $carry, array $item) use ($edgeGroup, $verticeGroup, $resultField, $includeTime)
                    {
                        $algorithm = Algorithm::from($item['algorithm']);

                        $cell = array_merge($carry, [
                            $algorithm->name => floatval($item[$resultField]),
                        ]);

                        $time = $item['time'] ?? null;

                        if ($includeTime && ! is_null($time)) {
                            $cell = array_merge($cell, [
                                $algorithm->timeColumn() => floatval($time),
                            ]);
                        }

                        return $cell;
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

    public function results(InstanceType $instanceType, Metric $metric, InstanceGroup $instanceGroup, array $params = [], bool $includeTime = false): array
    {
        return $this->generateAlgorithmsColumns(
            $this->runRepository->results($instanceType, $metric, $instanceGroup, $params),
            'value',
            $includeTime,
        );
    }

    public function gapResults(InstanceType $instanceType, InstanceGroup $instanceGroup, array $params = []): array
    {
        $metric = Metric::MIN;
        $results = $this->results($instanceType, $metric, $instanceGroup, $params);

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
        # Prioridade baseada na posição do array.
        $algorithmsByPriority = [
            Algorithm::EXACT,
            Algorithm::MORENO_ET_AL,
            Algorithm::BEP_ANDERSON,
            Algorithm::BEP,
            Algorithm::PR_BEP,
            Algorithm::R_BEP_ANDERSON,
            Algorithm::R_BEP,
            Algorithm::R_PR_BEP,
            Algorithm::GRASP_R_BEP_TVS,
            Algorithm::GRASP_R_BEP_B_TVS,
        ];

        $columnsByPriority = [
            'vertices',
            'edges',
        ];

        foreach ($algorithmsByPriority as $algorithm) {
            $columnsByPriority[] = $algorithm->name;
            $columnsByPriority[] = $algorithm->timeColumn();
        }

        $index = array_search($key, $columnsByPriority);

        return $index === false
            ? count($columnsByPriority)
            : $index;
    }

    private function gap(float $ref, float $value): ?float
    {
        if ($ref === $value) {
            return 0.0;
        }

        return $ref === 0.0
            ? null
            : round(100.0 * ($value - $ref) / $ref, 1);
    }

    public function compareDiffs(Algorithm $algorithmA, Algorithm $algorithmB, array $params = []): array
    {
        return $this->runRepository->compareDiffs($algorithmA, $algorithmB, $params)
            ->map(function (\stdClass $item)
            {
                $item = (array) $item;
                $item['diff'] = intval($item['diff']);
                return $item;
            })
            ->toArray();
    }

    public function verticesClassificationAccuracy(InstanceType $instanceType, InstanceGroup $instanceGroup, array $params = []): array
    {
        return $this->generateAlgorithmsColumns(
            $this->runRepository->verticesClassificationAccuracy($instanceType, $instanceGroup, $params),
            'accuracy_avg'
        );
    }

    public function distancesFromOptimal(InstanceGroup $instanceGroup, Algorithm $algorithm, array $hyperparameters, int $d = 2, ?InstanceType $instanceType = null, bool $groupByVerticesOnly = false): array
    {
        return $this->runRepository->distancesFromOptimal($instanceGroup, $algorithm, $hyperparameters, $d, $instanceType, $groupByVerticesOnly)
            ->map(fn ($item) => $item instanceof Model
                ? $item->toArray()
                : (array) $item
            )
            ->groupBy(['vertices', 'edges'])
            ->map(function (Collection $verticeItem, int $verticeGroup)
            {
                return $verticeItem->map(function (Collection $edgeItem, string $edgeGroup) use ($verticeGroup)
                {
                    $values = $edgeItem->reduce(function (array $carry, array $item) use ($edgeGroup, $verticeGroup)
                    {
                        $diff = strval($item['diff']);
                        $quantity = $item['quantity'];

                        return array_merge($carry, [
                            $diff => $quantity,
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
            ->map(function (array $item)
            {
                return collect($item)
                    ->mapWithKeys(function (int|float $value, string $key)
                    {
                        $key = is_numeric($key)
                            ? intval($key)
                            : $key;

                        return [$key => $value];
                    });
            })
            ->toArray();
    }

    public function valuesFromAlgorithms(
        InstanceGroup $instanceGroup,
        array $algorithms,
        int $d = 2,
        ?InstanceType $instanceType = null,
        ?ValuesFromAlgorithmsMode $valuesFromAlgorithmsMode = null,
    ): array
    {
        $valuesFromAlgorithmsMode ??= ValuesFromAlgorithmsMode::VALUE;

        return $this->runRepository->valuesFromAlgorithms($instanceGroup, $algorithms, $d, $instanceType, $valuesFromAlgorithmsMode)
            ->groupBy('instance')
            ->map(function (Collection $item, string $instance) use ($valuesFromAlgorithmsMode)
            {
                return $item ->reduce(function (array $carry, Run $item) use ($instance, $valuesFromAlgorithmsMode)
                {
                    /** @var Algorithm $algorithm */
                    $algorithm = $item['algorithm'];

                    $field = $valuesFromAlgorithmsMode->field();

                    return array_merge($carry, [
                        'vertices' => $item['vertices'],
                        'edges' => $item['edges'],
                        'instance' => $instance,
                        $algorithm->value => $valuesFromAlgorithmsMode->convertFieldTypeCallback($item[$field]),
                    ]);
                }, []);
            })
            ->values()
            ->toArray();
    }
}
