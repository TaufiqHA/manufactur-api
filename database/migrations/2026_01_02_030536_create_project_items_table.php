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
        Schema::create('project_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            $table->string('name');
            $table->string('dimensions')->nullable();
            $table->string('thickness')->nullable();

            $table->unsignedInteger('qty_set');
            $table->unsignedInteger('quantity');
            $table->string('unit');

            $table->boolean('is_bom_locked');
            $table->boolean('is_workflow_locked');

            $table->enum('flow_type', ['OLD', 'NEW']);

            $table->unsignedInteger('warehouse_qty')->default(0);
            $table->unsignedInteger('shipped_qty')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_items');
    }
};
