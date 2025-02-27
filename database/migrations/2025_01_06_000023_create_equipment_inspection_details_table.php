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
        Schema::create('equipment_inspection_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('condition')->default(0);
            $table->string('identity')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections');
            $table->foreignId('category_item_id')->nullable()->constrained('category_items');
            $table->foreignId('category_detail_id')->nullable()->constrained('category_details');
            //$table->foreignId('quantity_id')->nullable()->constrained('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_inspection_details');
    }
};
