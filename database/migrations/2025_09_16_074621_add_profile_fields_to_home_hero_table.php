<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToHomeHeroTable extends Migration
{
    public function up()
    {
        Schema::table('home_hero', function (Blueprint $table) {
            $table->string('profile_title')->nullable()->after('social_links');
            $table->string('profile_subtitle')->nullable()->after('profile_title');
            $table->string('profile_image')->nullable()->after('profile_subtitle');
        });
    }

    public function down()
    {
        Schema::table('home_hero', function (Blueprint $table) {
            $table->dropColumn(['profile_title', 'profile_subtitle', 'profile_image']);
        });
    }
}
