<?php

namespace App\Repositories;

use App\Enums\Algorithm;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Models\Optimal;
use App\Models\Run;
use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
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

    public function results(InstanceType $instanceType, Metric $metric, array $params = []): Collection
    {
        $algorithms = $params['algorithms'] ?? null;

        $groupBy = $instanceType->groupBy();
        $operator = $instanceType->operator();
        $delimiter = InstanceType::delimiter();

        $optimalColumns = $metric->optimalColumns();
        $sqlMetric = $metric->sqlMetric();

        return Run::query()
            ->select([
                'vertices',
                'algorithm',
                DB::raw('ROUND(AVG(edges), 2)::numeric as edges'),
                DB::raw('ROUND(AVG(value), 2)::numeric as value'),
            ])
            ->from(
                Run::query()
                    ->select([
                        'instance',
                        'vertices',
                        'edges',
                        'algorithm',
                        DB::raw("$sqlMetric(value) as value"),
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
                    ->select($optimalColumns)
                    ->where('vertices', $operator, $delimiter)
            )
            ->when(! is_null($algorithms), fn (Builder $query) => $query
                ->whereIn('algorithm', $algorithms)
            )
            ->orderBy('vertices')
            ->orderBy('edges')
            ->get();
    }

    public function compareDiffs(Algorithm $algorithmA, Algorithm $algorithmB): Collection
    {
        $delimiter = InstanceType::delimiter();

        return DB::table('runs as s')
            ->selectRaw("
                ABS(s.value - t.value) as diff,
                COUNT(*) as total,
                SUM (
                    CASE
                        WHEN s.vertices < $delimiter THEN 1
                        ELSE 0
                    END
                ) as medium,
                SUM (
                    CASE
                        WHEN s.vertices >= $delimiter THEN 1
                        ELSE 0
                    END
                ) as large
            ")
            ->join('runs as t', fn (JoinClause $join) => $join
                ->on('s.instance', '=', 't.instance')
            )
            ->where('s.algorithm', '=', $algorithmA)
            ->where('t.algorithm', '=', $algorithmB)
            ->groupByRaw('ABS(s.value - t.value)')
            ->orderBy('diff')
            ->get();
    }

    public function compareValues(Algorithm $algorithmA, Algorithm $algorithmB): Collection
    {
        $medium = InstanceType::MEDIUM->value;
        $large = InstanceType::LARGE->value;
        $delimiter = InstanceType::delimiter();
        $orderByRaw = InstanceType::orderByRaw();
        $instanceTypeField = InstanceType::field();

        return DB::query()
            ->from(
                DB::table('runs as s')
                    ->selectRaw("
                        CASE
                            WHEN s.vertices < $delimiter THEN '$medium'
                            ELSE '$large'
                        END as $instanceTypeField,
                        SUM (
                            CASE
                                WHEN t.value > s.value THEN 1
                                ELSE 0
                            END
                        ) as best,
                        SUM (
                            CASE
                                WHEN t.value = s.value THEN 1
                                ELSE 0
                            END
                        ) as equal,
                        SUM (
                            CASE
                                WHEN t.value < s.value THEN 1
                                ELSE 0
                            END
                        ) as worst
                ")
                ->join('runs as t', fn (JoinClause $join) => $join
                    ->on('s.instance', '=', 't.instance')
                )
                ->where('s.algorithm', '=', $algorithmA)
                ->where('t.algorithm', '=', $algorithmB)
                ->groupByRaw("
                    CASE
                        WHEN s.vertices < $delimiter THEN '$medium'
                        ELSE '$large'
                    END
                "),
                'tbl'
            )
            ->orderByRaw($orderByRaw)
            ->get();
    }
}

