<?php

use App\Enums\InstanceGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Algorithm;
use App\Repositories\Interfaces\OptimalRepositoryInterface;
use App\Models\Optimal;

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
            $table->decimal('edges', unsigned: true);
            $table->enum('instance_group', InstanceGroup::values());
            $table->enum('algorithm', Algorithm::values());
            $table->decimal('min', unsigned: true);
            $table->decimal('mean', unsigned: true);
            $table->timestamps();
        });

        $data = Optimal::optimalResults();

        app()->make(OptimalRepositoryInterface::class)
            ->createMany($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimals');
    }
};
