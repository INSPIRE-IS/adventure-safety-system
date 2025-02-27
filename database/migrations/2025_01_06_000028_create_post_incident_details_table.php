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
        Schema::create('post_incident_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('answer');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('post_incident_id')->nullable()->constrained('post_incidents');
            $table->foreignId('question_id')->nullable()->constrained('type_questions');

            $table->foreignId('user_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_incident_details');
    }
};
