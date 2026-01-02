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
        Schema::create('po_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('po_id')->constrained('purchase_orders')->onDelete('cascade'); // jika PO dihapus, item juga terhapus

            $table->foreignId('material_id')->constrained('materials')->onDelete('restrict'); // material tidak boleh terhapus selama digunakan

            $table->string('name');
            $table->unsignedInteger('qty');
            $table->decimal('price', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_items');
    }
};
