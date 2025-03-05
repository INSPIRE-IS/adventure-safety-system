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
        Schema::create('drill_member', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('member_name')->nullable();
            $table->string('competent');
            $table->string('uuid')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('site_id')->nullable()->constrained('teams');
            $table->foreignId('drill_id')->nullable()->constrained('drills');
            $table->foreignId('user_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drill_user');
    }
};
