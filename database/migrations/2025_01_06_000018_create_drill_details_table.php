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
        Schema::create('drill_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('complete_date');
            $table->string('competent');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('signature_file_id')->constrained('files')->onDelete('cascade');
            $table->foreignId('drill_id')->constrained('drills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drill_details');
    }
};
