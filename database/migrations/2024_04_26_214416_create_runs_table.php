<?php

use App\Utils\RunResultsParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;
use App\Enums\Centrality;
use App\Services\RunService;
use App\Enums\InstanceGroup;

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
            $table->decimal('time', 10, 6)->nullable();
            $table->enum('centrality', Centrality::values())->nullable();
            $table->jsonb('branch_vertices')->nullable();
            $table->unsignedInteger('d')->default(2);
            $table->timestamps();
        });

        $this->seedResults();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runs');
    }

    private function seedResults(): void
    {
        $parseJsonResultsCallback = fn (string $filename) => RunResultsParser::parseJsonResults($filename);
        $path = 'results/spd_rf2';

        $parsers = [
            'anderson_BEP_results.txt' => fn (string $filename) => RunResultsParser::parseAndersonResults($filename, Algorithm::BEP_ANDERSON),
            'anderson_R_BEP_smallest_results.txt' => fn (string $filename) => RunResultsParser::parseAndersonResults($filename, Algorithm::R_BEP_ANDERSON),
            'filipe_BEP_results.json' => $parseJsonResultsCallback,
            'filipe_PR_BEP_results.json' => $parseJsonResultsCallback,
            'filipe_R_BEP_smallest_results.json' => $parseJsonResultsCallback,
            'filipe_PR_R_BEP_smallest_results.json' => $parseJsonResultsCallback,
            'exact_results.json' => $parseJsonResultsCallback,
        ];

        $runService = app()->make(RunService::class);

        foreach ($parsers as $filename => $parser) {
            $results = $parser(path_join($path, $filename));
            $runService->createManyAsync($results);
        }
    }
};
