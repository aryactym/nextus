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
        // Age fields sudah di-add ke create_course_requirements_table
        // Tidak perlu di-add lagi
    }

    public function down(): void
    {
        // Tidak ada yang di-drop
    }
};
