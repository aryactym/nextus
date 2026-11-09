<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->nullOnDelete()->constrained('courses');
            $table->string('title');
            $table->enum('type', ['webinar', 'pelatihan', 'batch'])->default('webinar');
            $table->enum('format', ['daring', 'luring'])->default('daring');
            $table->string('location')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('quota')->nullable();
            $table->string('registration_url')->nullable();
            $table->enum('status', ['draft', 'open', 'soon', 'full', 'closed'])->default('open');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'starts_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
