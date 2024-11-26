<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avenants', function (Blueprint $table) {
            $table->bigIncrements('codeAvenant'); 
            $table->string('codeHerarchieModifier')->nullable();
            $table->string('path')->nullable();
            $table->string('referenceNoteSave')->nullable();
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->string('dateeffet')->nullable();
            $table->string('existantManageur')->nullable();
            $table->string('nouveauManageur')->nullable();
            $table->string('structure')->nullable();
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
        Schema::dropIfExists('avenants');
    }
}
