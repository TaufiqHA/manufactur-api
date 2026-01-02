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
        Schema::create('item_step_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('project_items')->onDelete('cascade');

            $table->string('step'); // ProcessStep
            $table->unsignedInteger('sequence');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_step_configs');
    }
};
