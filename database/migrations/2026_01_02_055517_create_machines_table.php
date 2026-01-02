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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('code');
            $table->string('name');
            $table->string('type'); // ProcessStep

            $table->unsignedInteger('capacity_per_hour');

            $table->enum('status', [
                'IDLE',
                'RUNNING',
                'MAINTENANCE',
                'OFFLINE',
                'DOWNTIME'
            ]);

            $table->boolean('is_maintenance');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
