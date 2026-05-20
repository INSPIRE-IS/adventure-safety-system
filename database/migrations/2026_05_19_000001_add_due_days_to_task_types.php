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
        Schema::table('type_inspections', function (Blueprint $table) {
            $table->unsignedSmallInteger('due_days')->default(14);
        });

        Schema::table('type_drills', function (Blueprint $table) {
            $table->unsignedSmallInteger('due_days')->default(14);
        });

        Schema::table('type_incidents', function (Blueprint $table) {
            $table->unsignedSmallInteger('due_days')->default(14);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_incidents', function (Blueprint $table) {
            $table->dropColumn('due_days');
        });

        Schema::table('type_drills', function (Blueprint $table) {
            $table->dropColumn('due_days');
        });

        Schema::table('type_inspections', function (Blueprint $table) {
            $table->dropColumn('due_days');
        });
    }
};

