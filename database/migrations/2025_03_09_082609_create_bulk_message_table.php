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
        Schema::create('bulk_message', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('user_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_message');
    }
};
