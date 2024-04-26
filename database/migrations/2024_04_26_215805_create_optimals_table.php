<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('optimals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vertices');
            $table->unsignedInteger('edges');
            $table->enum('algorithm', Algorithm::values());
            $table->decimal('min');
            $table->decimal('mean');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimals');
    }
};
