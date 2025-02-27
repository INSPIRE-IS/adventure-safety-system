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
        Schema::create('post_incident_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->date('active_date');
            $table->string('period_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('type_incident_id')->nullable()->constrained('type_incidents');
            $table->foreignId('incident_id')->nullable()->constrained('incidents');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->foreignId('assigned_by_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_incident_schedules');
    }
};
