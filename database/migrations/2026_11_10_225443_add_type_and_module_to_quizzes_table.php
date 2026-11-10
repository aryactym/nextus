<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN, so recreate the table
        Schema::create('quizzes_new', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('course_modules')->cascadeOnDelete();
            $table->string('type')->default('lesson'); // lesson, pre, post
            $table->unsignedTinyInteger('passing_score')->nullable();
            $table->timestamps();

            $table->unique(['module_id', 'type']);
        });

        // Copy existing data
        \Illuminate\Support\Facades\DB::statement('INSERT INTO quizzes_new (id, uuid, lesson_id, module_id, type, passing_score, created_at, updated_at) SELECT id, uuid, lesson_id, NULL, \'lesson\', passing_score, created_at, updated_at FROM quizzes');

        Schema::drop('quizzes');
        Schema::rename('quizzes_new', 'quizzes');
    }

    public function down(): void
    {
        Schema::create('quizzes_old', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('lesson_id')->unique()->constrained('lessons')->cascadeOnDelete();
            $table->unsignedTinyInteger('passing_score')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::statement('INSERT INTO quizzes_old (id, uuid, lesson_id, passing_score, created_at, updated_at) SELECT id, uuid, lesson_id, passing_score, created_at, updated_at FROM quizzes WHERE type = \'lesson\'');

        Schema::drop('quizzes');
        Schema::rename('quizzes_old', 'quizzes');
    }
};