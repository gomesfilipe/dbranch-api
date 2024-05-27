<?php

use App\Utils\RunResultsParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;
use App\Enums\Centrality;
use App\Services\RunService;

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
            $table->string('instance');
            $table->decimal('value', unsigned: true);
            $table->enum('algorithm', Algorithm::values());
            $table->decimal('time', 10, 6)->nullable();
            $table->enum('centrality', Centrality::values())->nullable();
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

        $parsers = [
            'results/anderson_bep_results.txt' => fn (string $filename) => RunResultsParser::parseAndersonResults($filename, Algorithm::BEP_ANDERSON),
            'results/anderson_r_bep_results.txt' => fn (string $filename) => RunResultsParser::parseAndersonResults($filename, Algorithm::R_BEP_ANDERSON),
            'results/filipe_bep_results.json' => $parseJsonResultsCallback,
            'results/filipe_pr_bep_results.json' => $parseJsonResultsCallback,
            'results/filipe_r_bep_results.json' => $parseJsonResultsCallback,
            'results/filipe_pr_r_bep_results.json' => $parseJsonResultsCallback,
        ];

        $runService = app()->make(RunService::class);

        foreach ($parsers as $filename => $parser) {
            $results = $parser($filename);
            $runService->createManyAsync($results);
        }
    }
};
