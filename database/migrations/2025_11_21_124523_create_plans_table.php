<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2)->default(0);
        $table->integer('max_products')->nullable();
        $table->integer('max_categories')->nullable();
        $table->boolean('custom_domain')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
