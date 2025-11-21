<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove foreign key existente
            $table->dropForeign(['client_id']);

            // Torna client_id nullable
            $table->unsignedBigInteger('client_id')->nullable()->change();

            // Recria a foreign key
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
};
