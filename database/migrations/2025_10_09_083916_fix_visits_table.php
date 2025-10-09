<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('page')->nullable()->default('home')->change();
            $table->string('url')->nullable()->after('page');
            $table->string('method', 10)->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('page')->nullable(false)->default(null)->change();
            $table->dropColumn(['url', 'method']);
        });
    }
};
