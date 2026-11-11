<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
            $table->string('url')->nullable()->after('name');
            $table->string('logo')->nullable()->after('url');
            $table->boolean('is_active')->default(true)->after('sort');
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->string('slug')->change();
            $table->dropColumn(['url', 'logo', 'is_active']);
        });
    }
};
