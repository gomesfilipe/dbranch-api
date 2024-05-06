<?php

namespace App\Repositories\Interfaces;

use App\Enums\Algorithm;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Models\Run;
use Illuminate\Support\Collection;

interface RunRepositoryInterface
{
    public function create(array $attributes): Run;

    public function createMany(array $data): bool;

    public function results(InstanceType $instanceType, Metric $metric): Collection;

    public function compareDiffs(Algorithm $algorithmA, Algorithm $algorithmB): Collection;

    public function compareValues(Algorithm $algorithmA, Algorithm $algorithmB): Collection;
}
