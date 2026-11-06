<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->json('payload');
            $table->boolean('signature_valid')->default(false);
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['order_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
