
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContratsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->string('police'); // primary key
            $table->string('NumProp')->nullable(); // je ne sais pas
            $table->string('NumExterne')->nullable(); // je ne sais pas
            $table->unsignedBigInteger('Produit')->nullable();
            //$table->foreign('Produit')->references('idProduit')->on('produits')->onDelete('cascade');
            $table->unsignedBigInteger('Client')->nullable();
            //$table->foreign('Client')->references('idClient')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('Reglement')->nullable();
            //$table->foreign('Reglement')->references('idReglement')->on('reglements')->onDelete('cascade');
            $table->unsignedBigInteger('Payeur')->nullable();
            //$table->foreign('Payeur')->references('idClient')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('Agent')->nullable();
            //$table->foreign('Agent')->references('codeCom')->on('commerciauxes')->onDelete('cascade');
            $table->string('fractionnement')->nullable();
            $table->string('DateCreation')->nullable();
            $table->string('DateResil')->nullable();
            $table->string('DateDebutEffet')->nullable();
            $table->string('DateFinEffet')->nullable();
            $table->unsignedBigInteger('user_action')->nullable();
            //$table->foreign('user_action')->references('idUser')->on('users')->onDelete('cascade');
            $table->string('action_save')->nullable();
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
        Schema::dropIfExists('contrats');
    }
}
