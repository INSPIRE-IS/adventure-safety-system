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
        if (Schema::hasTable('help_questions')) {
            return;
        }

        Schema::create('help_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');

            $table->foreignId('help_category_id')
                ->constrained('help_categories')
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->boolean('is_internal')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['help_category_id', 'team_id', 'is_internal'], 'help_questions_scope_index');
            $table->index(['team_id'], 'help_questions_team_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('help_questions');
        Schema::enableForeignKeyConstraints();
    }
};
