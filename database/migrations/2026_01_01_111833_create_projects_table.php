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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('customer');
            $table->date('start_date');
            $table->date('deadline');
            $table->enum('status', ['PLANNED', 'IN_PROGRESS', 'COMPLETED', 'ON_HOLD']);
            $table->unsignedInteger('progress');
            $table->unsignedInteger('qty_per_unit');
            $table->unsignedInteger('procurement_qty');
            $table->unsignedInteger('total_qty');
            $table->string('unit');
            $table->boolean('is_locked');
            $table->timestamps(); // sudah termasuk created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
