<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_requirements', function (Blueprint $table) {
            $table->foreignId('prerequisite_category_id')->nullable()
                ->constrained('categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('course_requirements', function (Blueprint $table) {
            $table->dropColumn('prerequisite_category_id');
        });
    }
};
