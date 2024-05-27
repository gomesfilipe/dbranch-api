<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solution_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained('runs');
            $table->unsignedInteger('vertice');
            $table->unsignedInteger('degree');
            $table->decimal('centrality_value', 10, 6);
            $table->boolean('is_branch');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solution_nodes');
    }
};
