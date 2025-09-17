<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeHeroTable extends Migration
{
    public function up()
    {
        Schema::create('home_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->string('background_image')->nullable();
            $table->json('social_links')->nullable(); // WhatsApp, Instagram etc
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_hero');
    }
}
