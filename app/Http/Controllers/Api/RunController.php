<?php

namespace App\Http\Controllers\Api;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Http\Controllers\Controller;
use App\Http\Requests\RunAccuracyRequest;
use App\Http\Requests\RunCompareRequest;
use App\Http\Requests\RunGapResultsRequest;
use App\Http\Requests\RunResultsRequest;
use App\Http\Requests\RunStoreRequest;
use App\Repositories\Interfaces\RunRepositoryInterface;
use App\Services\RunService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RunController extends Controller
{
    public function __construct(
        private readonly RunRepositoryInterface $runRepository,
        private readonly RunService $runService,
    ) {
        //
    }

    public function store(RunStoreRequest $request): Response
    {
        $data = $request->validated();
        $this->runService->createManyAsync($data);

        return response()->noContent();
    }

    public function results(RunResultsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $instanceType = InstanceType::from($data['instance_type']);
        $metric = Metric::from($data['metric']);
        $instanceGroup = InstanceGroup::from($data['instance_group']);
        $includeTime = boolval($data['include_time'] ?? true);

        unset($data['instance_type'], $data['metric'], $data['instance_group'], $data['include_time']);

        return response()->json(
            $this->runService->results($instanceType, $metric, $instanceGroup, $data, $includeTime)
        );
    }

    public function gapResults(RunGapResultsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $instanceType = InstanceType::from($data['instance_type']);
        $instanceGroup = InstanceGroup::from($data['instance_group']);

        unset($data['instance_type'], $data['instance_group']);

        return response()->json(
            $this->runService->gapResults($instanceType, $instanceGroup, $data)
        );
    }

    public function compareDiffs(RunCompareRequest $request): JsonResponse
    {
        $data = $request->validated();

        $algorithmA = Algorithm::from($data['algorithm_a']);
        $algorithmB = Algorithm::from($data['algorithm_b']);

        unset($data['algorithm_a'], $data['algorithm_b']);

        return response()->json(
            $this->runService->compareDiffs($algorithmA, $algorithmB, $data)
        );
    }

    public function compareValues(RunCompareRequest $request): JsonResponse
    {
        $data = $request->validated();

        $algorithmA = Algorithm::from($data['algorithm_a']);
        $algorithmB = Algorithm::from($data['algorithm_b']);

        unset($data['algorithm_a'], $data['algorithm_b']);

        return response()->json(
            $this->runRepository->compareValues($algorithmA, $algorithmB, $data)
        );
    }

    public function verticesClassificationAccuracy(RunAccuracyRequest $request): JsonResponse
    {
        $data = $request->validated();

        $instanceType = InstanceType::from($data['instance_type']);
        $instanceGroup = InstanceGroup::from($data['instance_group']);

        unset($data['instance_type'], $data['instance_group']);

        return response()->json(
            $this->runService->verticesClassificationAccuracy($instanceType, $instanceGroup, $data)
        );
    }
}
