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
        Schema::create('size_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('base_products')->onDelete('cascade');
            $table->string('size');
            $table->string('color');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_colors');
    }
};
