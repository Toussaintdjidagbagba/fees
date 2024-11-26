<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\CommissionController;
use GuzzleHttp\Client;

class EnvoieFicheCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:generefiche';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toutes les commissions sont validées par les acteurs et les fiches de paie seront générées.';

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
                                ->where('mois', view()->shared('periode'))->where('tres', 1)
                                ->first();
        if(isset($setPermissionCalcul->idpci)){
            if($setPermissionCalcul->fin == 1){
                //\Log::info('Génération des fiches de paie en cours..');
            }else{
                /*if($setPermissionCalcul->fin == 2){
                    \Log::info('Pas de fiche de paie à généré.');
                }else{*/
                    if($setPermissionCalcul->fin == 0){
                        DB::table('processuscominds')->where('typ', 'i')->where('mois', view()->shared('periode'))->where('tres', 1)
                        ->update(["fin"=>1]);
                        //$jsonretour = CommissionController::setgenerationfiche();
                        $client = new Client();
                        $res = $client->get("https://fees.nsiaviebenin.com/genererfichepaie");
                        //$verif = json_decode($res->getBody()->getContents());
                        $retour = json_decode($res->getBody()->getContents());
                        if($retour->response == 1){
                            \Log::info($retour->message);
                            DB::table('processuscominds')->where('typ', 'i')->where('mois', view()->shared('periode'))->where('tres', 1)->where('fin', 1)
                                        ->update(["fin"=>2]);
                        }
                    }
                //}
            }
        }
    }
}
