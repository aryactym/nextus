<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid()->unique()->after('id');
            $table->foreignId('organization_id')->constrained()->after('uuid');
            $table->string('locale')->default('id');
            $table->string('linkedin_url')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'locale', 'linkedin_url', 'uuid']);
        });
    }
};
