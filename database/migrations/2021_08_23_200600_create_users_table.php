<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations. 
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('idUser');
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('sexe')->nullable();
            $table->string('tel')->nullable();
            $table->string('mail')->nullable();
            $table->string('adresse')->nullable();
            $table->string('login')->nullable();
            $table->string('password')->nullable();
            $table->string('auth')->nullable();
            $table->unsignedBigInteger('Role')->nullable();
            //$table->foreign('Role')->references('idRole')->on('roles')->onDelete('cascade');
            $table->string('other')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_action')->nullable();
            //$table->foreign('user_action')->references('idUser')->on('users')->onDelete('cascade');
            $table->string('action_save')->nullable();
            $table->string('statut')->default("0");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
