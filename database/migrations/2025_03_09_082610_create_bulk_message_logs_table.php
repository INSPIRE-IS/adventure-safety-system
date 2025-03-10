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
        Schema::create('bulk_message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->enum('sent_status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamps();

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('bulk_message_id')->nullable()->constrained('bulk_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_message_logs');
    }
};
