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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('unit');
            $table->unsignedInteger('current_stock');
            $table->unsignedInteger('safety_stock');
            $table->decimal('price_per_unit', 15, 2);
            $table->enum('category', ['RAW', 'FINISHING', 'HARDWARE']);
            $table->timestamps(); // created_at & updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
