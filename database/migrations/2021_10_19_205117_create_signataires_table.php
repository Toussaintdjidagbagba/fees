<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignatairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signataires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('idSignataire')->nullable();
            $table->string('CodeCommission')->nullable();
            $table->string('Nom')->nullable();
            $table->string('pathSignataire')->nullable();
            $table->string('DateCalculer')->nullable();
            $table->string('RoleSignataire')->nullable();
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
        Schema::dropIfExists('signataires');
    }
}
