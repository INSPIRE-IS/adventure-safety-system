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
        Schema::create('post_incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('due_date')->nullable();
            $table->date('complete_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('post_incident_schedule_id')->nullable()->constrained('post_incident_schedules');
            $table->foreignId('type_incident_id')->nullable()->constrained('type_incidents');
            $table->foreignId('report_file_id')->nullable()->constrained('files');
            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_incidents');
    }
};
