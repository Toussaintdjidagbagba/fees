<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTauxNiveausTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taux_niveaus', function (Blueprint $table) {
            $table->bigIncrements('idTauxNiveau');
            $table->unsignedBigInteger('Niveau')->nullable();
            //$table->foreign('Niveau')->references('codeNiveau')->on('niveaux')->onDelete('cascade');
            $table->unsignedBigInteger('Periodicite')->nullable();
            //$table->foreign('Periodicite')->references('idPeriodicite')->on('periodicites')->onDelete('cascade');
            $table->unsignedBigInteger('Produit')->nullable();
            //$table->foreign('Produit')->references('idPeriode')->on('periodes')->onDelete('cascade');
            $table->double('tauxCommissionnement')->nullable();
            $table->integer('dureenapplication')->nullable();
            $table->unsignedBigInteger('Schema')->nullable();
            //$table->foreign('Schema')->references('idSchema')->on('schemas')->onDelete('cascade');
            $table->unsignedBigInteger('user_action')->nullable();
            //$table->foreign('user_action')->references('idUser')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('taux_niveaus');
    }
}
