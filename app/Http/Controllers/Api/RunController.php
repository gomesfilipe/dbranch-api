<?php

namespace App\Http\Controllers\Api;

use App\Enums\Algorithm;
use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Http\Controllers\Controller;
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
        $this->runRepository->createMany($data);

        return response()->noContent();
    }

    public function results(RunResultsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $instanceType = InstanceType::from($data['instance_type']);
        $metric = Metric::from($data['metric']);

        unset($data['instance_type'], $data['metric']);

        return response()->json(
            $this->runService->results($instanceType, $metric, $data)
        );
    }

    public function gapResults(RunGapResultsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $instanceType = InstanceType::from($data['instance_type']);

        unset($data['instance_type']);

        return response()->json(
            $this->runService->gapResults($instanceType, $data)
        );
    }

    public function compareDiffs(RunCompareRequest $request): JsonResponse
    {
        $data = $request->validated();

        $algorithmA = Algorithm::from($data['algorithm_a']);
        $algorithmB = Algorithm::from($data['algorithm_b']);

        return response()->json(
            $this->runService->compareDiffs($algorithmA, $algorithmB)
        );
    }

    public function compareValues(RunCompareRequest $request): JsonResponse
    {
        $data = $request->validated();

        $algorithmA = Algorithm::from($data['algorithm_a']);
        $algorithmB = Algorithm::from($data['algorithm_b']);

        return response()->json(
            $this->runRepository->compareValues($algorithmA, $algorithmB)
        );
    }
}
