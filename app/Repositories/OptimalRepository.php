<?php

namespace App\Repositories;

use App\Models\Optimal;
use App\Repositories\Interfaces\OptimalRepositoryInterface;

class OptimalRepository implements OptimalRepositoryInterface
{
    public function createMany(array $data): bool
    {
        foreach ($data as &$run) {
            $run['created_at'] = $run['updated_at'] = now()->toDateTimeString();
        }

        return Optimal::query()
            ->insert($data);
    }
}
