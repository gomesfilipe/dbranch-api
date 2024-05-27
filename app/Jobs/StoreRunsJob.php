<?php

namespace App\Jobs;

use App\Repositories\Interfaces\RunRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreRunsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $data)
    {
        //
    }

    /**
     * Execute the job.
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        app()->make(RunRepositoryInterface::class)->createMany($this->data);
    }
}
