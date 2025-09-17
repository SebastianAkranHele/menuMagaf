<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <--- IMPORTANTE

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Inserir usuário admin padrão
        DB::table('admin_users')->insert([
            'username' => 'admin',
            'password' => Hash::make('adminMagaf.123#'),
            'email' => 'admin@magaf.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
};
