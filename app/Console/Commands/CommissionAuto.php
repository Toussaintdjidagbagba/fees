<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\CommissionController;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CommissionAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcul des commissions automatique.';

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
                                ->where('mois', view()->shared('periode'))->first();
        $statut = 0;
        if(!$setPermissionCalcul){
			\Log::info("Démarrage des calculs automatique");
			$jsonretour = CommissionController::setcroncommission();
            $retour = json_decode($jsonretour);
            if($retour->response == 1){
                \Log::info($retour->message);                  
				$jsonretouramc = CommissionController::attributionManagCommission();
                $retouramc = json_decode($jsonretouramc);
                if($retouramc->response == 1){
                     \Log::info($retouramc->message);
                }
			}
        }
    }
}
