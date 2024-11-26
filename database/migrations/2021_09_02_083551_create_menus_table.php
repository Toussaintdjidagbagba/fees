<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('idMenu');
            $table->string('libelleMenu')->nullable();
            $table->string('titre_page')->nullable();
            $table->string('controller')->nullable();
            $table->string('route')->nullable();
            $table->unsignedBigInteger('Topmenu_id')->nullable();
            //$table->foreign('Topmenu_id')->references('idMenu')->on('menus')->onDelete('cascade');
            $table->unsignedBigInteger('user_action')->nullable();
            //$table->foreign('user_action')->references('idUser')->on('users')->onDelete('cascade');
            $table->integer('num_ordre')->nullable();
            $table->integer('order_ss')->nullable();
            $table->string('element_menu')->nullable();
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
        Schema::dropIfExists('menus');
    }
}
