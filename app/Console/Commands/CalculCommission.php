<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\CommissionController;

class CalculCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:calculer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculer les commissions.';

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
		/** Initialisation de la période des commissions **/
        if(date("d") == 01){
            DB::table('societes')->update(['periode' => date("m-Y")]);
        }
		
        $setPermissionCalcul = DB::table('processuscominds')->where('typ', 'i')
                                ->where('mois', view()->shared('periode'))->where('imp', 1)
                                ->where('calc', 0)->first();
        $statut = 0;
        if(isset($setPermissionCalcul->idpci)){
            
            if($setPermissionCalcul->stacal == 1){
                \Log::info('Calcul en cours');
            }else{
                if($setPermissionCalcul->stacal == 0){
                    // Start calcul
                    DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('imp', 1)
                                        ->where('calc', 0)->update(["stacal"=>1]);
                    
                    $jsonretour = CommissionController::setcommission();
                    $retour = json_decode($jsonretour);
                    if($retour->response == 1){
                        \Log::info($retour->message);
                        $statut = 1;
                        // Stop calcul ProcessusComInd::saveprocessus("calc");
                        DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('imp', 1)->where('calc', 1)
                                        ->update(["stacal"=>2]);
                    }
                
                }else{
                    \Log::info('Calcul fini.');
                }
            }
            
        }
        if($statut == 1){
            \Log::info('Calcul effectuer');
        }
    }
}
