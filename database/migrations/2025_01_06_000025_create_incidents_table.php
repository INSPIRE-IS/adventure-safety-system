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
        Schema::create('incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('incident_date');
            $table->string('instructor_one_name')->nullable();
            $table->string('instructor_two_name')->nullable();
            $table->string('uuid')->nullable();
            $table->longText('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('type_incident_id')->nullable()->constrained('type_incidents');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('signature_file_id')->nullable()->constrained('files');
            $table->foreignId('incident_file_id')->nullable()->constrained('files');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
