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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Project
            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->string('project_name');

            // Item
            $table->foreignUuid('item_id')
                ->constrained('project_items')
                ->cascadeOnDelete();

            $table->string('item_name');

            // Sub Assembly (optional)
            $table->foreignUuid('sub_assembly_id')
                ->nullable()
                ->constrained('sub_assemblies')
                ->nullOnDelete();

            $table->string('sub_assembly_name')->nullable();

            // Process
            $table->string('step');

            // Machine (optional)
            $table->foreignUuid('machine_id')
                ->nullable()
                ->constrained('machines')
                ->nullOnDelete();

            // Quantities
            $table->unsignedInteger('target_qty');
            $table->unsignedInteger('daily_target')->nullable();
            $table->unsignedInteger('completed_qty')->default(0);
            $table->unsignedInteger('defect_qty')->default(0);

            // Status
            $table->enum('status', [
                'PENDING',
                'IN_PROGRESS',
                'PAUSED',
                'COMPLETED',
                'DOWNTIME'
            ]);

            $table->text('note')->nullable();
            $table->unsignedInteger('total_downtime_minutes')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
