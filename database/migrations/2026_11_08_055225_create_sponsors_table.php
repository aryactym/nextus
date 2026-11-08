<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
