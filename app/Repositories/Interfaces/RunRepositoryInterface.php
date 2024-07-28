<?php

namespace App\Repositories\Interfaces;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Models\Run;
use Illuminate\Support\Collection;

interface RunRepositoryInterface
{
    public function create(array $attributes): Run;

    public function createMany(array $data): bool;

    public function results(InstanceType $instanceType, Metric $metric, InstanceGroup $instanceGroup, array $params = []): Collection;

    public function compareDiffs(Algorithm $algorithmA, Algorithm $algorithmB, array $params = []): Collection;

    public function compareValues(Algorithm $algorithmA, Algorithm $algorithmB, array $params = []): Collection;

    public function verticesClassificationAccuracy(InstanceType $instanceType, InstanceGroup $instanceGroup, array $params = []): Collection;

    public function getMinimumResultIdsByInstance(): array;

    public function distancesFromOptimal(InstanceGroup $instanceGroup, Algorithm $algorithm, array $hyperparameters, int $d = 2, ?InstanceType $instanceType = null, bool $groupByVerticesOnly = false): Collection;

    public function valuesFromAlgorithms(InstanceGroup $instanceGroup, array $algorithms, int $d = 2, ?InstanceType $instanceType = null): Collection;
}
