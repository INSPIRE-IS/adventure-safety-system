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
        Schema::create('drills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('due_date')->nullable();
            $table->date('complete_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('drill_schedule_id')->nullable()->constrained('drill_schedules');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->foreignId('photo_file_id')->nullable()->constrained('files');
            $table->foreignId('signature_file_id')->nullable()->constrained('files');
            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('type_activity_id')->nullable()->constrained('type_activities');
            $table->foreignId('type_drill_id')->nullable()->constrained('type_drills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drills');
    }
};
