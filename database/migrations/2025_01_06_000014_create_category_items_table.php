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
        Schema::create('category_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('type_inspection_id')->nullable()->constrained('type_inspections');
            $table->foreignId('certificate_recieved_id')->nullable()->constrained('type_bools');
            $table->foreignId('certificate_file_id')->nullable()->constrained('files');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_items');
    }
};
