<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\CommissionController;

class AttributionManag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:attribuer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tous les manageurs sont connu sur les commissions.';

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
                                ->where('mois', view()->shared('periode'))->where('valcalc', 1)->first();
                                
        $statut = 0;
        if(isset($setPermissionCalcul->idpci)){
            if($setPermissionCalcul->attricalc == 1)
                \Log::info('Les attributions sont en cours.');
            else{
                if($setPermissionCalcul->attricalc == 2)
                    $bon = 0;
                    //\Log::info('Les attributions sont terminé.');
                else{
                    DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('valcalc', 1)
                                        ->update(["attricalc"=>1]);
                                        
                        $jsonretour = CommissionController::attributionManagCommission();
                        $retour = json_decode($jsonretour);
                        //\Log::info('Erreur 1 : '.$jsonretour);
                        //\Log::info('Erreur 1 reponse: '.$retour->response);
                        if($retour->response == 1){
                            \Log::info($retour->message);
                            $statut = 1;
                            DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('attricalc', 1)
                                        ->update(["attricalc"=>2]);
                        }
                }
            }
        }
        /*if($statut == 1){
            \Log::info('Tous les manageurs sont connu sur les commissions.');
        }*/
        
    }
}
