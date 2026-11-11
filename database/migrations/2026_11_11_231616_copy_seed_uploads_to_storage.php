<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $source = base_path('database/seeders/data/uploads');
        $target = storage_path('app/private/uploads');

        if (is_dir($source)) {
            File::copyDirectory($source, $target);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak menghapus file yang sudah disalin.
    }
};
