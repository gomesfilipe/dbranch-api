<?php

use App\Utils\RunResultsParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;
use App\Repositories\Interfaces\RunRepositoryInterface;

return new class extends Migration
{
    private array $results = [
        'results/anderson_bep_results.txt' =>  Algorithm::BEP_ANDERSON
    ];

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
            $table->decimal('time', 10, 6);
            $table->timestamps();
        });

        $runRepository = app()->make(RunRepositoryInterface::class);

        foreach ($this->results as $filename => $algorithm) {
            $result = RunResultsParser::parseAndersonResults($filename, $algorithm);

            $runRepository->createMany($result);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runs');
    }
};
