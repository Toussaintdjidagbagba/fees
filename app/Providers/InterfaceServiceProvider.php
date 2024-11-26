<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use App\Http\FonctionControllers\Fonction;

class InterfaceServiceProvider extends ServiceProvider
{
    
    public static function convertirMoisAnglaisEnFrancais($moisEnAnglais) {
        $moisEnAnglaisToFrancais = [
            'January' => 'Janvier',
            'February' => 'Février',
            'March' => 'Mars',
            'April' => 'Avril',
            'May' => 'Mai',
            'June' => 'Juin',
            'July' => 'Juillet',
            'August' => 'Août',
            'September' => 'Septembre',
            'October' => 'Octobre',
            'November' => 'Novembre',
            'December' => 'Décembre'
        ];

        return $moisEnAnglaisToFrancais[$moisEnAnglais] ?? null;
    }
    
    public static function defaultCarec(){
        return DB::table('societes')->first()->carec;
    }
    
    public static function nivprocessus(){
        $init = DB::table('processuscominds')->where('typ', 'i')
                                ->where('mois', view()->shared('periode'))
                                ->where('imp', 1)->where('tres', 0)->first();
		$initsp = DB::table('processuscominds')->where('typ', 'i')
                                ->where('mois', view()->shared('periode'))
                                ->where('imp', 1)->where('sp', 0)->first();
        if(isset($init->nombrC))
			if(session('utilisateur')->Role == 1 || isset($initsp->nombrC) )
				return 1;
			else
            	return 0;
        else
            return 1;
    }
    
    public static function pourcent(){
        $actu = count(Fonction::Commission());
        
        $init = DB::table('processuscominds')->where('typ', 'i')
                                ->where('mois', view()->shared('periode'))->where('imp', 1)
                                ->where('calc', 0)->first();
        if(isset($init->nombrC))
        {
            $poer = ($init->nombrC - $actu) * 100 / $init->nombrC;
            return round($poer, 0);
        }else{
            return 0;
        }
    }
    
    public static function niveauVal($niv){
        $list = DB::table('commissions')->select('statutcalculer as statutC', 'Statut')->where('TypeCommission', 'i');
        switch ($niv) {
            case -1:
                $list = $list->where("Statut", null)
                        ->where("ctrl", 1);
                break;
            case 0:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', null)
                        ->where("ctrl", 1);
                break;
            case 1:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', null)
                        ->where("ctrl", 1);
                break;
            case 2:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', null)
                        ->where("ctrl", 1);
                break;
            case 3:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', null)
                        ->where("ctrl", 1);
                break;
            case 4:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', "oui")
                        ->where('confirmercdaf', null)
                        ->where("ctrl", 1);
                break;
            case 5:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', "oui")
                        ->where('confirmercdaf', "oui")
                        ->where('confirmertresorerie', null)
                        ->where("ctrl", 1);
                break;
            case 6:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', "oui")
                        ->where('confirmercdaf', "oui")
                        ->where('confirmertresorerie', "oui")
                        ->where("ctrl", 2);
                break;
        }
        
        $listx = $list->get();
        
        if(count($listx) != 0){
            
            return 1;
        }else{
            return 2;
        }
            
    }
    
