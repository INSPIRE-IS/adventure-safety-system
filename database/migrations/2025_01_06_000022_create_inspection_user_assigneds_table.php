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
        Schema::create('inspection_user_assigneds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('due_by_date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('inspection_id')->nullable()->constrained('inspections');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->foreignId('photo_id')->nullable()->constrained('files');
            $table->foreignId('signature_id')->nullable()->constrained('files');
            $table->foreignId('report_file_id')->nullable()->constrained('files');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_user_assigneds');
    }
};
