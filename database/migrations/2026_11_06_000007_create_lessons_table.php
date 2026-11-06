<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->json('title');
            $table->string('type')->default('text');
            $table->json('content')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('duration')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
