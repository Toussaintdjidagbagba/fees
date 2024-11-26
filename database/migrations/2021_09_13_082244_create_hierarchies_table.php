<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHierarchiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hierarchies', function (Blueprint $table) {
            $table->string('codeH'); 
            $table->string('libelleH')->nullable();
            $table->string('structureH')->nullable();
            //$table->foreign('structureH')->references('codeNiveau')->on('niveaux')->onDelete('cascade');
            $table->unsignedBigInteger('managerH')->nullable();
            //$table->foreign('managerH')->references('codeCom')->on('commerciauxes')->onDelete('cascade');
            $table->string('superieurH')->nullable();
            //$table->foreign('superieurH')->references('codeH')->on('hierarchies')->onDelete('cascade');
            $table->string('villeH')->nullable();
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
        Schema::dropIfExists('hierarchies');
    }
}
