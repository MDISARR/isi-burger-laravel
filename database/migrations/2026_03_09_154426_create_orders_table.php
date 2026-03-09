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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('client_name');
            $table->string('client_phone', 30);
            $table->string('status')->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('confirmation_message')->nullable();
            $table->timestamp('placed_at');
            $table->string('invoice_path')->nullable();
            $table->timestamp('invoice_generated_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'placed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
