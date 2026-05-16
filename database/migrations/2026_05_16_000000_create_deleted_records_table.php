<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_records', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->unsignedBigInteger('detail_id')->nullable();
            $table->unsignedInteger('file_count')->default(0);
            $table->timestamp('device_deleted_at')->nullable();
            $table->json('payload');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->timestamps();

            $table->index(['site_id', 'user_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_records');
    }
};