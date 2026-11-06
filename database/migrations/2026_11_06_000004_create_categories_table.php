<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->json('name');
            $table->string('slug');
            $table->integer('sort')->default(0);
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['slug', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
