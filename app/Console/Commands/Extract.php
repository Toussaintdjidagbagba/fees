<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Providers\InterfaceServiceProvider;
use App\Http\Model\Schema;


class Extract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Commission:extra';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mise à jour de la base de donnée local.';

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
        $data = DB::connection('mysqlsimula')->select("select * from rejets where statut = 0");
        $array_entete = array();
        foreach ($data as $key => $values) {
            
            // Récupération de l'entête
            if ($key == 0) {
                $array_entete = array_keys((array) $values);
            }
            
            // Récupération des datas
            foreach ($array_entete as $keyva => $entete) {
                if($entete == "police"){
                    $police = $values->$entete;
                    // recuperer les données et mettre à jour la table distance
                    //\Log::info('Erreur 1 : '.$police);
                    $contrat = DB::table('contrats')->where('police', $police)->first();
					if(isset($contrat->Agent)){
                    $codeApporteur = $contrat->Agent;
                    $payeur = $contrat->Client;
                    
                    $commercial = DB::table('commerciauxes')->where('codeCom', $codeApporteur)->first();
                    $nomCom = $commercial->nomCom.' '.$commercial->prenomCom;
                    
                    $infoclient = DB::table('clients')->where('idClient', $payeur)->first();
                    $nomclient = $infoclient->nom.' '.$infoclient->prenom;
                    $mailclient = $infoclient->mails;
                    $numclient = $infoclient->contact;
                    $id = "id";
                    
                    $hei = InterfaceServiceProvider::getcom($codeApporteur);
                    
                    DB::connection('mysqlsimula')->select('UPDATE `rejets` SET `code`="'.$codeApporteur.'",`app`="'.$nomCom.'",`payeur`="'.$payeur.'",`nompayeur`="'.$nomclient.'",`mail`="'.$mailclient.'",`num`="'.$numclient.'",`statut`= 1 WHERE id = '.$values->$id);
                    DB::connection('mysqlsimula')->select("UPDATE `rejets` SET `Niveau`='".json_decode($hei)[0]->Niveau."',`Equipe`='".json_decode($hei)[0]->Equipe."',`NomEquipe`='".json_decode($hei)[0]->NomEquipe.' '.json_decode($hei)[0]->PrenomEquipe."',`Inspecteur`='".json_decode($hei)[0]->Inspecteur."',`NomInspecteur`='".json_decode($hei)[0]->NomInspecteur.' '.json_decode($hei)[0]->PrenomInspecteur."',`RG`='".json_decode($hei)[0]->RG."',`NomRG`= '".json_decode($hei)[0]->NomRG.' '.json_decode($hei)[0]->PrenomRG."', `CD`='".json_decode($hei)[0]->CD."',`NomCD`= '".json_decode($hei)[0]->NomCD.' '.json_decode($hei)[0]->PrenomCD."' WHERE id = ".$values->$id);
                
                    \Log::info('Mise à jour SIMULA:REJET effectuer avec succès ');
					}
                }
            }
        }
		
		
		$data = DB::connection('mysqlsimula')->select("select * from impayes where dateeffet = null");
        $array_entete = array();
        foreach ($data as $key => $values) {
            
            // Récupération de l'entête
            if ($key == 0) {
                $array_entete = array_keys((array) $values);
            }
			
			// Récupération des datas
            foreach ($array_entete as $keyva => $entete) {
                if($entete == "police"){
                    $police = $values->$entete;
                    $contrat = DB::table('contrats')->where('police', $police)->first();
                    
                    if(isset($contrat->police)){
                    $id = "id";
                    
                    DB::connection('mysqlsimula')->select("UPDATE `impayes` SET `dateeffet`='".$contrat->DateDebutEffet."' WHERE id = ".$values->$id);
                
                    \Log::info('Mise à jour SIMULA:IMPAYES:DateEffet effectuer avec succès ');
					}
                }
            }
			
		}
		
		// Impayes
		
		$data = DB::connection('mysqlsimula')->select("select * from impayes where statut = 0");
        $array_entete = array();
        foreach ($data as $key => $values) {
            
            // Récupération de l'entête
            if ($key == 0) {
                $array_entete = array_keys((array) $values);
            }
            
            // Récupération des datas
            foreach ($array_entete as $keyva => $entete) {
                if($entete == "codeapp"){
                    $codeapp = $values->$entete;
                    $commercial = DB::table('commerciauxes')->where('codeCom', $codeapp)->first();
                    
                    if(isset($commercial->nomCom)){
                    $id = "id";
                    $hei = InterfaceServiceProvider::getcom($codeapp);
                    
                    DB::connection('mysqlsimula')->select("UPDATE `impayes` SET `Niveau`='".json_decode($hei)[0]->Niveau."',`Equipe`='".json_decode($hei)[0]->Equipe."',`NomEquipe`='".json_decode($hei)[0]->NomEquipe.' '.json_decode($hei)[0]->PrenomEquipe."',`Inspecteur`='".json_decode($hei)[0]->Inspecteur."',`NomInspecteur`='".json_decode($hei)[0]->NomInspecteur.' '.json_decode($hei)[0]->PrenomInspecteur."',`RG`='".json_decode($hei)[0]->RG."',`NomRG`= '".json_decode($hei)[0]->NomRG.' '.json_decode($hei)[0]->PrenomRG."', `CD`='".json_decode($hei)[0]->CD."',`NomCD`= '".json_decode($hei)[0]->NomCD.' '.json_decode($hei)[0]->PrenomCD."', statut = 1 WHERE id = ".$values->$id);
                
                    \Log::info('Mise à jour SIMULA:IMPAYES effectuer avec succès ');
					}
                }
            }
        }
		
		
		// Propositions
		
		$data = DB::connection('mysqlsimula')->select("select * from propositions where statut = 0");
        $array_entete = array();
        foreach ($data as $key => $values) {
            
            // Récupération de l'entête
            if ($key == 0) {
                $array_entete = array_keys((array) $values);
            }
            
            // Récupération des datas
            foreach ($array_entete as $keyva => $entete) {
                if($entete == "codeapp"){
                    $codeapp = $values->$entete;
                    $commercial = DB::table('commerciauxes')->where('codeCom', $codeapp)->first();
                    
                    if(isset($commercial->nomCom)){
                    $id = "id";
                    $hei = InterfaceServiceProvider::getcom($codeapp);
                    
                    DB::connection('mysqlsimula')->select("UPDATE `propositions` SET `app`=\"".$commercial->nomCom.' '.$commercial->prenomCom."\",  `Niveau`='".json_decode($hei)[0]->Niveau."',`Equipe`='".json_decode($hei)[0]->Equipe."',`NomEquipe`=\"".json_decode($hei)[0]->NomEquipe.' '.json_decode($hei)[0]->PrenomEquipe."\",`Inspecteur`=\"".json_decode($hei)[0]->Inspecteur."\",`NomInspecteur`=\"".json_decode($hei)[0]->NomInspecteur.' '.json_decode($hei)[0]->PrenomInspecteur."\",`RG`='".json_decode($hei)[0]->RG."',`NomRG`= \"".json_decode($hei)[0]->NomRG.' '.json_decode($hei)[0]->PrenomRG."\", `CD`='".json_decode($hei)[0]->CD."',`NomCD`= \"".json_decode($hei)[0]->NomCD.' '.json_decode($hei)[0]->PrenomCD."\", statut = 1 WHERE id = ".$values->$id);
                
                    \Log::info('Mise à jour SIMULA:IMPAYES effectuer avec succès ');
					}
                }
            }
        }
    
    }
}
