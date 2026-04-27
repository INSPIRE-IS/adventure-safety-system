<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaints')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `complaints` MODIFY COLUMN `description` LONGTEXT NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE complaints ALTER COLUMN description TYPE TEXT');
            return;
        }

        if ($driver === 'sqlite') {
            // SQLite does not enforce varchar length; leave column as-is.
            return;
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->longText('description')->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('complaints')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `complaints` MODIFY COLUMN `description` VARCHAR(255) NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE complaints ALTER COLUMN description TYPE VARCHAR(255)');
            return;
        }

        if ($driver === 'sqlite') {
            return;
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->string('description', 255)->change();
        });
    }
};

