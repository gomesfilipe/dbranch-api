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

    public function createMany(array $data): bool
    {
        foreach ($data as &$run) {
            $run['created_at'] = $run['updated_at'] = now()->toDateTimeString();
        }

        return Run::query()
            ->insert($data);
    }

}
