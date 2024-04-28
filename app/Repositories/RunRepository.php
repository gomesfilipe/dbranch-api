<?php

namespace App\Repositories;

use App\Enums\InstanceType;
use App\Models\Optimal;
use App\Models\Run;
use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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

    public function minResults(InstanceType $instanceType): Collection
    {
        $groupBy = $instanceType->groupBy();
        $operator = $instanceType->operator();
        $delimiter = 600;

        return Run::query()
            ->select([
                'vertices',
                'algorithm',
                DB::raw('ROUND(AVG(edges), 2)::numeric as edges'),
                DB::raw('ROUND(AVG(min_value), 2)::numeric as value'),
            ])
            ->from(
                Run::query()
                    ->select([
                        'instance',
                        'vertices',
                        'edges',
                        'algorithm',
                        DB::raw('MIN(value) as min_value'),
                    ])
                    ->where('vertices', $operator, $delimiter)
                    ->groupBy([
                        'instance',
                        'vertices',
                        'edges',
                        'algorithm',
                    ]),
                'tbl',
            )
            ->groupBy($groupBy)
            ->union(
                Optimal::query()
                    ->select([
                        'vertices',
                        'algorithm',
                        'edges',
                        'min',
                    ])
                    ->where('vertices', $operator, $delimiter)
            )
            ->orderBy('vertices')
            ->orderBy('edges')
            ->get();
    }
}
