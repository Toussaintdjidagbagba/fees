<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecapcommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recapcommissions', function (Blueprint $table) {
            $table->bigIncrements('serie');
            $table->string('periode')->nullable();
            $table->string('codeQR')->nullable();
            $table->string('montantEtat')->nullable();
            $table->string('nombreAgent')->nullable();
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
        Schema::dropIfExists('recapcommissions');
    }
}
