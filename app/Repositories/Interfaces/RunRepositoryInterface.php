<?php

namespace App\Repositories\Interfaces;

use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Models\Run;
use Illuminate\Support\Collection;

interface RunRepositoryInterface
{
    public function create(array $attributes): Run;

    public function createMany(array $data): bool;

    public function results(InstanceType $instanceType, Metric $metric): Collection;
}
