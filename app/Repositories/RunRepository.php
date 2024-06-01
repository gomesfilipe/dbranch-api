<?php

namespace App\Repositories;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Models\Optimal;
use App\Models\Run;
use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RunRepository implements RunRepositoryInterface
{
    public function create(array $attributes): Run
    {
        $run = $this->fillDefaultValues($attributes);

        /** @var Run */
        return Run::query()
            ->create($run);
    }

    public function createMany(array $data): bool
    {
        foreach ($data as &$run) {
            $run = $this->fillDefaultValues($run);
        }

        return Run::query()
            ->insert($data);
    }

    private function fillDefaultValues(array $runAttributes): array
    {
        $algorithm = $runAttributes['algorithm'];

        $algorithm = is_string($runAttributes['algorithm'])
            ? Algorithm::from($algorithm)
            : $algorithm;

        $defaultValues = [
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
            'centrality' => $algorithm->centrality(),
        ];

        return array_merge($runAttributes, $defaultValues);
    }

    public function results(InstanceType $instanceType, Metric $metric, InstanceGroup $instanceGroup, array $params = []): Collection
    {
        $algorithms = $params['algorithms'] ?? null;

        $groupBy = $instanceGroup->groupBy($instanceType);
        $operator = $instanceType->operator();
        $delimiter = InstanceType::delimiter();

        $optimalColumns = $metric->optimalColumns();
        $sqlMetric = $metric->sqlMetric();

        return DB::query()
            ->from(
                Run::query()
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
                            ->whereNotIn('algorithm', Algorithm::disregardRuns())
                            ->where('instance_group', '=', $instanceGroup)
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
                            ->where('instance_group', '=', $instanceGroup)
                    )
                    ->orderBy('vertices')
                    ->orderBy('edges'),
                'tbl2'
            )
            ->when(! is_null($algorithms), fn (Builder $query) => $query
                ->whereIn('algorithm', $algorithms)
            )
            ->get();
    }

    public function compareDiffs(Algorithm $algorithmA, Algorithm $algorithmB, array $params = []): Collection
    {
        $instanceGroup = $params['instance_group'] ?? null;

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
                ->on('s.instance_group', '=', 't.instance_group')
            )
            ->where('s.algorithm', '=', $algorithmA)
            ->where('t.algorithm', '=', $algorithmB)
            ->when(! is_null($instanceGroup), fn (Builder $query) => $query
                ->where('s.instance_group', '=', $instanceGroup)
            )
            ->groupByRaw('ABS(s.value - t.value)')
            ->orderBy('diff')
            ->get();
    }

    public function compareValues(Algorithm $algorithmA, Algorithm $algorithmB, array $params = []): Collection
    {
        $instanceGroup = $params['instance_group'] ?? null;

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
                    ->on('s.instance_group', '=', 't.instance_group')
                )
                ->where('s.algorithm', '=', $algorithmA)
                ->where('t.algorithm', '=', $algorithmB)
                ->when(! is_null($instanceGroup), fn (Builder $query) => $query
                    ->where('s.instance_group', '=', $instanceGroup)
                )
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

    public function verticesClassificationAccuracy(InstanceType $instanceType, InstanceGroup $instanceGroup, array $params = []): Collection
    {
        $algorithms = $params['algorithms'] ?? null;

        $groupBy = $instanceGroup->groupBy($instanceType);

        $operator = $instanceType->operator();
        $delimiter = InstanceType::delimiter();

        return DB::query()
            ->select([
                'vertices',
                'algorithm',
                DB::raw('ROUND(AVG(edges), 2)::numeric as edges'),
                DB::raw('ROUND(AVG(accuracy), 4) as accuracy_avg'),
            ])
            ->from(
                DB::table('runs as s')
                    ->select([
                        't.vertices',
                        't.edges',
                        't.algorithm',
                        DB::raw('
                            (
                                select round(1 - count(*) / s.vertices::decimal, 4)
                                from (
                                    (
                                        (select * from jsonb_array_elements(s.branch_vertices) as s_branch_vertices)
                                        UNION
                                        (select * from jsonb_array_elements(t.branch_vertices) as t_branch_vertices)
                                    )
                                    EXCEPT
                                    (
                                        (select * from jsonb_array_elements(s.branch_vertices) as s_branch_vertices)
                                        INTERSECT
                                        (select * from jsonb_array_elements(t.branch_vertices) as t_branch_vertices)
                                    )
                                ) as not_in_insersection_branch_vertices
                            ) as accuracy
                        '),
                    ])
                    ->join('runs as t', fn (JoinClause $join) => $join
                        ->on('s.instance', '=', 't.instance')
                        ->on('s.instance_group', '=', 't.instance_group')
                    )
                    ->whereNotNull([
                        's.branch_vertices',
                        't.branch_vertices',
                    ])
                    ->whereColumn('s.algorithm', '<>', 't.algorithm')
                    ->whereColumn('s.d', '=', 't.d')
                    ->where('s.instance_group', '=', $instanceGroup)
                    ->where('s.algorithm', '=', Algorithm::EXACT)
                    ->where('s.vertices', $operator, $delimiter)
                ,
                'tbl',
            )
            ->when(! is_null($algorithms), fn (Builder $query) => $query
                ->whereIn('algorithm', $algorithms)
            )
            ->groupBy($groupBy)
            ->orderBy('vertices')
            ->orderBy('edges')
            ->get();
    }
}

