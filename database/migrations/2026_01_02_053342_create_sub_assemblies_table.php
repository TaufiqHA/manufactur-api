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
        Schema::create('sub_assemblies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('project_items')->onDelete('cascade');

            $table->string('name');

            $table->unsignedInteger('qty_per_parent');
            $table->unsignedInteger('total_needed');

            $table->unsignedInteger('completed_qty')->default(0);
            $table->unsignedInteger('total_produced')->default(0);
            $table->unsignedInteger('consumed_qty')->default(0);

            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('restrict');

            // jsonb di PostgreSQL → gunakan json (Laravel akan mapping otomatis)
            $table->json('processes');
            $table->json('step_stats')->nullable();

            $table->boolean('is_locked');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_assemblies');
    }
};
