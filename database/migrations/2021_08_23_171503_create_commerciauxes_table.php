<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommerciauxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciauxes', function (Blueprint $table) {
            $table->bigIncrements('codeCom');
            $table->string('nomCom')->nullable();
            $table->string('prenomCom')->nullable();
            $table->string('sexeCom')->nullable();
            $table->string('telCom')->nullable();
            $table->string('numCompte')->nullable();
            $table->string('libCompte')->nullable(); 
            $table->string('adresseCom')->nullable();
            $table->string('loginCom')->nullable();
            $table->string('password')->nullable();
            $table->string('mail')->nullable();
            $table->string('AIB')->nullable();
            $table->unsignedBigInteger('codeChefEquipe')->nullable();
            //$table->foreign('codeChefEquipe')->references('codeEquipe')->on('equipes')->onDelete('cascade');
            $table->unsignedBigInteger('codeChefInspection')->nullable();
            //$table->foreign('codeChefInspection')->references('codeInspection')->on('inspections')->onDelete('cascade');
            
            $table->unsignedBigInteger('Niveau')->nullable();
            //$table->foreign('Niveau')->references('codeNiveau')->on('niveaux')->onDelete('cascade');
            $table->unsignedBigInteger('Role')->nullable();
            //$table->foreign('Role')->references('idRole')->on('roles')->onDelete('cascade');
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
        Schema::dropIfExists('commerciauxes');
    }
}
