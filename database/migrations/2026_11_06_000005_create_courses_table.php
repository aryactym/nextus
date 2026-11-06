<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ownership')->default('own');
            $table->string('visibility')->default('public');
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->string('slug');
            $table->string('level')->default('beginner');
            $table->integer('duration_minutes')->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('access_type')->default('free');
            $table->string('certificate_type')->default('free');
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->json('suggested_amounts')->nullable();
            $table->boolean('allow_custom_amount')->default(true);
            $table->string('currency')->default('IDR');
            $table->integer('passing_grade')->default(75);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['slug', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