    public static function niveauValG($niv){
        $list = DB::table('commissions')->select('statutcalculer as statutC', 'Statut')->where('TypeCommission', 'g');
        switch ($niv) {
            case -1:
                $list = $list->where("Statut", null)
                        ->where("ctrl", 1);
                break;
            case 0:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', null)
                        ->where("ctrl", 1);
                break;
            case 1:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', null)
                        ->where("ctrl", 1);
                break;
            case 2:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', null)
                        ->where("ctrl", 1);
                break;
            case 3:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', null)
                        ->where("ctrl", 1);
                break;
            case 4:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', "oui")
                        ->where('confirmercdaf', null)
                        ->where("ctrl", 1);
                break;
            case 5:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', "oui")
                        ->where('confirmercdaf', "oui")
                        ->where('confirmertresorerie', null)
                        ->where("ctrl", 1);
                break;
            case 6:
                $list = $list->where("commissions.Statut", view()->shared('periode'))->where("statutcalculer", "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('confirmerdt', "oui")
                        ->where('confirmerdg', "oui")
                        ->where('confirmercdaf', "oui")
                        ->where('confirmertresorerie', "oui")
                        ->where("ctrl", 2);
                break;
        }
        
        $listx = $list->get();
        
        if(count($listx) != 0){
            
            return 1;}
        else{
            
            return 2;
        }
    }
    
    public static function recipIFU($commercial){
        return DB::table('commerciauxes')->where('codeCom', $commercial)->first()->AIB;
    }
    
    public static function  RecupererTaux($commercial){
		$ifu = DB::table('commerciauxes')->where('codeCom', $commercial)->first()->AIB;
		if ($ifu == "") {
			return DB::table('societes')->first()->tauxNonAIB;
		}else{
			return DB::table('societes')->first()->tauxAIB;
		}
	}
    
    public static function RecupCompteAncien($agent, $periode){
        if($periode == "12-2021")
            $data = DB::table('tracecomptes')->where('Commercial', $agent)->where('moiscalculer', $periode)->where('tracecomptes.created_at', ">=", "2021-12-27")->first(); // En raison de plusieurs modification qui a été faite le mois là, celle qui est le recap est celui du 27-12-2021
        else
            $data = DB::table('tracecomptes')->where('Commercial', $agent)->where('moiscalculer', $periode)->first();
            
        if(isset($data->contenu)){
            $compte = json_decode(trim(substr($data->contenu, 23)), true);
            return $compte;
        }else{
            return "";
        }
    }
    
    public static function RecupCompte($agent){
        $compte = DB::table('compteagents')->where('Agent', $agent)->first();
        return $compte;
    }
    
    public static function RecupInfoPayeur($police)
    {
        if($police != null && $police != ""){
        $client = DB::table('contrats')->where('police', $police)->first()->Client;

        $payeur = DB::table('clients')->where('idClient', $client)->first()->Payeur;

        $infoPayeur =  DB::table('clients')->where('idClient', $payeur)->first();

        if (isset($infoPayeur->nom)) {
            return $infoPayeur->nom.' '.$infoPayeur->prenom;
        }else
            return '';
        }else return '';
            
    }
    
    public static function RecupInfoClient($client)
    {
        $info =  DB::table('clients')->where('idClient', $client)->first();

        if (isset($info->nom)) {
            return $info->nom.' '.$info->prenom;
        }else
            return '';
    }
    
    public static function RecupInfoPayeurId($police)
    {
        $client = DB::table('contrats')->where('police', $police)->first()->Client;

        $payeur = DB::table('clients')->where('idClient', $client)->first()->Payeur;

        $infoPayeur =  DB::table('clients')->where('idClient', $payeur)->first();

        if (isset($infoPayeur->nom)) {
            return $infoPayeur->idClient;
        }else
            return '';
    }

    public static function recupactions($value)
    {
        return DB::table('action_menus')->where('Menu', $value)->get();
    }
 
    public static function sous_menu($tab_ss, $menu)
    {

        $tab = array();
        for ($i=0; $i < count($tab_ss); $i++){
            if (isset(DB::table('menus')->where('idMenu', $tab_ss[$i])->where("Topmenu_id", $menu)->first()->Topmenu_id)) {
                array_push($tab, $tab_ss[$i]);
            }
        }
        return $tab;
    }

    public static function verifie_ss($ssm)
    {
        $allmenu_sous = DB::table('action_menu_acces')->join('menus', "menus.idMenu", "=", "action_menu_acces.Menu")->select('Menu', 'Topmenu_id')->where('Role', session('utilisateur')->Role)->where('Topmenu_id', '<>', 0)->where('action_menu_acces.statut', 0)->orderby('num_ordre', 'ASC')->get();

        $val = false;
        foreach($allmenu_sous as $all){
            if ($all->Topmenu_id == $ssm) {
                $val = true;
            }
        }
        return $val;
    }

    public static function actionMenu($menu)
    {
        return DB::table('action_menus')->where('Menu', $menu)->get();
    }

    public static function libmenu($id)
    {
        if ($id == 0) {
            return '';
        }else
        return DB::table('menus')->where('idMenu', $id)->first()->libelleMenu;
    }

    public static function infomenu($id)
    {
        return DB::table('menus')->where('idMenu', $id)->first();
    }

    public static function libprod($code)
    {
        return DB::table('produits')->where('codeProduit', $code)->first()->libelle;
    }
    
    public static function libprodid($code)
    {
        return DB::table('produits')->where('idProduit', $code)->first()->libelle;
    }

    public static function LibelleRole($id)
    {
        return DB::table('roles')->where('idRole', $id)->get()[0]->libelle;        
    }

    public static function inforole($id)
    {
        return DB::table('roles')->where('idRole', $id)->get()[0];        
    }    

    public static function LibelleUser($id)
    {
        $user = DB::table('users')->where('idUser', $id)->get()[0];
        return $user->nom.' '.$user->prenom;        
    }
    
    public static function LibelleUserRecl($id)
    {
        if($id == 0 || $id == null)
            return "";
        $user = DB::table('users')->where('idUser', $id)->get()[0];
        return $user->nom.' '.$user->prenom;        
    }

    public static function infouser($id)
    {
        return DB::table('users')->where('idUser', $id)->get()[0];        
    }

    public static function infoniveau($id)
    {
        return DB::table('niveaux')->where('codeNiveau', $id)->get()[0];        
    }

    public static function infoproduit($id)
    {
        return DB::table('produits')->where('idProduit', $id)->get()[0];
    }

    public static function infoperiodicite($id)
    {
        return DB::table('periodicites')->where('idPeriodicite', $id)->get()[0];        
    }

    public static function infoschema($id)
    {
        return DB::table('schemas')->where('idSchema', $id)->get()[0];        
    }

    public static function LibelleSchema($schema)
    {
        return DB::table('schemas')->where('idSchema', $schema)->first()->libelle;
    }
    
    

    public static function LibelleCHEFEquipe($code)
    {
        $numcom = DB::table('equipes')->where('codeEquipe', $code)->first()->CHEF;
        return DB::table('commerciauxes')->where('codeCom', $numcom)->first()->nomCom.' '.
        DB::table('commerciauxes')->where('codeCom', $numcom)->first()->prenomCom;
    }

    public static function LibelleCHEFInspecteur($code)
    {
        $numcom = DB::table('inspections')->where('codeInspection', $code)->first()->CHEF;
        return DB::table('commerciauxes')->where('codeCom', $numcom)->first()->nomCom.' '.
        DB::table('commerciauxes')->where('codeCom', $numcom)->first()->prenomCom;
    }

    public static function infocom($code)
    {
        return DB::table('commerciauxes')->where('codeCom', $code)->get()[0];
    }

    public static function Libellecom($code)
    {
        if ($code == null || $code == 0) {
            return "";
        } else {
            return DB::table('commerciauxes')->where('codeCom', $code)->get()[0]->nomCom.' '.DB::table('commerciauxes')->where('codeCom', $code)->get()[0]->prenomCom;
        }
    }
    
    public static function infohier($id)
    {
        if ($id == null || $id == 0) {
            return "";
        } else {
            if ($id == "8888" || $id == "7777" || $id == "9999") {
                return "Par défaut";
            }
            $h = DB::table('hierarchies')->where('codeH', $id)->get()[0];
            if ($h->managerH == 0) {
                return "inconnue";
            }
            
            return InterfaceServiceProvider::Libellecom($h->managerH);
        }
    }

    public static function Equipecom($code)
    {
        return DB::table('commerciauxes')->where('codeCom', $code)->get()[0]->codeEquipe;
    }

    public static function infotaux($id)
    {
        return DB::table('taux_niveaus')->where('idTauxNiveau', $id)->get()[0];        
    }

    public static function infoequipe($id)
    {
        return DB::table('equipes')->where('codeEquipe', $id)->get()[0];        
    }

    public static function infoins($id)
    {
        return DB::table('inspections')->where('codeInspection', $id)->get()[0];        
    }

    public static function infomanageur($id)
    {
        if ($id == 0 || $id == null) {
            return "inconnue";
        }
        
        if (!isset(DB::table('commerciauxes')->where('codeCom', $id)->get()[0])) {
            return "";
        }
        $h = DB::table('commerciauxes')->where('codeCom', $id)->get()[0];
        $nom = $h->nomCom.' '.$h->prenomCom;
        return $nom;     
    }

    public static function infosup($id)
    {
        if ($id == null || $id == 0) {
            return "";
        } else {
            if ($id == "8888" || $id == "7777" || $id == "9999") {
                return "Par défaut";
            }
            $hs = DB::table('hierarchies')->where('codeH', $id)->get();
            
            if (count($hs) == 0) {
                return "n'exite pas";
            }
            if(isset($hs[0]->managerH) && $hs[0]->managerH == 0)
                return "inconnue";
            $nom = InterfaceServiceProvider::infocom($hs[0]->managerH)->nomCom.' '.InterfaceServiceProvider::infocom($hs[0]->managerH)->prenomCom;
            return $nom;
        }
    }
    
    public static function ChefEquipe($code)
    {
        $numcom = DB::table('equipes')->where('codeEquipe', $code)->first()->CHEF;
        return DB::table('commerciauxes')->where('codeCom', $numcom)->first()->nomCom.' '.
        DB::table('commerciauxes')->where('codeCom', $numcom)->first()->prenomCom;
    }
    

    public static function infohierarchie($id)
    {
        if ($id == null || $id == 0) {
            return "";
        } else {
            if ($id == "8888" || $id == "7777" || $id == "9999") {
                return "Par défaut";
            }
            $h = DB::table('hierarchies')->where('codeH', $id)->get()[0];
            if ($h->managerH == 0) {
                return "inconnue";
            }
            
            return InterfaceServiceProvider::infocom($h->managerH);
        }
    }
    
    public static function infohierarchieNonE($id)
    {
        if ($id == null || $id == 0) {
            return "";
        } else {
            if ($id == "8888" || $id == "7777" || $id == "9999") {
                return "Par défaut";
            }
            $h = DB::table('hierarchies')->where('codeH', $id)->where('structureH', '!=', "CEQP")->get()[0];
            if ($h->managerH == 0) {
                return "inconnue";
            }
            
            return InterfaceServiceProvider::infocom($h->managerH);
        }
    }

    public static function sexe($sigle)
    {
        if ($sigle == 'M') return "Masculin";
        if ($sigle == 'F') return "Féminin";
    }
    
    public static function getcom($Commercial)
    {
        if(isset($Commercial)){
            
            $dataCom = DB::table('commerciauxes')->where('codeCom', $Commercial)->first();
            
            if(isset($dataCom->codeCom))
            {
                $tabcomm = array();
                
                $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $dataCom->codeEquipe)->first();
                if(isset($magEquipe->managerH))
                {
                    $codechefequipe = $magEquipe->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magEquipe->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefequipe = $data->nomCom;
                        $prenomchefequipe = $data->prenomCom;
                    }else{
                        $nomchefequipe = "";
                        $prenomchefequipe = "";
                    }
                }else{ 
                    $codechefequipe = "";
                    $nomchefequipe = "";
                    $prenomchefequipe = "";
                }
                    
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $dataCom->codeInspection)->first();
                if(isset($magIns->managerH)) {
                    $codechefins = $magIns->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magIns->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefins = $data->nomCom;
                        $prenomchefins = $data->prenomCom;
                    }else{
                        $nomchefins = "";
                        $prenomchefins = "";
                    }
                }else{
                    $codechefins = ""; 
                    $nomchefins = "";
                    $prenomchefins = "";
                } 
                
