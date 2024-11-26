<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\CommissionController;

class AffectationComTwoManag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:affectation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toutes les manageurs ont reçu les com2 dans leur compte.';

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
                                ->where('mois', view()->shared('periode'))->where('attricalc', 2)->first();
        $statut = 0;
        if(isset($setPermissionCalcul->idpci)){
            if($setPermissionCalcul->valcomanag == 1)
                \Log::info('Les affectations sont en cours.');
            else{
                if($setPermissionCalcul->valcomanag == 2)
                    $bon = 0;
                    //\Log::info('Les affectations sont terminé.');
                else{
                    DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('attricalc', 2)
                                        ->update(["valcomanag"=>1]);
                        $jsonretour = CommissionController::affectationComManag();
                        $retour = json_decode($jsonretour);
                        if($retour->response == 1){
                            \Log::info($retour->message);
                            $statut = 1;
                            DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('valcomanag', 1)
                                        ->update(["valcomanag"=>2]);
                        }
                }
            }
        }
        /*if($statut == 1){
            \Log::info('Toutes les manageurs ont reçu les com2 dans leur compte.');
        }*/
        
    }
}
