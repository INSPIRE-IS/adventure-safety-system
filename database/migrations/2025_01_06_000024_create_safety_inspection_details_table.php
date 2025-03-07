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
        Schema::create('safety_inspection_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('condition')->nullable();
            $table->integer('resolved')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections');
            $table->foreignId('category_item_id')->nullable()->constrained('category_items');
            $table->foreignId('category_detail_id')->nullable()->constrained('category_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_inspection_details');
    }
};
