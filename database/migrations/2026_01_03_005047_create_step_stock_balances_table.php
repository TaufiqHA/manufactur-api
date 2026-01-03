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
        Schema::create('step_stock_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('project_items');

            $table->foreignId('sub_assembly_id')
                ->nullable()
                ->constrained('sub_assemblies');

            $table->foreignId('process_step_id')
                ->constrained('item_step_configs');

            $table->integer('total_produced')->default(0);
            $table->integer('total_consumed')->default(0);
            $table->integer('available_qty')->default(0);

            $table->unique([
                'item_id',
                'sub_assembly_id',
                'process_step_id'
            ], 'step_stock_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('step_stock_balances');
    }
};
