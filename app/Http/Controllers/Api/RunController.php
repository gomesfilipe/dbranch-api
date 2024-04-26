<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunStoreRequest;
use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RunController extends Controller
{
    public function __construct(
        private readonly RunRepositoryInterface $runRepository,
    ) {
        //
    }
    public function store(RunStoreRequest $request): Response
    {
        $attributes = $request->validated();
        $this->runRepository->create($attributes);

        return response()->noContent();
    }
}