                $magRg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $dataCom->codeRegion)->first();
                if(isset($magRg->managerH)) {
                    $codechefrg = $magRg->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magRg->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefrg = $data->nomCom;
                        $prenomchefrg = $data->prenomCom;
                    }else{
                        $nomchefrg = "";
                        $prenomchefrg = "";
                    }
                }else{ 
                    $codechefrg = "";
                    $nomchefrg = "";
                    $prenomchefrg = "";
                }
                
                $magcd = DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $dataCom->codeCD)->first();
                if(isset($magcd->managerH)){ 
                    $codecd = $magcd->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magcd->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefcd = $data->nomCom;
                        $prenomchefcd = $data->prenomCom;
                    }else{
                        $nomchefcd = "";
                        $prenomchefcd = "";
                    }
                }else{ 
                    $codecd = "";
                    $nomchefcd = "";
                    $prenomchefcd = "";
                }
                
                $tabdata = [
                    'Commercial' => $dataCom->codeCom, 
                    'Nom' => $dataCom->nomCom, 
                    'Prenom' => $dataCom->prenomCom,
                    'Niveau' => $dataCom->Niveau,
                    'Equipe' => $codechefequipe,
                    'NomEquipe' => $nomchefequipe, 
                    'PrenomEquipe' => $prenomchefequipe,
                    'Inspecteur' => $codechefins,
                    'NomInspecteur' => $nomchefins, 
                    'PrenomInspecteur' => $prenomchefins,
                    'RG' => $codechefrg,
                    'NomRG' => $nomchefrg, 
                    'PrenomRG' => $prenomchefrg,
                    'CD' => $codecd,
                    'NomCD' => $nomchefcd, 
                    'PrenomCD' => $prenomchefcd
                ];
                
                array_push($tabcomm, $tabdata);
                
                return json_encode($tabcomm);
            }else{
                return "";
            }
        }
    }
}
