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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('project_items')->onDelete('cascade'); // jika project item dihapus, BOM ikut terhapus

            $table->foreignId('material_id')->constrained('materials')->onDelete('restrict');

            $table->unsignedInteger('quantity_per_unit');
            $table->unsignedInteger('total_required');

            $table->unsignedInteger('allocated')->default(0);
            $table->unsignedInteger('realized')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
