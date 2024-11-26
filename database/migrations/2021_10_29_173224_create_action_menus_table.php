<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Menu')->nullable();
            $table->string('action')->nullable();
            $table->string('code_dev')->nullable();
            $table->string('statut')->nullable(); 
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
        Schema::dropIfExists('action_menus');
    }
}
