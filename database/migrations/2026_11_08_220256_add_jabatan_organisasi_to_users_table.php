<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('nama_jabatan')->nullable()->after('pendidikan_id');
            $table->string('kategori_jabatan')->nullable()->after('nama_jabatan');
            $table->string('nama_organisasi')->nullable()->after('kategori_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['nama_jabatan', 'kategori_jabatan', 'nama_organisasi']);
        });
    }
};
