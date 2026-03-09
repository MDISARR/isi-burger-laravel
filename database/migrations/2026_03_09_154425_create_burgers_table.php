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
        Schema::create('burgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['is_archived', 'stock_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burgers');
    }
};
