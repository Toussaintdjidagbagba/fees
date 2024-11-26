<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendMail;

class EnvoiFichePaieMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:fichepaie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoi des fiches de paie par mail.';

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
        $agents = DB::table('documents')->where('statut', "false")->whereRaw('( type is null )')->first();
        
        if(isset($agents->Agent)){
            //if($agents->type == null || $agents->type == ""){
                $fiche_O = $agents->path;
                $detail_O = $agents->pathD;
                $email = DB::table("commerciauxes")->where('codeCom', $agents->Agent)->first()->mail;
                $email = strtolower(str_replace(' ', '', htmlspecialchars(trim($email))));
                
                
                if (isset($email) && $email != "" && $email != null) {
                    $data = ["fiche" => $fiche_O, "detail" => $detail_O];
                    
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $retour = SendMail::sendFicheCommission($email, "fiche de paie", $data);
                        if( $retour == 0){
                            DB::table('documents')->where('Agent', $agents->Agent)->where('path', $fiche_O)->update(['statut' => "true"]);
                            \Log::info('Fiche de paie de '.$agents->Agent.' envoyer avec succès.');
                        }else
                            DB::table('documents')->where('Agent', $agents->Agent)->where('path', $fiche_O)->update(['type' => "Erreur dans l'envoie. Vérifier l'adresse email ".$email]);
                    }else {
                        DB::table('documents')->where('Agent', $agents->Agent)->where('path', $fiche_O)->update(['type' => "L'adresse email ".$email." est invalide." ]);
                    }
                }else{
                    DB::table('documents')->where('Agent', $agents->Agent)->where('path', $fiche_O)->update(['type' => "L'adresse email n'existe pas" ]);
                }
                
            //}
        }
    }
}
