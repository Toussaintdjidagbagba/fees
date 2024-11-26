<?php

namespace App\Http\FonctionControllers;

use Illuminate\Http\Request;
use App\Http\Model\Hierarchie;
use App\Http\Model\Trace;
use App\Http\Model\Compteagent;
use App\Http\Model\Contrat;
use App\Http\Model\Produit;
use App\Http\Model\Client;
use App\Http\Model\Structr;
use App\Http\Model\AppStructr;
use App\Http\Model\Commerciaux;
use DB;
/**
 * 
 */
class Fonction
{
	
	function __construct(){

	}

	public static function LibelleFractionnement($contr_fractionnement){
		switch ($contr_fractionnement) {
			case 'M':
				return "MENSUELLE";
				break;
			case 'A':
				return "ANNUELLE";
				break;
			case 'S':
				return "SEMESTRIELLE";
				break;
			case 'T':
				return "TRIMESTRIELLE";
				break;
			case '':
			    return "UNIQUE";
			    break;
			case 'U':
			    return "UNIQUE";
			    break;
			
			default:
				return "";
				break;
		}
	}

	public static function CheckCons($commercial){
		
		// vérifie s'il est un conseiller
		$check = DB::table('commerciauxes')->where('codeCom', $commercial)->first()->Niveau;

		if ($check == "CONS" || in_array($check, trans('var.group') )) {
			return true;
		}
		return false;
	}
	
	public static function CheckCeqp($commercial){
		
		// vérifie s'il est un conseiller
		$check = DB::table('commerciauxes')->where('codeCom', $commercial)->first()->Niveau;

		if ($check == "CEQP") {
			return true;
		}
		return false;
	}

	public static function CheckIns($commercial){
		
		// vérifie s'il est un conseiller
		$check = DB::table('commerciauxes')->where('codeCom', $commercial)->first()->Niveau;

		if ($check == "INS") {
			return true;
		}
		return false;
	}

	public static function saveClient($contr_nomassur, $contr_prenomassur, $contr_num_assur, $payeur, $tel="", $mail="", $nais=""){
		if(!isset(Client::where("idClient", $contr_num_assur)->first()->idClient)){
			$addClient = new Client();
			$addClient->idClient = $contr_num_assur;
			$addClient->nom = $contr_nomassur;
			$addClient->prenom = $contr_prenomassur;
			$addClient->Payeur = $payeur;
			$addClient->contact = $tel;
			$addClient->mails = $mail;
			$addClient->nais = $nais;
			$addClient->save();
			return true;
		}else{
			Client::where("idClient", $contr_num_assur)->update([
				"nom" => $contr_nomassur,
				"prenom" => $contr_prenomassur,
				"Payeur" => $payeur,
				"contact" => $tel,
				"mails" => $mail,
				"nais" => $nais,
				
			]);
			return true;
		}
		return false;
	}

	public static function saveStructure($code, $lib){
		if(!isset(Hierarchie::where("codeH", $code)->first()->codeH)){
			$add = new Hierarchie(); 
	        $add->codeH = $code;
	        $add->libelleH =  $lib;
	        $add->structureH =  "INS";
	        $add->user_action = 1;
	        return $add->save();
        }
        return 0;
	}

	public static function updatetypeStructure($codeinspection, $typeinspection)
	{
		$code = "INS";
		switch (trim($typeinspection)) {
			case 'BUREAU DIRECT':
				$code = "BD";
				break;
			case 'COURTIER':
				$code = "COU";
				break;
			case 'FORCE DE VENTE':
				$code = "FV";
				break;
			
			default:
				$code = "INS";
				break;
		}
		if($codeinspection != "NULL"){
			if(isset(Hierarchie::where("codeH", $codeinspection)->first()->codeH)){
				Hierarchie::where("codeH", $codeinspection)->update([
					"structureH" => $code
				]);
	        }else{
	        	$add = new Hierarchie(); 
		        $add->codeH = $codeinspection;
		        $add->libelleH =  "";
		        $add->structureH = $code;
		        $add->user_action = 1;
		        return $add->save();
	        }
        }
	}

	public static function GenererCode($typeStructure)
	{
		$code = RAND(1000, 9999);
		while(Hierarchie::where("codeH", $code)->where("structureH", $typeStructure)->first()->codeH != "") {
			$code = RAND(1000, 9999);
		} 
		return $code;	
	}

	public static function commercial($codeagent)
	{
		if(isset(DB::table('commerciauxes')->where('codeCom', $codeagent)->first()->codeCom)) return true; return false;
	}

	public static function ChangeFormatDate($date){
		$aa = substr($date, -8, 4);
		$jj = substr($date, -2);
		$mm = substr($date, -4, 2);
        $dateRetour = $jj."-".$mm."-".$aa;
        if($dateRetour == "--"){
            return "01-01-1999";
        }
		return $dateRetour;
    }

