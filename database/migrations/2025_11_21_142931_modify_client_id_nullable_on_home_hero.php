<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyClientIdNullableOnHomeHero extends Migration
{
    public function up(): void
    {
        Schema::table('home_hero', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('home_hero', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
}
