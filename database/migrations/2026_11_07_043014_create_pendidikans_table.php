<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendidikans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // SD, SMP, SMA, Diploma, Sarjana, Pascasarjana
            $table->integer('level')->default(0); // untuk pengurutan tingkat pendidikan
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('pendidikans', function (Blueprint $table) {
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendidikans');
    }
};