    public static function VérificationPolice($police)
    {
    	if (isset(Contrat::where('police', $police)->first()->police)) {
    		return false;
    	}
    	return true;
    }

    public static function saveProduit($contr_produit, $libelle_produit){
    	if(!isset(Produit::where("idProduit", $contr_produit)->first()->idProduit)){
    		$addProduit = new Produit();
    		$addProduit->idProduit = $contr_produit;
    		$addProduit->libelle = $libelle_produit;
    		$addProduit->user_action = 1;
    		$addProduit->save();
    		return true;
    	}
    	return false;
    }
    
    public static function saveApporteur($code, $nom, $email, $niv){
        // Enregistrer commercial
        $add = new Commerciaux();
        $add->codeCom = $code;
        $add->nomCom = $nom;
        $add->mail = $email;
        $add->Niveau = $niv;
        $add->action_save = 'i';
        $add->user_action = session("utilisateur")->idUser;
        $add->save();
        
        // Créer un compte commercial
        $addC = new Compteagent();
        $addC->Agent = $add->id;
        $addC->save();
        
        return true;
    }
    
    public static function saveStruc($lib){
        if(isset(DB::table('structrs')->where('libelle', $lib)->first()->id))
            return DB::table('structrs')->where('libelle', $lib)->first()->id;
        else{
            $addS = new Structr();
            $addS->libelle = $lib;
            $addS->save();
            
            return $addS->id;
        }
    }
    
    public static function saveApporteuStructure($idStruc, $codeapporteur){
        if(!isset(DB::table('appstrutrs')->where('apporteur', $codeapporteur)->where('structure', $idStruc)->first()->id)){
            $add = new AppStructr();
            $add->apporteur = $codeapporteur;
            $add->structure = $idStruc;
            $add->save();
        }
        return true;
    }

    public static function ChangeFormatDate2($date){
		$aa = substr($date, 6, 4);
		$jj = substr($date, 0, 2);
		$mm = substr($date, 3, 2);
		
        $dateRetour = $jj."-".$mm."-".$aa;

        if($dateRetour == "--"){
            return "01-01-1999";
        }
		return $dateRetour;
    }
    
    public static function ChangeFormatDateFormat($date){
		//$jj = substr($date, 0, 2);
		//$mm = substr($date, 3, 2);
		//$aa = substr($date, 6, 4);
		
		$jj = substr($date, 8, 2);
		$mm = substr($date, 5, 2);
		$aa = substr($date, 0, 4);
		
        $dateRetour = $jj."-".$mm."-".$aa;

        if($dateRetour == "--"){
            return "01-01-1999";
        }
		return $dateRetour;
    }

	public static function Commission()
	{
		return DB::table('commissions')->where("statutcalculer", null)->where("ctrl", 0)->where("Statut", view()->shared('periode'))->where("TypeCommission", "i")->get();
	}
	
	public static function CommissionGroupe()
	{
		return DB::table('commissions')->where("statutcalculer", null)->where("ctrl", 0)->where("TypeCommission", "g")->get();
	}

	public static function GetCommercial($agent)
	{
		return DB::table('commerciauxes')->where("codeCom", $agent)->first();
	}

	public static function GetContrat($numPolice)
	{
		return DB::table('contrats')->where('police', $numPolice)->first();
	}

	public static function Verification($prod)
	{
		$code = DB::table('produits')->where('idProduit', $prod)->first()->codeProduit;
		$val = false;
		switch ($code) {
			case 'pension':
				$val = true;
				break;
			case 'retraite':
				$val = true;
				break;
			case 'etudes':
				$val = true;
				break;
			case 'f':
				$val = true;
				break;
			case 'epargne':
				$val = true;
				break;
			default:
				$val = false;
				break;
		}
		/*
		$codepossible = [2160, 2180, 2410, 2420, 2430, 2460, 3100, 3120];
		$val = false;
		for ($i=0; $i < count($codepossible); $i++) { 
			if ($codepossible[$i] == $prod) {
				$val = true;
			}
		} */
		return $val;
		
	}

	public static function GetidPeriodicite($lib){
        return DB::table('periodicites')->where('libelle', $lib)->first()->idPeriodicite;
    }
    
    public static function ValeurPeriocite($fractionnement){
        if($fractionnement == "") $fractionnement = "MENSUELLE";
        
        switch ($fractionnement) {
			case "MENSUELLE":
				return 1;
				break;
			case "ANNUELLE":
				return 12;
				break;
			case "SEMESTRIELLE":
				return 6;
				break;
			case "TRIMESTRIELLE":
				return 3;
				break;
			case "UNIQUE":
				return 1;
				break;
			
			default:
				return 1;
				break;
		}
		
    }

