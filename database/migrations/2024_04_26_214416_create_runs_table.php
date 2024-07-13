<?php

use App\Enums\AlgorithmMode;
use App\Utils\RunResultsParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;
use App\Enums\Centrality;
use App\Services\RunService;
use App\Enums\InstanceGroup;
use App\Enums\AlgorithmType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vertices');
            $table->unsignedInteger('edges');
            $table->enum('instance_group', InstanceGroup::values());
            $table->string('instance');
            $table->decimal('value', unsigned: true);
            $table->enum('algorithm', Algorithm::values());
            $table->enum('algorithm_type', AlgorithmType::values());
            $table->enum('algorithm_mode', AlgorithmMode::values());
            $table->enum('constructive_algorithm', array_column(Algorithm::constructiveAlgorithms(), 'value'))->nullable();
            $table->decimal('initial_value')->nullable();
            $table->unsignedInteger('iterations')->nullable();
            $table->decimal('time', 10, 6)->nullable();
            $table->enum('centrality', Centrality::values())->nullable();
            $table->jsonb('branch_vertices')->nullable();
            $table->unsignedInteger('d')->default(2);
            $table->timestamps();
        });

        $this->seedResults(useSmallestRandom: true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runs');
    }

    private function seedResults(bool $useSmallestRandom = true): void
    {
        $runService = app()->make(RunService::class);

        foreach (InstanceGroup::cases() as $instanceGroup) {
            $filenames = $instanceGroup->resultsFiles($useSmallestRandom);

            foreach ($filenames as $filename) {
                $results = RunResultsParser::parseJsonResults($filename);
                $runService->createManyAsync($results);
            }
        }
    }
};
