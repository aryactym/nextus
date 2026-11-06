<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('foundation');
            $table->string('status')->default('active');
            $table->string('logo')->nullable();
            $table->json('brand')->nullable();
            $table->string('default_locale')->default('id');
            $table->string('linkedin_org_id')->nullable();
            $table->string('custom_domain')->nullable();
            $table->json('midtrans_config')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
