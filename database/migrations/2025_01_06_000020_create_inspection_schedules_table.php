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
        Schema::create('inspection_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->date('active_date');
            $table->string('period_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('type_activity_id')->nullable()->constrained('type_activities');
            $table->foreignId('type_inspection_id')->nullable()->constrained('type_inspections');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->foreignId('assigned_by_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_schedules');
    }
};
