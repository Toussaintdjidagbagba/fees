<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\CommissionController;

class ValiderCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:validercalculer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tous les commissions seront envoyer dans le compte temporaire des apporteurs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $setPermissionCalcul = DB::table('processuscominds')->where('typ', 'i')
                                ->where('mois', view()->shared('periode'))->where('calc', 1)
                                ->where('valcalc', 0)->first();
        $statut = 0;
        if(isset($setPermissionCalcul->idpci)){
            if($setPermissionCalcul->stacal == 1){
                \Log::info('Calcul en cours');
            }else{
                if($setPermissionCalcul->stacal == 0){
                    \Log::info('Pas de calcul en cours..');
                }else{
                    if($setPermissionCalcul->stacal == 2){
                       
                        $jsonretour = CommissionController::valideCommissionIndiv();
                        $retour = json_decode($jsonretour);
                        if($retour->response == 1){
                            \Log::info($retour->message);
                            $statut = 1;
                            DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('imp', 1)->where('calc', 1)
                                        ->update(["valcalc"=>1]);
                        }
                    }
                }
            }
        }
        if($statut == 1){
            \Log::info('Tous les commissions sont envoyer dans le compte temporaire des apporteurs.');
        }
        
    }
}
