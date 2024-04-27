<?php

namespace App\Repositories\Interfaces;

use App\Models\Run;

interface RunRepositoryInterface
{
    public function create(array $attributes): Run;

    public function createMany(array $data): bool;
}
