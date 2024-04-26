<?php

namespace App\Repositories;

use App\Models\Run;
use App\Repositories\Interfaces\RunRepositoryInterface;

class RunRepository implements RunRepositoryInterface
{
    public function create(array $attributes): Run
    {
        /** @var Run */
        return Run::query()
            ->create($attributes);
    }
}
