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
        if (Schema::hasTable('help_answers')) {
            return;
        }

        Schema::create('help_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('help_question_id')
                ->unique()
                ->constrained('help_questions')
                ->cascadeOnDelete();

            $table->longText('answer_md')->nullable();

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
        Schema::dropIfExists('help_answers');
        Schema::enableForeignKeyConstraints();
    }
};