	public static function VerificationNombreQuittanceDejaCalculer($quittance, $prod, $schema, $frac, $dureeContrat)
	{
	    if($schema == "NOUVEAU"){
            // nombr actu
            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
            if($frac == "") $frac = "MENSUELLE";
            $nombreAutoriser = DB::table('taux_niveaus')
                                ->where("Produit", $prod)
                                //->where("Niveau", "CONS")
                                ->where("Schema", $schema)
                                ->where("Periodicite", Fonction::GetidPeriodicite($frac))
                                ->first();
            //dd(isset($nombreAutoriser->dureenapplication));
            if(isset($nombreAutoriser->dureenapplication)){
	            if ($nombre <= $nombreAutoriser->dureenapplication) {
	                return 0;
	            }
	            return 1;
            }else{
            	return -1;
            }
        }
        if ($schema == "ANCIEN"){
            // nombr actu
            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
            if($frac != "UNIQUE")
                $frac = "MENSUELLE";
            
            $nombreAutoriser = DB::table('taux_niveaus')
                ->where("Produit", $prod)
                ->where("Schema", $schema)
                ->where("Periodicite", Fonction::GetidPeriodicite($frac))
                ->where("dureecontratmin", '<=', $dureeContrat)
                ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
                ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")
                ->first();
            if($frac == "UNIQUE" && !isset($nombreAutoriser->dureenapplication))
            {
                $frac = "MENSUELLE";
                $nombreAutoriser = DB::table('taux_niveaus')
                    ->where("Produit", $prod)
                    ->where("Schema", $schema)
                    ->where("Periodicite", Fonction::GetidPeriodicite($frac))
                    ->where("dureecontratmin", '<=', $dureeContrat)
                    ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
                    ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")
                    ->first();        
            }
            if($nombreAutoriser == null)
                return 2;
            if(isset($nombreAutoriser->dureenapplication)){
                if($nombreAutoriser->dureenapplication == -1)
                    return 0;
	            if ($nombre <= $nombreAutoriser->dureenapplication) {
	                return 0;
	            }
	            return 1;
            }else{
            	return -1;
            }
        }
	}
	
	public static function GetAllTauxGroup($periodicite, $schema, $niv, $prod, $quittance, $dureeContrat, $agent, $police = 0, $conv = 0, $base = 0){
	    if ($schema == "ANCIEN"){
		            // nombr actu
		            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
		            if($periodicite != "UNIQUE") 
		                $periodicite = "MENSUELLE";
		                
		            $taux = DB::table('taux_niveaus')
    		                ->where("Produit", $prod)
    		                ->whereIn("Niveau", $niv)
    		                ->where("Schema", $schema)
    		                //->where("Agent", $agent)
    		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
    		                ->where("dureecontratmin", "<=", $dureeContrat)
    		                ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
    		                ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ");
    		        //if($police != 0 && isset(DB::table('taux_niveaus')->where('police', $police)->first()->police) )
    		          //  $taux = $taux->where("police", $police);
    		            
    		        if($conv != 0)
    		            $taux = $taux->where("conv", $conv);
    		            
    		        if($base != 0)
    		            $taux = $taux->where("basemin", "<=", abs(intval($base)))->whereRaw(" (basemax >= ".abs(intval($base))." or basemax = -1 or basemax = 0) ");
    		       
    		        $taux = $taux->orderBy('dureenapplication', 'asc')->get();
    		        
    		        return $taux;
		            
	    }
	}

