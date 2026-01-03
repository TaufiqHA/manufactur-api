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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('project_items');

            $table->foreignId('sub_assembly_id')
                ->nullable()
                ->constrained('sub_assemblies');

            $table->foreignId('source_step_id')
                ->nullable()
                ->constrained('item_step_configs');

            $table->foreignId('target_step_id')
                ->constrained('item_step_configs');

            $table->foreignId('task_id')
                ->nullable()
                ->constrained('tasks');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users');

            $table->integer('quantity');
            $table->integer('good_qty');
            $table->integer('defect_qty');

            $table->enum('movement_type', [
                'PRODUCTION',
                'CONSUMPTION',
                'ADJUSTMENT'
            ]);

            $table->enum('shift', [
                'SHIFT_1',
                'SHIFT_2',
                'SHIFT_3'
            ])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
