<?php

namespace App\Http\Controllers\Api;

use App\Enums\InstanceType;
use App\Enums\Metric;
use App\Http\Controllers\Controller;
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

        return response()->json(
            $this->runService->results($instanceType, $metric)
        );
    }

    public function gapResults(RunGapResultsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $instanceType = InstanceType::from($data['instance_type']);

        return response()->json(
            $this->runService->gapResults($instanceType)
        );
    }
}
