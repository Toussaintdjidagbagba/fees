<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void 
     */
    public function up()
    {                 
        Schema::create('commissions', function (Blueprint $table) {
            $table->string('NumCommission'); // primary key
            // $table->unsignedBigInteger('NumAgent')->nullable(); redondance
            //$table->foreign('NumAgent')->references('codeCom')->on('commerciauxes')->onDelete('cascade');

            $table->unsignedBigInteger('Structure')->nullable();
            //$table->foreign('Structure')->references('idStructure')->on('structures')->onDelete('cascade');
            $table->unsignedBigInteger('Niveau')->nullable();
            //$table->foreign('Niveau')->references('codeNiveau')->on('niveaux')->onDelete('cascade');
            $table->unsignedBigInteger('NumPolice')->nullable();
            //$table->foreign('NumPolice')->references('police')->on('contrats')->onDelete('cascade');
            
            $table->unsignedBigInteger('NumQuittance')->nullable();
            $table->string('DateCreation')->nullable();
            $table->string('DateProduction')->nullable();
            $table->integer('Garantie')->nullable();
            $table->integer('BaseCommission')->nullable();
            $table->string('NomTotal')->nullable();
            $table->integer('NumReleve')->nullable();
            $table->string('IndexQuittance')->nullable();
            
            // Conseiller
            $table->integer('MontantCommissionConseillerCalculer')->comment("Montant calculer")->nullable();
            $table->integer('AIB_Conseiller')->comment("MontantCommissionConseillerCalculer * TauxAIBConseiller ")->nullable();
            $table->integer('MontantNetAPayerConseiller')->comment("MontantCommissionConseillerCalculer - AIB_Conseiller ")->nullable();
            
            // Chef d'équipe
            $table->integer('MontantCommissionCEQCalculer')->comment(" ? ")->nullable();
            $table->integer('AIB_CEQ')->comment("MontantCommissionCEQCalculer * TauxAIBCommercieux ")->nullable();
            $table->integer('MontantNetAPayerCEQ')->comment("MontantCommissionCEQCalculer - AIB_CEQ ")->nullable();
            
            // Inspecteur
            $table->integer('Taux')->comment("MmtCom / BaseCommission ")->nullable();
            $table->integer('TauxAbsolu')->comment(" ValeurAbsolue(MmtCom / BaseCommission) ")->nullable();
            $table->integer('Vitalis')->comment(" ? ")->nullable();
            $table->integer('Libre')->comment(" ? ")->nullable();
            $table->integer('Autres')->comment(" ? ")->nullable();
            $table->integer('PD')->comment(" ? ")->nullable();
            $table->integer('EpargneP')->comment(" ? ")->nullable();
            $table->integer('MontantCommissionInspecteurCalculer')->comment(" ? ")->nullable();
            $table->integer('AIB_Inspecteur')->comment(" MontantCommissionInspecteurCalculer * TauxAIBCommercieux ")->nullable();
            $table->integer('MontantNetAPayerInspecteur')->comment(" MontantCommissionInspecteurCalculer - AIB_Inspecteur  ")->nullable();

            $table->integer('ctrl')->comment(" ? ")->nullable();
            $table->string('Statut')->comment(" ? ")->nullable();
            $table->string('Toc')->comment(" ? ")->nullable();

            $table->unsignedBigInteger('Autrecommission')->nullable();
            //$table->foreign('Autrecommission')->references('codeAutre')->on('autrecommissions')->onDelete('cascade');
            $table->string('statutcalculer')->nullable();
            $table->integer('ncom')->comment("nombre commission")->nullable();
            $table->string('TypeCommission')->nullable();
            $table->string('bareme')->comment("ANCIEN BAREME ou NOUVEAU BAREME. ANCIEN SCHEMA ou NOUVEAU SCHEMA")->nullable();
            $table->string('statut_occu')->default("0");
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
        Schema::dropIfExists('commissions');
    }
}