	public static function GetTauxNiveau($periodicite, $schema, $niv, $prod, $quittance, $dureeContrat, $agent, $police)
	{
	    $nombre = 0;
		
		$taux = DB::table('taux_niveaus')->where('statad', 0)->where("Produit", $prod)->whereIn("Niveau", $niv)->where("Schema", $schema);
		    
		                
		    // Vérifier aussi si quittance exite dans la même table
		    if (isset(DB::table('taux_niveaus')->where('Quittance', $quittance)->first()->Quittance)) 
		        $taux =  $taux->where("Quittance", $quittance);
		        
		    /*if (isset(DB::table('taux_niveaus')->where('police', $police)->first()->police))
		        $taux =  $taux->where('police', $police); */
			
			if($schema == "NOUVEAU")
			   if($periodicite == "") $periodicite = "MENSUELLE";
				
			if ($schema == "ANCIEN"){
		       // nombr actu
		       $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
		       if($periodicite != "UNIQUE") 
		         $periodicite = "MENSUELLE";
		       if(($periodicite == "UNIQUE" || $periodicite == "ANNUELLE") && $prod == 2300) 
                 $periodicite = "MENSUELLE";
                
		        $taux = $taux->where("dureecontratmin", "<=", $dureeContrat)
		              ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
		              ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")
		              ->orderBy('dureenapplication', 'asc');
			}
			
			// Recherche si le code agent existe dans la table taux_niveaus pour facilité la recherche de taux spécifique
		    if(isset(DB::table('taux_niveaus')->where('statad', 0)->where("Produit", $prod)->whereIn("Niveau", $niv)->where("conv", 0)->where("Schema", $schema)->where('Agent', $agent)->where("dureecontratmin", "<=", $dureeContrat)->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")->first()->Agent))
		        $taux = $taux->where("Agent", $agent);
					
		    $taux = $taux->where("Periodicite", Fonction::GetidPeriodicite($periodicite))->where("conv", 0)->first();
		                
		    return $taux;
	}

	
	public static function GetTauxNiveauAncien($periodicite, $schema, $niv, $prod, $quittance, $dureeContrat, $agent)
	{
	    $nombre = 0;
		// Recherche si le code agent existe dans la table taux_niveaus pour facilité la recherche de taux spécifique
		if(isset(DB::table('taux_niveaus')->where('Agent', $agent)->first()->Agent))
		{
			// Vérifier aussi si quittance exite dans la même table
			if (isset(DB::table('taux_niveaus')->where('Agent', $agent)->where('Quittance', $quittance)->first()->Agent)) {
				if($schema == "NOUVEAU"){
					if($periodicite == "") $periodicite = "MENSUELLE";
					$taux = DB::table('taux_niveaus')
		                ->where("Produit", $prod)
		                ->whereIn("Niveau", $niv)
		                ->where("Schema", $schema)
		                ->where("Agent", $agent)
		                ->where("Quittance", $quittance)
		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
		                ->first();
		            return $taux;
		        }
		        if ($schema == "ANCIEN"){
		            // nombr actu
		            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
		            if($periodicite != "UNIQUE") 
		                $periodicite = "MENSUELLE";
		            $taux = DB::table('taux_niveaus')
		                ->where("Produit", $prod)
		                ->whereIn("Niveau", $niv)
		                ->where("Schema", $schema)
		                ->where("Agent", $agent)
		                ->where("Quittance", $quittance)
		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
		                ->where("dureecontratmin", "<=", $dureeContrat)
		                ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
		                ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")
		                //->where("dureenapplication", ">=", intval($nombre))
		                ->orderBy('dureenapplication', 'asc')
		                ->first();
		            return $taux;
		        }

			}else{
				// Sinon récuperer l'occurence qui comporte l'agent
				if($schema == "NOUVEAU"){
					if($periodicite == "") $periodicite = "MENSUELLE";
					$taux = DB::table('taux_niveaus')
		                ->where("Produit", $prod)
		                ->whereIn("Niveau", $niv)
		                ->where("Schema", $schema)
		                ->where("Agent", $agent)
		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
		                ->first();
		            return $taux;
		        }
		        if ($schema == "ANCIEN"){
		            // nombr actu
		            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
		            if($periodicite != "UNIQUE") 
		                $periodicite = "MENSUELLE";
		                
		            $taux = DB::table('taux_niveaus')
    		                ->where("Produit", $prod)
    		                ->whereIn("Niveau", $niv)
    		                ->where("Schema", $schema)
    		                ->where("Agent", $agent)
    		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
    		                ->where("dureecontratmin", "<=", $dureeContrat)
    		                ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
    		                ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ");
    		            
    		        $taux = $taux->orderBy('dureenapplication', 'asc')->first();
    		        
    		        return $taux;
		            
		        }
			}
		}else{
			//Sinon vérifie si la quittance existe dans la table
			// Pour les cas où c'est unique tous les quittances auquel seront attribuer les taux
            if (isset(DB::table('taux_niveaus')->where('Quittance', $quittance)->first()->Quittance)) {
				if($schema == "NOUVEAU"){
					if($periodicite == "") $periodicite = "MENSUELLE";
					$taux = DB::table('taux_niveaus')
		                ->where("Produit", $prod)
		                ->whereIn("Niveau", $niv)
		                ->where("Schema", $schema)
		                ->where("Quittance", $quittance)
		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
		                ->first();
		            return $taux;
		        }
		        if ($schema == "ANCIEN"){
		            // nombr actu
		            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
		            if($periodicite != "UNIQUE") 
		                $periodicite = "MENSUELLE";
		            $taux = DB::table('taux_niveaus')
		                ->where("Produit", $prod)
		                ->whereIn("Niveau", $niv)
		                ->where("Schema", $schema)
		                ->where("Quittance", $quittance)
		                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
		                ->where("dureecontratmin", "<=", $dureeContrat)
		                ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
		                ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")
		                //->where("dureenapplication", ">=", intval($nombre))
		                ->orderBy('dureenapplication', 'asc')
		                ->first();
		            return $taux;
		        }
			}
		}

		// Si aucun des deaux n'est spécifié

        if($schema == "NOUVEAU"){
            if($periodicite == "") $periodicite = "MENSUELLE";

            $taux = DB::table('taux_niveaus')
                ->where("Produit", $prod)
                ->whereIn("Niveau", $niv)
                ->where("Schema", $schema)
                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
                ->first();
            //if($niv == "CONS")
            //echo json_encode($taux)."<br> <br>";
            return $taux;
        }
        if ($schema == "ANCIEN"){
            // nombr actu
            $nombre = DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->first()->ncom;
            if($periodicite != "UNIQUE") 
                $periodicite = "MENSUELLE";
            if($periodicite == "UNIQUE" && $prod == 2300) 
                $periodicite = "MENSUELLE";

            $taux = DB::table('taux_niveaus')
                ->where("Produit", $prod)
                ->whereIn("Niveau", $niv)
                ->where("Schema", $schema)
                ->where("Periodicite", Fonction::GetidPeriodicite($periodicite))
                ->where("dureecontratmin", "<=", $dureeContrat)
                ->whereRaw(" (dureecontratmax >= ".intval($dureeContrat)." or dureecontratmax = -1) ")
                ->whereRaw(" (dureenapplication >= ".intval($nombre)." or dureenapplication = -1) ")
                //->where("dureenapplication", ">=", intval($nombre))
                ->orderBy('dureenapplication', 'asc')
                ->first();
            //if($quittance == 50234899)
            //dd(DB::table("commissions")->where("NumQuittance", $quittance)->orderby("id", "DESC")->get());
                //dd($taux);

            //if($niv == "CONS")
            //echo json_encode($taux)."  Durée Contrat : ".$dureeContrat."<br> <br>";
            return $taux;
        }

	}

	public static function AIBCONS($aib, $schema, $numprod)
	{
		$codeprod = DB::table('produits')->where('idProduit', $numprod)->first()->codeProduit;
		if ($aib != "") {
			return DB::table('schemas')->where('idSchema', $schema)->where("Produit", $codeprod)->first()->tauxNonAIB;
		} else {
			return DB::table('schemas')->where('idSchema', $schema)->where("Produit", $codeprod)->first()->tauxAIB;
		}
	}

	public static function AIBSUP($Equipe, $schema, $numprod)
	{
		$codeprod = DB::table('produits')->where('idProduit', $numprod)->first()->codeProduit;
		$codChfEquipe = DB::table('hierarchies')->where('codeH', $Equipe)->first()->managerH;
		$aib = DB::table('commerciauxes')->where('codeCom', $codChfEquipe)->first()->AIB;
		if ($aib != "") {
			return DB::table('schemas')->where('idSchema', $schema)->where("Produit", $codeprod)->first()->tauxNonAIB;
		} else {
			return DB::table('schemas')->where('idSchema', $schema)->where("Produit", $codeprod)->first()->tauxAIB;
		}
	}

	public static function SetMontantSup($montant, $agent){
	    if($agent != null){
		    $recupcompte = DB::table('compteagents')->where('Agent', $agent)->first();
		//if($recupcompte->statutEng == 0){
    		$newcompte = $recupcompte->compteEncadrementMoisCalculer + $montant;
    		Compteagent::where('Agent', $agent)->update([
    			'compteEncadrementMoisCalculer' => $newcompte,
    			'statutEng' =>1
    		]);
    		
    		return true;
	    }	    
		/*}
		
		else return false;*/
	}
	
	public static function SetMontantSupGroupe($montant, $agent){
		$recupcompte = DB::table('compteagents')->where('Agent', $agent)->first();
		if($recupcompte->statutEngG == 0){
    		$newcompte = $recupcompte->compteEncadrementMoisCalculerGroupe + $montant;
    		Compteagent::where('Agent', $agent)->update([
    			'compteEncadrementMoisCalculerGroupe' => $newcompte
    		]);
    		
    		return true;
    			    
    	}
		else return false;
	}

	public static function RecupererTaux($commercial){
		$ifu = DB::table('commerciauxes')->where('codeCom', $commercial)->first()->AIB;
		if ($ifu == "" || strlen($ifu) < 12 || strlen($ifu) > 13 || preg_match('#^[0-9]+$#', $ifu) == 0) {
			return DB::table('societes')->first()->tauxNonAIB;
		}else{
			return DB::table('societes')->first()->tauxAIB;
		}
	}

	public static function RecupererCompte($commercial){
		return DB::table('compteagents')->where('Agent', $commercial)->first();
	}

	public static function SetMontantAgent($montant, $agent)
	{
		$recupcompte = DB::table('compteagents')->where('Agent', $agent)->first()->compteMoisCalculer;
		$newcompte = $recupcompte + $montant;
		Compteagent::where('Agent', $agent)->update([
			'compteMoisCalculer' => $newcompte, 
			'MoisCalculer' => view()->shared('periode') //date('m-Y')
		]);

		return true;
	}
	
	public static function SetMontantAgentGroupe($montant, $agent)
	{
		$recupcompte = DB::table('compteagents')->where('Agent', $agent)->first()->compteMoisGroupe;
		$newcompte = $recupcompte + $montant;
		Compteagent::where('Agent', $agent)->update([
			'compteMoisGroupe' => $newcompte, 
			'MoisCalculer' => view()->shared('periode') //date('m-Y')
		]);

		return true;
	}
	
	public static function solderretenue($agent){
	    //Compte de l'agent 
	    
	    $compteAgent = DB::table('compteagents')->where('Agent', $agent)->first();
	    
	    // Vérification s'il y a une avances en cours 
	    if($compteAgent->retenue > 0){
	        
	        $a_rembourser = $compteAgent->retenue;
	        
	        // Vérification s'il y a suffisamment dans son compte
	        if($compteAgent->compte >= $a_rembourser){
	            
	            // décrémentation du compte actuelle de naf
	            $actu_compte = $compteAgent->compte - $a_rembourser;
	            
	            Compteagent::where('Agent', $agent)->update([
    				'retenue' => 0, 
    				'compte' => round ( $actu_compte, 0 ),
    				'traceretenue' => round ( $a_rembourser, 0 )
    			]);
    			
	        }else{
	            // compteBloquer
                Compteagent::where('Agent', $agent)->update([
    				'compteBloquer' => $compteAgent->compte,
    				'compte' => 0
    			]);
	        }
	        
	    }else{
	        return false;
	    }
	    return true;
	}
	
	public static function soldernaf($agent){
	    //Compte de l'agent 
	    
	    $compteAgent = DB::table('compteagents')->where('Agent', $agent)->first();
	    
	    // Vérification s'il y a une avances en cours 
	    if($compteAgent->naf > 0){
	        
	        $a_rembourser = $compteAgent->naf;
	        
	        // Vérification s'il y a avances anticiper en attente
	        if($compteAgent->impayernaf != 0){
	            $a_rembourser = $a_rembourser + ($a_rembourser * $compteAgent->impayernaf);
	        }
	        
	        // Vérification s'il y a suffisamment dans son compte
	        if($compteAgent->compte >= $a_rembourser){
	            
	            // décrémentation du compte actuelle de naf
	            $actu_compte = $compteAgent->compte - $a_rembourser;
	            
	            $actu_naf = $compteAgent->comptenaf + $a_rembourser;
	            
	            Compteagent::where('Agent', $agent)->update([
    				'comptenaf' => round ( $actu_naf, 0 ), 
    				'compte' => round ( $actu_compte, 0 ),
    				'tracenaf' => round ( $a_rembourser, 0 ),
    				'impayernaf' => 0
    			]);
    			
    			
	        }else{
	            // compteBloquer
				if($compteAgent->compte != 0)
					Compteagent::where('Agent', $agent)->update([
						'compteBloquer' => $compteAgent->compte,
						'compte' => 0,
						'impayernaf' => ($compteAgent->impayernaf + 1)

					]);
	        }
	        
	    }else{
	        return false;
	    }
	    return true;
	}
	
	public static function avanceregler($agent){
	    //Compte de l'agent 
	    
	    $compteAgent = DB::table('compteagents')->where('Agent', $agent)->first();
	    
	    // Vérification s'il y a une avances en cours 
	    if($compteAgent->avances > 0){
	        
	        // Calcul du montant à rembourser ce mois
	        $a_rembourser = 0;
	        // montant de l'échance
	        $m_rembourser = $compteAgent->avances / ($compteAgent->duree);

	        // décrémenter pour le mois en cours
	        $a_rembourser = $m_rembourser;
	        
	        if($compteAgent->impayeravances != 0){
	        	// Montant du nombre de mois impayé(s)
	        	if($compteAgent->impayeravances == $compteAgent->duree){
	        		// Si le nombre d'impayés est égale à l'échéance actuelle alors défalqué le nombre de fois l'impayés.
		            $mi_rembourser = ($m_rembourser *  $compteAgent->impayeravances);
		            // Définir le montant d'avances à payer ce mois 
		            $a_rembourser = $mi_rembourser;
	        	}else{
	        		// Sinon défalqué le nombre de fois l'impayé en plus du mois actuelle
	        		$mi_rembourser = ($m_rembourser * $compteAgent->impayeravances);
		            // Augmenter le montant d'avances à payer ce mois 
		            $a_rembourser = $a_rembourser + $mi_rembourser;
	        	}
	        }

	        // Vérification s'il y a avances anticiper en attente
	        if($compteAgent->anticiper != 0){
	        	// Montant anticiper
	            $ma_rembourser = ($m_rembourser * $compteAgent->anticiper);
	            // Augmenter le montant d'avances à payer ce mois 
	            $a_rembourser = $a_rembourser + $ma_rembourser;
	        }
	        
	        // Vérification s'il y a suffisamment dans son compte
	        if($compteAgent->compte >= $a_rembourser){
	            
	            // décrémentation du compte actuelle de l'avance
	            $actu_compte = $compteAgent->compte - $a_rembourser;
	            
	            $actu_avance = $compteAgent->avances - $a_rembourser;
	            
	            $actu_duree = 0;

	            if($compteAgent->impayeravances == $compteAgent->duree)
	            	$actu_duree = $compteAgent->duree - $compteAgent->impayeravances;
	            else
	            	$actu_duree = $compteAgent->duree - (1 + $compteAgent->impayeravances + $compteAgent->anticiper);
	            
	            Compteagent::where('Agent', $agent)->update([
    				'avancesancien' => $compteAgent->avances,
    				'avances' => round ( $actu_avance, 0 ),
    				'duree' => $actu_duree,
    				'recentrembourcer' => round ($a_rembourser, 0 ), 
    				'compte' => round ( $actu_compte, 0 ),
    				'impayeravances' => 0
    			]);
    			
	        }else{

	        	$nombreimpaye = 0;
	        	if($compteAgent->impayeravances == $compteAgent->duree) 
	        		$nombreimpaye = $compteAgent->impayeravances;
	        	else
	        		$nombreimpaye = $compteAgent->impayeravances + 1;

	            // compteBloquer
	            if($compteAgent->compte != 0)
	                Compteagent::where('Agent', $agent)->update([
	    				'avancesancien' => $compteAgent->avances,
	    				'recentrembourcer' => 0,
	    				'compteBloquer' => $compteAgent->compte,
	    				'compte' => 0,
	    				'impayeravances' => ($nombreimpaye)
	    			]);
	            else
	            	Compteagent::where('Agent', $agent)->update([
	    				'avancesancien' => $compteAgent->avances,
	    				'impayeravances' => ($nombreimpaye)
	    			]);
	        }
	        
	    }else{
	        return false;
	    }
	    return true;
	}

	public static function avancesolder($agent){
	    
	    Fonction::avanceregler($agent);
	    /*
		// Vérification des avances dues à l'agent
		$checkavance = DB::table('compteagents')->where('Agent', $agent)->first();
		
		if($checkavance->avances > 0 && $checkavance->recentrembourcer == 0){
			// avances / duree pour savoir comment doit être rembourser suivant la période
			$rembource = $checkavance->avances / $checkavance->duree;
            
            if($checkavance->compte >= $rembource){
                
    			$soldeactu = $checkavance->compte - $rembource;
    
    			$resteavance = $checkavance->avances - $rembource;
    
    			$dureer = $checkavance->duree - 1;
    
    			Compteagent::where('Agent', $agent)->update([
    				'avancesancien' => $checkavance->avances,
    				'avances' => $resteavance,
    				'duree' => $dureer,
    				'recentrembourcer' => $rembource, 
    				'compte' => $soldeactu
    			]);
            }else{
                // compteBloquer
                Compteagent::where('Agent', $agent)->update([
    				'avancesancien' => $checkavance->avances,
    				'recentrembourcer' => 0,
    				'compteBloquer' => $checkavance->compte,
    				'compte' => 0
    			]);
            }
		} */
		return true;
	}

	public static function setCompte($agent, $montant){
		//$com = DB::table('compteagents')->where('Agent', $agent)->first()->compte;
		//dd(intval($montant + $com));
		//$sold = intval($montant + $com);

        // Backup de compte Bloquer 
        $comm = Commerciaux::where('codeCom', $agent)->first();
        
        if($comm->statut == "1"){
            $desactA = Compteagent::where('Agent', $agent)->first()->desact;
            $desactA += $montant;
             Compteagent::where('Agent', $agent)->update([
    				'compte' => 0, 
    				'desact' => $desactA,
    				'statueValide' => 1 // valider
    			]);
        }else{
            $backup = Compteagent::where('Agent', $agent)->first()->compteBloquer;
            if($montant < 0)
                Compteagent::where('Agent', $agent)->update([
    				'compte' => 0, 
    				'compteBloquer' => $montant,
    				'compteBloquerBackup' => $backup,
    				'statueValide' => 1 // valider
    			]);
    		else
    		    Compteagent::where('Agent', $agent)->update([
    				'compte' => $montant, 
    				'compteBloquer' => 0,
    				'compteBloquerBackup' => $backup,
    				'statueValide' => 1 // valider
    			]);
        }
		return true;

	}
	
	public static function reglementamical($agent){
	    // Compte apporteur
	    $compte = Compteagent::where('Agent', $agent)->first();
	    
	    // Vérification de la durée de traitement
	    if( $compte->dureeencourAmical > 0){ 
	    
    	    // prime fixe
    	    $primeamiral = $compte->montantAmical ;
    	    
    	    if( $compte->compte > $primeamiral){
    	        $necompte = $compte->compte - $primeamiral; // prelèvement du prime
        	    
        	    $compteamir = $compte->compteAmical + $primeamiral; // incrémentation du compte amical
        	    
        	    // décrementation de la durée du carec
        	    $neduree = $compte->dureeencourAmical - 1; 
        	    
        	    // mise à jour
        	    Compteagent::where('Agent', $agent)->update([
        			'compte' => $necompte,
        			'tracesAmical' => $primeamiral,
        			'compteAmical' => $compteamir,
        			'dureeencourAmical' => $neduree,
        		]);
    	    }
	    } // SINON PAS DE PRELEVEMENT
	    return 0;
	}
	
	public static function reglementcarec($agent){
	    // Compte apporteur
	    $compte = Compteagent::where('Agent', $agent)->first();
    	    // Vérification de la durée de traitement
    	    if( $compte->dureeencourCarec > 0){
    	        $dateactu = date("d-").view()->shared('periode');
	            if(strtotime($compte->dateeffetcarec) <= strtotime($dateactu)){
    	    
            	    // calcul prime carec
            	    $primecarec = $compte->compte * $compte->tauxCarec / 100;
            	    
            	    // taux min carec
            	    $primemin = DB::table('societes')->first()->primemin;
            	    
            	    if ($primecarec >= $primemin){ // controle sur le prime calculé
            	        
            	        if($compte->compte > $primecarec){
                    	    $necompte = $compte->compte - $primecarec; // prelèvement du prime
                    	    
                    	    $comptecar = $compte->compteCarec + $primecarec; // incrémentation du compte carec
                    	    
                    	    // décrementation de la durée du carec
                    	    $neduree = $compte->dureeencourCarec - 1; 
                    	    
                    	    // mise à jour
                    	    Compteagent::where('Agent', $agent)->update([
                    			'compte' => $necompte,
                    			'traceCarec' => $primecarec,
                    			'compteCarec' => $comptecar,
                    			'dureeencourCarec' => $neduree,
                    		]);
            	        } // SINON PAS DE PRELEVEMENT
            	    } // SINON PAS DE PRELEVEMENT
    	        } // Pas de prélèvement autoriser
	        }// SINON PAS DE PRELEVEMENT
	    return 0;
	}
	
	public static function setCompteGroupe($agent, $montant){
		//$com = DB::table('compteagents')->where('Agent', $agent)->first()->compte;
		//dd(intval($montant + $com));
		//$sold = intval($montant + $com);

        // Backup de compte Bloquer 
        $comm = Commerciaux::where('codeCom', $agent)->first();
        
        if($comm->statut == "1"){
            $desactA = Compteagent::where('Agent', $agent)->first()->desact;
            $desactA += $montant;
             Compteagent::where('Agent', $agent)->update([
    				'compte' => 0, 
    				'desact' => $desactA,
    				'statueValide' => 1 // valider
    			]);
        }else{
            
            $backup = Compteagent::where('Agent', $agent)->first()->compteBloquer;
            if($montant < 0)
                Compteagent::where('Agent', $agent)->update([
    				'compteGroupe' => 0, 
    				'compteBloquer' => $montant,
    				'compteBloquerBackup' => $backup,
    				'statueValideG' => 1 // valider
    			]);
    		else
    		    Compteagent::where('Agent', $agent)->update([
    				'compteGroupe' => $montant, 
    				'compteBloquer' => 0,
    				'compteBloquerBackup' => $backup,
    				'statueValideG' => 1 // valider
    			]);
        }
		return true;

	}
	
	public static function genererNumCommissionG()
	{
		$num = rand(100000000000, 999999999999);

		if(isset(DB::table('commissions')->where('NumCommission', $num)->first()->NumCommission))
			Fonction::genererNumCommission();
		else
			return $num;
	}

	public static function genererNumCommission()
	{
		$num = rand(100000000000, 999999999999);

		if(isset(DB::table('commissions')->where('NumCommission', $num)->first()->NumCommission))
			Fonction::genererNumCommission();
		else
			return $num;
	}
}