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
        Schema::create('inspections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('due_date')->nullable();
            $table->datetime('complete_date')->nullable();
            $table->string('closing_comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('inspection_schedule_id')->nullable()->constrained('inspection_schedules');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->foreignId('photo_file_id')->nullable()->constrained('files');
            $table->foreignId('signature_file_id')->nullable()->constrained('files');
            $table->foreignId('site_id')->nullable()->constrained('teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
