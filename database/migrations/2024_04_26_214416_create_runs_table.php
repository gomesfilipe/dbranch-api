<?php

use App\Utils\RunResultsParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;
use App\Repositories\Interfaces\RunRepositoryInterface;

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
        $parsers = [
            'results/anderson_bep_results.txt' => fn (string $filename) => RunResultsParser::parseAndersonResults($filename, Algorithm::BEP_ANDERSON),
            'results/anderson_r_bep_results.txt' => fn (string $filename) => RunResultsParser::parseAndersonResults($filename, Algorithm::R_BEP_ANDERSON),
            'results/filipe_bep_results.json' => fn (string $filename) => RunResultsParser::parseJsonResults($filename),
            'results/filipe_pr_bep_results.json' => fn (string $filename) => RunResultsParser::parseJsonResults($filename),
        ];

        $runRepository = app()->make(RunRepositoryInterface::class);

        foreach ($parsers as $filename => $parser) {
            $results = $parser($filename);

            $runRepository->createMany($results);
        }
    }
};
