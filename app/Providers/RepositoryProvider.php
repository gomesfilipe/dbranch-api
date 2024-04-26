<?php

namespace App\Providers;

use App\Repositories\Interfaces\OptimalRepositoryInterface;
use App\Repositories\Interfaces\RunRepositoryInterface;
use App\Repositories\OptimalRepository;
use App\Repositories\RunRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        RunRepositoryInterface::class => RunRepository::class,
        OptimalRepositoryInterface::class => OptimalRepository::class,
    ];
}
