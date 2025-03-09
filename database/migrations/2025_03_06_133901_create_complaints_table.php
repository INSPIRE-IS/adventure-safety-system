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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('title');
            $table->string('description');
            $table->string('complaint_date');
            $table->string('uuid')->nullable();
            $table->integer('resolved')->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('site_id')->constrained('teams');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('complaints');
        Schema::enableForeignKeyConstraints();
    }
};
