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
        Schema::table('course_requirements', function (Blueprint $table) {
            $table->unsignedSmallInteger('min_age')->nullable()->after('pendidikan_id');
            $table->unsignedSmallInteger('max_age')->nullable()->after('min_age');
        });
    }

    public function down(): void
    {
        Schema::table('course_requirements', function (Blueprint $table) {
            $table->dropColumn(['min_age', 'max_age']);
        });
    }
};
