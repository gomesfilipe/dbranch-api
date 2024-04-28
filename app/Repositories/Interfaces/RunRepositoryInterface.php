<?php

namespace App\Repositories\Interfaces;

use App\Enums\InstanceType;
use App\Models\Run;
use Illuminate\Support\Collection;

interface RunRepositoryInterface
{
    public function create(array $attributes): Run;

    public function createMany(array $data): bool;

    public function minResults(InstanceType $instanceType): Collection;
}
