<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schemas', function (Blueprint $table) {
            $table->bigIncrements('idSchema');
            $table->string('libelle');
            $table->double('tauxAIB')->nullable();
            $table->double('tauxNonAIB')->nullable();
            $table->unsignedBigInteger('NumProduit')->nullable();
            //$table->foreign('NumPolice')->references('idProduit')->on('produits')->onDelete('cascade');
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
        Schema::dropIfExists('schemas');
    }
}
