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
        Schema::create('category_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('in_use_date')->nullable();
            $table->string('out_use_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('category_item_id')->nullable()->constrained('category_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_details');
    }
};
