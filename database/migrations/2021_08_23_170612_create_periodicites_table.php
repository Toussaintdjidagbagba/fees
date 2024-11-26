<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodicitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodicites', function (Blueprint $table) {
            $table->bigIncrements('idPeriodicite');
            $table->string('libelle')->nullable();
            $table->unsignedBigInteger('user_action')->nullable();
            //$table->foreign('user_action')->references('idUser')->on('users')->onDelete('cascade');
            $table->string('statut')->default("0");
            $table->string('action_save')->nullable();
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
        Schema::dropIfExists('periodes');
    }
}
