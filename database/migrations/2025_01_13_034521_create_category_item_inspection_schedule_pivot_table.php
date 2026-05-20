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
        Schema::create('category_item_inspection_schedule', function (Blueprint $table) {
            $table->foreignId('inspection_schedule_id')->constrained('inspection_schedules')->onDelete('cascade');
            $table->foreignId('category_item_id')->constrained('category_items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_item_inspection_schedule');
    }
};
