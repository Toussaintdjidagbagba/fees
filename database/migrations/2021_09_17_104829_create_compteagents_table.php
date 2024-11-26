<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompteagentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compteagents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Agent')->nullable();
            //$table->foreign('Agent')->references('codeCom')->on('commerciauxes')->onDelete('cascade');
            $table->double('compte');
            $table->double('avances');
            $table->string('periodicite')->nullable();
            $table->string('duree')->nullable();
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
        Schema::dropIfExists('compteagents');
    }
}
