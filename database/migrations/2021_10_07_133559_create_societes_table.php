<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocietesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('societes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('libelleSociete')->nullable();
            $table->string('avis')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->string('adresse')->nullable();
            $table->string('signature')->nullable();
            $table->string('piedpage')->nullable();
            $table->string('numautorisation')->nullable();
            $table->string('aidemanuel')->nullable();
            $table->double('tauxAIB');
            $table->double('tauxNonAIB');
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
        Schema::dropIfExists('societes');
    }
}
