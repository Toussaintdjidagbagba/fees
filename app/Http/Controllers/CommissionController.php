<?php

namespace App\Http\Controllers;

use App\Providers\InterfaceServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\FonctionControllers\Fonction;
use DB;
use App\Http\Model\Commission;
use App\Http\Model\Recapcommission;
use App\Http\Model\Reglement;
use App\Http\Model\Signataire;
use App\Http\Model\Document;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel; 
use App\Exports\ExportCommissionAll;
use App\Http\Model\Compteagent;
use App\Exports\ExportErreurAutreCommission;
use App\Http\Import\ImportExcel;
use Illuminate\Support\Facades\Session;
use QrCode;
use App\Http\Model\Trace;
use App\Http\Model\ProcessusComInd;
use App\Http\Model\Tracecompte;
use Crabbly\Fpdf\Fpdf;
use App\Exports\ExportCommissionGlobale;
use App\Exports\ExportCommissionDETAILGlobale;
use App\Exports\ExportCommissionControleDETAIL;
use App\Exports\ExportCommissionControlleResume;
use App\Exports\ExportCommissionGlobaleTraitement;
use Carbon\Carbon;

class PDFF extends Fpdf
{
    //
    function Header() {
        
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',5);
        $this->Cell(0,10,iconv('utf-8','cp1252',"Société Anonyme avec Conseil d'administration au Capital social de F CFA 3.000.000.000. Entreprise régie par le Code CIMA Immeuble NSIA, 1066 Boulevard Saint Michel - 08 BP 0258 Tri Postal - "),0,0,'C');
        $this->ln(4);
        $this->Cell(0,10, iconv('utf-8', 'cp1252', "Tél (229) 99 24 93 60 / 21 36 54 89 Fax(229) 21 31 35 17 Email nsiavie.benin@groupensia.com - Site web : www.nsiaviebenin.com"),0,0,'C');
    }
	
	function AdjustText($x, $y, $text, $maxWidth) { 
		$this->SetFont('Arial', '', 7); 
		while ($this->GetStringWidth($text) > $maxWidth) { 
			$size = $this->FontSizePt - 1; 
			if ($size < 4) 
			{ // Si la taille de police est trop petite, on divise le texte sur deux lignes 
				$words = explode(' ', $text); 
				$line1 = ''; 
				$line2 = ''; 
				foreach ($words as $word) { 
					if ($this->GetStringWidth($line1 . ' ' . $word) <= $maxWidth) { 
						$line1 .= ' ' . $word; 
					} else { 
						$line2 .= ' ' . $word; 
					} 
				} 
				$line1 = trim($line1); 
				$line2 = trim($line2); 
				$this->Text($x, $y - 2, iconv('utf-8', 'cp1252', $line1)); 
				$this->Text($x, $y + 2, iconv('utf-8', 'cp1252', $line2)); 
				return; 
			} 
			$this->SetFont('Arial', '', $size); 
		} // Sinon, on ajuste la taille du texte 
		$this->Text($x, $y, iconv('utf-8', 'cp1252', $text)); 
	}
    
}

class CommissionController extends Controller
{
    public function __construct()
    {
        set_time_limit(1024000);
        ini_set("memory_limit", "1024M");
    }
    
    public static function testt()
    {
		
		//$allnaf = DB::table('compteagents')->where("effetnaf", "!=", "01-04-2024")->where("naf", "!=", 0)->get();
		/*$allapporteur = DB::table('tracecomptes')->where("moiscalculer", "03-2024")->get();
		
		foreach ($allapporteur as $app){
			$comp = InterfaceServiceProvider::RecupCompteAncien($app->Commercial, "03-2024");
			
			if($comp['naf'] != 0){
				DB::table('compteagents')->where("Agent", $app->Commercial)->update(["effetnaf" => $comp['effetnaf']]);
				echo "Naf : ". $comp['naf']. " Apporteur : ".$app->Commercial."<br>";
			}
		}
		
		dd("Fin"); */
		
		//dd("bon");
		
        /*
        $setPermissionCalcul = DB::table('processuscominds')->where('typ', 'i')
                                ->where('mois', view()->shared('periode'))->where('valcalc', 1)
                                ->where('attricalc', 0)->first();
         
        if(isset($setPermissionCalcul->idpci)){
                        $jsonretour = CommissionController::attributionManagCommission();
                        $retour = json_decode($jsonretour);
                        dd($retour);
                        if($retour->response == 1){
                            \Log::info($retour->message);
                            $statut = 1;
                            DB::table('processuscominds')->where('typ', 'i')
                                        ->where('mois', view()->shared('periode'))->where('valcalc', 1)
                                        ->update(["attricalc"=>1]);
                        }
                  
        }*/
        
        // Récupérer le ou les sp
        // $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "csp")->first();
                
        //SendMail::sendnotification("emmanueldjidagbagba@gmail.com", [], "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "sp");
            
        //dd("Bon");
    }
    
    public function validecommissionimporter(){
        
        $nom = count(Fonction::Commission());
        // initialisation
        $ini = new ProcessusComInd();
        $ini->typ = 'i';
        $ini->imp = 1;
        $ini->nombrC = $nom;
        $ini->mois = view()->shared('periode');
        $ini->save();
		
		// Récupérer le ou les sp
        $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
        $mails = array();
        foreach ($allAdmin as $value) {
            array_push($mails, $value->mailZ);
        }
            
        SendMail::sendnotification("emmanueldjidagbagba@gmail.com", $mails, "Tous les commissions et contrats importés sont validées et le calcul est lancé.", []);
                                
        return response()->json(['response' =>  1, 'message' => "Tous les commissions et contrats importés sont validées et le calcul est lancé."]);
    }

    public static function setcommission()
    {
        //Récupérer les derniers état de commissions non calculé
        $comm = Fonction::Commission();
        //dd($comm);
        $tab_agents = array();
        $tab_taux = array();
        $tab_taux_affaireconcerner = array();
        $tab_police = array();
        
        // Listes des chefs d'équipes et inspecteurs qui ont bénéficier de commissions ???

        foreach ($comm as $commission) {
            //Pour chaque commissions, récupérer le numéro de police
            $numPolice = $commission->NumPolice;
            
            $Contrat = Fonction::GetContrat($numPolice);
            
            //dd($Contrat == null);
            if($Contrat == null){
                if (!in_array($numPolice, $tab_police))
                    array_push($tab_police, $numPolice);
            }else{
               
            // Omis les contrats résilié
            if($Contrat->statutSunshine == "Actif"){

                //recherche l'agent concerner  par le calcul de commissionnement dans contrat
                $Commercial = Fonction::GetCommercial($Contrat->Agent);
    
                // vérification du code de produit qu'il fait partir bien des produits concerner par le calcul
                // NSIA Etudes, NSIA Retraite, NSIA Pension
                $check = Fonction::Verification($Contrat->Produit);
                
                ////////////////////////////////////////////////////////////////////////////////////////////////////
                
                $ts2 = strtotime($commission->DateFinQuittance);
                $ts1 = strtotime($Contrat->DateDebutEffet);
                    
                    $year1 = date('Y', $ts1);
                    $year2 = date('Y', $ts2);
                    
                    $month1 = date('m', $ts1);
                    $month2 = date('m', $ts2);
                    
                    $ncom = (($year2 - $year1) * 12) + ($month2 - $month1);
                    
                    if($ncom < 0) $ncom = 0;
                
                ////////////////////////////////////////////////////////////////////////////////////////////////////
    
                if ($check && strtotime($Contrat->DateDebutEffet) >= strtotime("01-02-2021")) { 
                    $ncom = $ncom / Fonction::ValeurPeriocite($Contrat->fractionnement);
                    Commission::where('NumCommission', $commission->NumCommission)->update([
                        'ncom' => $ncom
                    ]);
                    // Nouveau Schéma
                    $schema = "NOUVEAU";
    
                    //récuperer baseCommission dans commissions 
                    $baseCommission = $commission->BaseCommission;
    
                    // récupérer le fractionnement dans la table contrat
                    $fractionnement = $Contrat->fractionnement;
    
                    // durée du contrat
                    //$dureecontrat = abs(intval((strtotime($Contrat->DateEcheance) - strtotime($Contrat->DateDebutEffet)) / 60 / 60 / 24 / 30));
                    $date1 = $Contrat->DateEcheance;
                    $date2 = $Contrat->DateDebutEffet;
                    $mois1 = intval(substr($date1, -7, 2));
                    $an1 = intval(substr($date1, -4, 4));
                    $mois2 = intval(substr($date2, -7, 2));
                    $an2 = intval(substr($date2, -4, 4));
    
                    $dureecontrat =  ($an1 - $an2) * 12 + abs($mois1 - $mois2);
    
                    //Vérifier nombre de commission déjà pour le numéro de quittance suivant le fractionnement
                    $checkFrac = Fonction::VerificationNombreQuittanceDejaCalculer($commission->NumQuittance, $Contrat->Produit, $schema, $fractionnement, $dureecontrat);
    
                    if ($checkFrac == -1) {
                        // Enregistrer les taux qui ne pas paramétré
                        if (!in_array($Contrat->Produit, $tab_taux)){
                            array_push($tab_taux, $Contrat->Produit);
                            array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                        }
                    }else{
                        // Si ça dépasse, pas de calcul 
                        if ($checkFrac == 0) {
    
                            // Taux 
                            $taux_niveauCons = "";
                            
                            $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.cons'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            
                            if(isset($Commercial->codeCom)){
                            if($Commercial->Niveau == "COU" || $Commercial->Niveau == "BA"){
                                $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, ["COU", "BA"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            }
                            }else{
                                     // Enregistrer les agents qui n'existent pas
                                    if (!in_array($Contrat->Agent, $tab_agents))
                                        array_push($tab_agents, $Contrat->Agent);
                            }
                            $taux_niveauCEQP = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.ceqp'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            $taux_niveauINSPECTION = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.inspecteur'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            $taux_niveauRG = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.rgt'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            $taux_niveauCD = Fonction::GetTauxNiveau($fractionnement, $schema, ["CD"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            
                            if(isset($taux_niveauCons->tauxCommissionnement))
                            {
                                // Calcul des montant des commissions sans aib
                                $montantCons = (($baseCommission * $taux_niveauCons->tauxCommissionnement / 100) * $taux_niveauCons->pourcentage) / 100;
                                $montant_CEQP = (($baseCommission * $taux_niveauCEQP->tauxCommissionnement / 100) * $taux_niveauCEQP->pourcentage) / 100;
                                $montant_INSPECTION = (($baseCommission * $taux_niveauINSPECTION->tauxCommissionnement / 100) * $taux_niveauINSPECTION->pourcentage) / 100;
                                $montant_RG = 0;
                                $montantCD = 0;
                                if($Contrat->Produit == 6120){
                                        $montant_RG = ($baseCommission * 0.25 / 100);
                                        if(isset($taux_niveauCD->tauxCommissionnement))
                                            $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                }else{
                                    $montant_RG = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                    if(isset($taux_niveauCD->tauxCommissionnement))
                                        $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                    else
                                        $montantCD = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                }
                                if(isset($Commercial->codeCom)){
                                    
                                    $montSun = Commission::where('NumCommission', $commission->NumCommission)->first()->MontantSunShine;
                                    
                                    if( (abs($montSun - round($montantCons))) == 1 )
                                        $montantCons = $montSun;
    
                                    // Mise à  jour de la table Commission
                                    Commission::where('NumCommission', $commission->NumCommission)->update([
                                        'MontantConseiller' => round($montantCons),
                                        'MontantCEQ' => round($montant_CEQP),
                                        'MontantInspecteur' => round($montant_INSPECTION),
                                        'MontantRG' => round($montant_RG),
                                        'MontantCD' => round($montantCD),
                                        'statutcalculer' => 'oui',
                                        'bareme' => $schema,
										'ctrl' => 1
                                    ]);
                                }else{
                                     // Enregistrer les agents qui n'existent pas
                                    if (!in_array($Contrat->Agent, $tab_agents))
                                        array_push($tab_agents, $Contrat->Agent);
                                }
                            }else{
                                // Enregistrer les taux qui ne pas paramétré
                                if (!in_array($commission->NumCommission, $tab_taux_affaireconcerner)){
                                    array_push($tab_taux, $Contrat->Produit);
                                    array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                                }
                            }
    
                        }
                    }
                }
                else{
                    
                    Commission::where('NumCommission', $commission->NumCommission)->update([
                        'ncom' => $ncom
                    ]);
                    
                    // Ancien Schéma
                    $schema = "ANCIEN";
    
                    //récuperer baseCommission dans commissions
                    $baseCommission = $commission->BaseCommission;
    
                    // récupérer le fractionnement dans la table contrat
                    $fractionnement = $Contrat->fractionnement;
    
                    // durée du contrat en mois
                    //$dureecontrat = abs(intval((strtotime($Contrat->DateEcheance) - strtotime($Contrat->DateDebutEffet)) / 60 / 60 / 24 / 30));
                    $date1 = $Contrat->DateEcheance;
                    $date2 = $Contrat->DateDebutEffet;
                    $mois1 = intval(substr($date1, -7, 2));
                    $an1 = intval(substr($date1, -4, 4));
                    $mois2 = intval(substr($date2, -7, 2));
                    $an2 = intval(substr($date2, -4, 4));
    
                    $dureecontrat =  ($an1 - $an2) * 12 + abs($mois1 - $mois2);
    
                    //Vérifier nombre de commission déjà pour le numéro de quittance suivant le fractionnement
                    $checkFrac = Fonction::VerificationNombreQuittanceDejaCalculer($commission->NumQuittance, $Contrat->Produit, $schema, $fractionnement, $dureecontrat);
                    //if($Contrat->Produit == 2300)
                        //dd($commission->NumCommission);
                    
                    if ($checkFrac == -1) {
                        // Enregistrer les taux qui ne pas paramétré
                        if (!in_array($Contrat->Produit, $tab_taux)){
                            array_push($tab_taux, $Contrat->Produit);
                            array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                        }
                    }else{
                        // Si ça dépasse, pas de calcul
                        
                        if ($checkFrac == 0) {
                            $taux_niveauCons = ""; $taux_niveauCEQP = ""; $taux_niveauINSPECTION = ""; $taux_niveauRG = "";
                            // Taux
                            //if(in_array($Commercial->Niveau, trans('var.ceqp')) || in_array($Commercial->Niveau, trans('var.inspecteur')) || in_array($Commercial->Niveau, trans('var.rgt')))
                                $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.cons'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            //else
                              //  $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, [$Commercial->Niveau], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent);
                            if(isset($Commercial->codeCom)){
                            if($Commercial->Niveau == "COU" || $Commercial->Niveau == "BA"){
                                $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, ["COU", "BA"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            }
                            }else{
                                     // Enregistrer les agents qui n'existent pas
                                    if (!in_array($Contrat->Agent, $tab_agents))
                                        array_push($tab_agents, $Contrat->Agent);
                            }
                            $taux_niveauCEQP = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.ceqp'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            $taux_niveauINSPECTION = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.inspecteur'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            $taux_niveauRG = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.rgt'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            $taux_niveauCD = Fonction::GetTauxNiveau($fractionnement, $schema, ["CD"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                            
                        
                            if(isset($taux_niveauCons->tauxCommissionnement)){
                                // Calcul des montant des commissions sans aib
                                $montantCons = (($baseCommission * $taux_niveauCons->tauxCommissionnement / 100) * $taux_niveauCons->pourcentage) / 100;
                                $montant_CEQP = (($baseCommission * $taux_niveauCEQP->tauxCommissionnement / 100) * $taux_niveauCEQP->pourcentage) / 100;
                                $montant_INSPECTION = (($baseCommission * $taux_niveauINSPECTION->tauxCommissionnement / 100) * $taux_niveauINSPECTION->pourcentage) / 100;
                                $montant_RG = 0;
                                $montantCD = 0;
                                if($Contrat->Produit == 6120){
                                        $montant_RG = ($baseCommission * 0.25 / 100);
                                        if(isset($taux_niveauCD->tauxCommissionnement))
                                            $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                    
                                }else{
                                    $montant_RG = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                    if(isset($taux_niveauCD->tauxCommissionnement))
                                        $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                    else
                                        $montantCD = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                }
                                
                                if(isset($Commercial->codeCom)){
                                    $montSun = Commission::where('NumCommission', $commission->NumCommission)->first()->MontantSunShine;
                                    if( (abs($montSun - round($montantCons))) == 1 )
                                        $montantCons = $montSun;
                                        
                                    Commission::where('NumCommission', $commission->NumCommission)->update([
                                        'MontantConseiller' => round($montantCons),
                                        'MontantCEQ' => round($montant_CEQP),
                                        'MontantInspecteur' => round($montant_INSPECTION),
                                        'MontantRG' => round($montant_RG),
                                        'MontantCD' => round($montantCD),
                                        'statutcalculer' => 'oui',
                                        'bareme' => $schema,
										'ctrl' => 1
                                    ]);
                                }else{
                                    // Enregistrer les agents qui n'existent pas
                                    if (!in_array($Contrat->Agent, $tab_agents))
                                        array_push($tab_agents, $Contrat->Agent);
                                }
                            }else{
                                //dd($Contrat);
                                    // Enregistrer les taux qui ne pas paramétré
                                if (!in_array($commission->NumCommission, $tab_taux_affaireconcerner)){
                                    array_push($tab_taux, $Contrat->Produit);
                                    array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                                }
                            }
                        }
                    }
                }
            }
            }   
        }
		
		$allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
        $mails = array();
        foreach ($allAdmin as $value) {
            array_push($mails, $value->mailZ);
        }
        
        TraceController::setTrace("Vous avez lancé le calcul manuel des commissions de ce mois", 1);
        $message = "";
        if(count($tab_agents) != 0 && count($mails) != 0){
            $message .= ' <br><br> Erreur ! Agent(s) inconnu(s) : '.json_encode($tab_agents);
            SendMail::sendnotificationErreurAgent($mails, "Erreur lors des calculs des commissions de ce mois !", ["Agent" => $tab_agents]);
            
        }
        if(count($tab_police) != 0 && count($mails) != 0){
            $message .= ' <br> <br> Erreur ! Contrat(s) inexistant(s) : '.json_encode($tab_police);
            SendMail::sendnotificationErreurAgent($mails, "Erreur lors des calculs des commissions de ce mois !", ["Contrat" => $tab_police]);
            
        }
        if(count($tab_taux) != 0 && count($mails) != 0){
            $message .= ' <br> <br> Erreur ! Produit(s) non paramétré(s) : '.json_encode($tab_taux).'. Commission concernée : '.json_encode($tab_taux_affaireconcerner);
            SendMail::sendnotificationErreurTaux($mails, "Erreur lors des calculs des commissions de ce mois !", ["Produit" => $tab_taux]);
            
        }
        
        ProcessusComInd::saveprocessus("calc");
        
        return json_encode(['response' =>  1, 'message' => $message, 'succes' => "Commission calculée."]);
        return response()->json(['response' =>  1, 'message' => $message, 'succes' => "Commission calculée."]);
        
    }
    
    public static function valideCommissionIndiv()
    {
        $commissioncons = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                ->join('compteagents', 'compteagents.Agent', '=', 'commissions.Apporteur')
                ->select('Apporteur', DB::raw('SUM(MontantConseiller) as comcons'))
                ->whereRaw(" ( contrats.Agent = commissions.Apporteur) ")
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)
                ->groupBy('Apporteur')
                ->get();
        foreach ($commissioncons as $comm) {
            Compteagent::where('Agent', $comm->Apporteur)->update([
            			'compteMoisCalculer' => $comm->comcons, 
            			'MoisCalculer' => view()->shared('periode')
            	    ]);
        }
        
        return json_encode(['response' =>  1, 'message' => "Toutes les commissions calculées ont été validées. "]);
        return response()->json(['response' =>  1, 'message' => "Toutes les commissions calculées ont été validées."]);
    }
    
    // Attribuer les manageurs sur chaque commission calculé 
    public static function attributionManagCommission(){
         $commission = DB::table('commissions')
                ->where('statutcalculer', 'oui')
                ->where('statutEnc', 0)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)->get();
            $temp_commerciaux = array();
            $error = 0;
            $temp_chef = array();
            
            if (isset($commission) && sizeof($commission) != 0) {
                foreach ($commission as $comm) {
                    // Vérifier si le commercial est un conseiller
                    //Avec le conseiller, récupérer le chef de son équipe, de son inspection et de son région pour leurs accordés leur commission
    
                    // vérifie s'il est un conseiller
                    $checkcons = Fonction::CheckCons($comm->Apporteur);
    
                    if ($checkcons) {
    
                        $commercial = DB::table('contrats')->where('police', $comm->NumPolice)->first()->Agent;
    
                        $data_cons = DB::table('commerciauxes')->where('codeCom', $commercial)->first();
    
                        /*if($data_cons->codeEquipe == "" || $data_cons->codeEquipe == null) {
                            array_push($temp_commerciaux, $data_cons->codeCom);
                            $error += 1;
                        }else{ */
                            if($comm->statutEnc == 0){
                                if($data_cons->codeEquipe != "" && $data_cons->codeEquipe != null){
                                    // Code commercial du Chef Equipe
                                    if(isset(DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH)){
                                        $chefequipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH;
                                        // ontrole d'ancien equipe 
                                        $ancienequipe = DB::table('contrats')->where('police', $comm->NumPolice)->first()->ceqp;
                                        if ($ancienequipe != 0 && $ancienequipe != null){
                                            $chefequipe = $ancienequipe;
                                        }
                                        if ($chefequipe != 0 && $chefequipe != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["Toc" => $chefequipe]);
                                        }
                                    }
                                }
    
                                if ($data_cons->codeInspection != "" && $data_cons->codeInspection != null) {
                                    
                                    // Code commercial du Chef Inspection
                                    if(isset(DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH)){
                                        $chefins = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH;
                                        // Controle d'ancien inspecteurs 
                                        $ancienins = DB::table('contrats')->where('police', $comm->NumPolice)->first()->ins;
                                        if ($ancienins != 0 && $ancienins != null)
                                        {
                                            $chefins = $ancienins;
                                        }
                                        if ($chefins != 0 && $chefins != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["premiervalidation" => $chefins]);
                                            
                                        }
                                    }
                                }
                                
                                if ($data_cons->codeRegion != "" && $data_cons->codeRegion != null) {
                                    // Code commercial du Chef Région
                                    if(isset(DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH)){
                                        $chefrg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH;
                                        
                                        // Controle d'ancien rg 
                                        $ancienrg = DB::table('contrats')->where('police', $comm->NumPolice)->first()->rg;
                                        if ($ancienrg != 0 && $ancienrg != null)
                                        {
                                            $chefrg = $ancienrg;
                                        }
                                        if ($chefrg != 0 && $chefrg != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["regionCom" => $chefrg]);
                                            
                                        }
                                    }
                                }
                                
                                if ($data_cons->codeCD != "" && $data_cons->codeCD != null) {
                                    // Code commercial du Chef Coordination
                                    if(isset(DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $data_cons->codeCD)->first()->managerH)){
                                        $chefcd = DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $data_cons->codeCD)->first()->managerH;
                                        // Controle d'ancien cd 
                                        $anciencd = DB::table('contrats')->where('police', $comm->NumPolice)->first()->cd;
                                        if ($anciencd != 0 && $anciencd != null)
                                        {
                                            $chefrg = $anciencd;
                                        }
                                        if ($chefcd != 0 && $chefcd != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["cdCom" => $chefcd]);
                                            
                                        }
                                    }
                                }
                                
                                // Confirmer que telle commission a été attribuer au manager et ne fera plus objet de réatribution
                                DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["statutEnc" => 1]);
                            }
                        //}
                    }
					
					// vérifie s'il est un cehf d'éauipe
                    $checkceqp = Fonction::CheckCeqp($comm->Apporteur);
                    if ($checkceqp) {
    
                        $commercial = DB::table('contrats')->where('police', $comm->NumPolice)->first()->Agent;
    
                        $data_cons = DB::table('commerciauxes')->where('codeCom', $commercial)->first();
    
                        /*if($data_cons->codeInspection == "" || $data_cons->codeInspection == null) {
                            array_push($temp_commerciaux, $data_cons->codeCom);
                            $error += 1;
                        }else{ */
                            if($comm->statutEnc == 0){
                                
                                if ($data_cons->codeInspection != "" && $data_cons->codeInspection != null) {
                                    
                                    // Code commercial du Chef Inspection
                                    if(isset(DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH)){
                                        $chefins = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH;
                                        // Controle d'ancien inspecteurs 
                                        $ancienins = DB::table('contrats')->where('police', $comm->NumPolice)->first()->ins;
                                        if ($ancienins != 0 && $ancienins != null)
                                        {
                                            $chefins = $ancienins;
                                        }
                                        if ($chefins != 0 && $chefins != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["premiervalidation" => $chefins]);
                                            
                                        }
                                    }
                                }
                                
                                if ($data_cons->codeRegion != "" && $data_cons->codeRegion != null) {
                                    // Code commercial du Chef Région
                                    if(isset(DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH)){
                                        $chefrg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH;
                                        
                                        // Controle d'ancien rg 
                                        $ancienrg = DB::table('contrats')->where('police', $comm->NumPolice)->first()->rg;
                                        if ($ancienrg != 0 && $ancienrg != null)
                                        {
                                            $chefrg = $ancienrg;
                                        }
                                        if ($chefrg != 0 && $chefrg != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["regionCom" => $chefrg]);
                                            
                                        }
                                    }
                                }
                                
                                if ($data_cons->codeCD != "" && $data_cons->codeCD != null) {
                                    // Code commercial du Chef Coordination
                                    if(isset(DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $data_cons->codeCD)->first()->managerH)){
                                        $chefcd = DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $data_cons->codeCD)->first()->managerH;
                                        // Controle d'ancien cd 
                                        $anciencd = DB::table('contrats')->where('police', $comm->NumPolice)->first()->cd;
                                        if ($anciencd != 0 && $anciencd != null)
                                        {
                                            $chefrg = $anciencd;
                                        }
                                        if ($chefcd != 0 && $chefcd != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["cdCom" => $chefcd]);
                                            
                                        }
                                    }
                                }
                                
                                // Confirmer que telle commission a été attribuer au manager et ne fera plus objet de réatribution
                                DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["statutEnc" => 1]);
                            }
                        // }
                    }

                    // vérifie s'il est un inspecteur
                    $checkins = Fonction::CheckIns($comm->Apporteur);
    
                    if ($checkins) {
    
                        $commercial = DB::table('contrats')->where('police', $comm->NumPolice)->first()->Agent;
    
                        $data_cons = DB::table('commerciauxes')->where('codeCom', $commercial)->first();
    
                        /* if($data_cons->codeInspection == "" || $data_cons->codeInspection == null) {
                            array_push($temp_commerciaux, $data_cons->codeCom);
                            $error += 1;
                        }else { */
                            if($comm->statutEnc == 0){
                                
                                if ($data_cons->codeRegion != "" && $data_cons->codeRegion != null) {
                                    // Code commercial du Chef Région
                                    if(isset(DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH)){
                                        $chefrg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH;
                                        
                                        // Controle d'ancien rg 
                                        $ancienrg = DB::table('contrats')->where('police', $comm->NumPolice)->first()->rg;
                                        if ($ancienrg != 0 && $ancienrg != null)
                                        {
                                            $chefrg = $ancienrg;
                                        }
                                        if ($chefrg != 0 && $chefrg != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["regionCom" => $chefrg]);
                                            
                                        }
                                    }
                                }
                                
                                if ($data_cons->codeCD != "" && $data_cons->codeCD != null) {
                                    // Code commercial du Chef Coordination
                                    if(isset(DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $data_cons->codeCD)->first()->managerH)){
                                        $chefcd = DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $data_cons->codeCD)->first()->managerH;
                                        // Controle d'ancien cd 
                                        $anciencd = DB::table('contrats')->where('police', $comm->NumPolice)->first()->cd;
                                        if ($anciencd != 0 && $anciencd != null)
                                        {
                                            $chefrg = $anciencd;
                                        }
                                        if ($chefcd != 0 && $chefcd != null){
                                            // autoriser l'attribution du solde au sup
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["cdCom" => $chefcd]);
                                            
                                        }
                                    }
                                }
                                
                                // Confirmer que telle commission a été attribuer au manager et ne fera plus objet de réatribution
                                DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["statutEnc" => 1]);
                            }
                        //}
                    }
					
                }
            }
        return json_encode(['response' =>  1, 'message' => "Toutes les manageurs ont été attributer sur chaque commissions calculées."]);
    }
    
    public static function affectationComManag(){
        
        // CEQP
        $commissionceqp = DB::table('commissions')->select('Toc', DB::raw('SUM(MontantCEQ) as ceq'))
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)
                ->groupBy('Toc')
                ->get();
        foreach ($commissionceqp as $comm) {
            Fonction::SetMontantSup($comm->ceq, $comm->Toc);
        }
        // INS
        $commissionins = DB::table('commissions')->select('premiervalidation', DB::raw('SUM(MontantInspecteur) as ins'))
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)
                ->groupBy('premiervalidation')
                ->get();
        foreach ($commissionins as $comm) {
            Fonction::SetMontantSup($comm->ins, $comm->premiervalidation);
        }
        
        // REGION
        $commissionrg = DB::table('commissions')->select('regionCom', DB::raw('SUM(MontantRG) as reg'))
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)
                ->groupBy('regionCom')
                ->get();
        foreach ($commissionrg as $comm) {
            Fonction::SetMontantSup($comm->reg, $comm->regionCom);
        }
        
        // CD
        $commissioncd = DB::table('commissions')->select('cdCom', DB::raw('SUM(MontantCD) as cd'))
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)
                ->groupBy('cdCom')
                ->get();
        foreach ($commissioncd as $comm) {
            Fonction::SetMontantSup($comm->cd, $comm->cdCom);
        }
		
		// Récupérer le ou les sp
        $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "sp")->first();
        $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
        $mails = array();
        foreach ($allAdmin as $value) {
            array_push($mails, $value->mailZ);
        }
            
        SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", []);
        return json_encode(['response' =>  1, 'message' => "Toutes les manageurs ont reçu les com2 dans leur compte."]);
    }

    public function setautrecom(Request $request)
    {
        $codeAgent = DB::table('commissions')->where('NumCommission', $request->codecom)->first()->Apporteur;

        //$codeAgent = DB::table('contrats')->where('police', $police)->first()->Agent;

        $solde = DB::table('compteagents')->where('Agent', $codeAgent)->first()->AutreCommissionMoisCalculer + $request->soldeautre;

         DB::table('compteagents')->where('Agent', $codeAgent)->update([
            'AutreCommissionMoisCalculer' => $solde,
            'libautrecom' => $request->libother
        ]);

        TraceController::setTrace(
                "Vous avez ajouté Autre Commission dont le montant est ".$request->soldeautre." au compte de l'Agent dont le code commercial est ".$codeAgent.".",
                session("utilisateur")->idUser);

        flash('Autres commissions ajoutées avec succès.');
        return Back();
    }

    public function setbonus(Request $request)
    {
        $codeAgent = DB::table('commissions')->where('NumCommission', $request->codecom)->first()->Apporteur;

        //$codeAgent = DB::table('contrats')->where('police', $police)->first()->Agent;

        $solde = DB::table('compteagents')->where('Agent', $codeAgent)->first()->bonus + $request->soldebonus;

        DB::table('compteagents')->where('Agent', $codeAgent)->update([
            'bonus' => $solde,
            'libbonus' => $request->libbonu
        ]);

        TraceController::setTrace("Vous avez ajouté Bonus dont le montant est ".$request->soldebonus." au compte de l'Agent dont le code commercial est ".$codeAgent.".",
                session("utilisateur")->idUser);

        flash('Bonus ajouté avec succès.');
        return Back();
    }

    public function setretenue(Request $request)
    {
        $codeAgent = DB::table('commissions')->where('NumCommission', $request->codecom)->first()->Apporteur;

        //$codeAgent = DB::table('contrats')->where('police', $police)->first()->Agent;

        $solde = DB::table('compteagents')->where('Agent', $codeAgent)->first()->retenue + $request->soldedefalque;

        DB::table('compteagents')->where('Agent', $codeAgent)->update([
            'retenue' => $solde,
            'libretenue' => $request->libretenue
        ]);

        TraceController::setTrace(
                "Vous avez ajouté de retenue dont le montant est ".$request->soldedefalque." au compte de l'Agent dont le code commercial est ".$codeAgent.".",
                session("utilisateur")->idUser);

        flash('Solde retenue ajoutée avec succès.');
        return Back();
    }

    public function importerAutreCommission(){
        // récupérer list apporteur concerné par le calcul de commissionnement et qui bénéficierons des frais
        $list = DB::table('compteagents')
            ->select(
                 'compteagents.Agent as Commercial',
                 'compteagents.AutreCommissionMoisCalculer as autrescom',
                 'compteagents.bonus as bonus',
                 'compteagents.retenue as retenue', 'compteagents.libretenue as libretenue', 'compteagents.libbonus as libbonus',  'compteagents.fixe as fixe', 'compteagents.dotationTelephonie as dotationTelephonie', 
                 'compteagents.dotationCarburant as dotationCarburant' )
			->whereNotIn("Agent", [1, 2, 3])
            ->get();

        $i = 0;
        // préparation du fichier excel
        foreach ($list as $itemApporteur){
            $tabl[$i]["apporteur"] = $itemApporteur->Commercial;
            $tabl[$i]["autrescom"] = $itemApporteur->autrescom;
            $tabl[$i]["bonus"] = $itemApporteur->bonus;
            $tabl[$i]["retenue"] = $itemApporteur->retenue;
            $tabl[$i]["fixe"] = $itemApporteur->fixe;
            $tabl[$i]["telephone"] = $itemApporteur->dotationTelephonie;
            $tabl[$i]["carburant"] = $itemApporteur->dotationCarburant;
            $tabl[$i]["libretenue"] = $itemApporteur->libretenue;
            $tabl[$i]["libbonus"] = $itemApporteur->libbonus;
            $i++;
        }

        $autre = new Collection($tabl);
        Session()->put('autrecommission', $autre);
        // Téléchargement du fichier excel
        return Excel::download(new ExportExcel, 'AutreCommissionExportExcel_'.date('Y-m-d-h-i-s').'.xlsx');

    }

    public function setautreCommission(Request $request){
        // vérification du fichier importer
        if ($request->hasFile('fichie')) {
            $ext  = $request->file('fichie')->getClientOriginalExtension();
            $error = 0; $a = 0;
            $temp = array();
            $tabl = "";
            if(in_array($ext,['xlsx','xls'])){
                $reference = "REF-IMPORTERAUTRECOMMISSION-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichie')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $frais = $tab[0];
                // parcourt ndes données
                for ($i=1; $i < count($frais); $i++){
                    $f = $frais;
                    // Si apporteur existe alors insert les valeurs
                    if (isset(DB::table("compteagents")->where('Agent', $f[$i][0])->first()->Agent)){
                        $autr = $f[$i][1] ;
                        $bonus = $f[$i][2];
                        $d = $f[$i][3];
                        $fixe = $f[$i][4];
                        $dTel = $f[$i][5];
                        $dCarb = $f[$i][6];
                        if($f[$i][1] == "" || $f[$i][1] == null)
                            $autr = 0;
                        if($f[$i][2] == "" || $f[$i][2] == null)
                            $bonus = 0;
                        if($f[$i][3] == "" || $f[$i][3] == null)
                            $d = 0;
                        if($f[$i][4] == "" || $f[$i][4] == null)
                            $fixe = 0;
                        if($f[$i][5] == "" || $f[$i][5] == null)
                            $dTel = 0;
                        if($f[$i][6] == "" || $f[$i][6] == null)
                            $dCarb = 0;
                        DB::table("compteagents")->where('Agent', $f[$i][0])->update([
                            "AutreCommissionMoisCalculer" => intval($autr),
                            "bonus" => intval($bonus),
                            "retenue" => intval($d),
                            "libretenue" => $f[$i][7],
                            "libbonus" => $f[$i][8],
                            "fixe" => intval($fixe),
                            "dotationTelephonie" => intval($dTel),
                            "dotationCarburant" => intval($dCarb),
                        ]);
                    }else{ // sinon récuperer les apporteurs qui n'existent pas
                        $error++;
                        array_push($temp, $f[$i][0]);
                        $tabl[$a]["apporteur"] = $f[$i][0];
                        $tabl[$a]["autre"] = $f[$i][1];
                        $tabl[$a]["bonus"] = $f[$i][2];
                        $tabl[$a]["retenue"] = $f[$i][3];
                        $tabl[$a]["fixe"] = $f[$i][4];
                        $tabl[$a]["telephone"] = $f[$i][5];
                        $tabl[$a]["carburant"] = $f[$i][6];
                        $tabl[$a]["libretenue"] = $f[$i][7];
                        $tabl[$a]["libbonus"] = $f[$i][8];
                        $tabl[$a]["observation"] = "Apporteur n'existe pas";
                        $a++;
                    }
                }

                if($error != 0) {
                    // préparer un fichier excel pour sauvegarder les erreurs
                    $autre = new Collection($tabl);
                    Session()->put('autrecommissionerror', $autre);
                    $message = count($frais) - $a ." ont bénéficié des frais supplémentaires. <br> ";
                    $message .= "Les apporteurs dont les codes n'existent pas. <br> Codes :  ";
                    for ($i=0; $i < count($temp); $i++) {
                        $message .= $temp[$i]. " ; ";
                    }
                    $message .= "<br> <a href=\"{{ route('EIAC')}}\"> Télécharger le fichier d'erreur. </a>";
                    flash($message)->error();
                    return Back();
                }

                $message = "Tous les apporteurs ont bénéficié des frais supplémentaires.";
                TraceController::setTrace(
                "Vous avez importé des frais supplémentaires.",
                session("utilisateur")->idUser);
                flash($message)->success();
                return Back();
            }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }
        }else{
            flash('Aucun fichier importé')->error();
            return Back();
        }
    }

    public function geterrorautrecommission(){
        if (session("autrecommissionerror") != ""){
            return Excel::download(new ExportErreurAutreCommission, 'AutreCommissionExportExcel_'.date('Y-m-d-h-i-s').'.xlsx');
        }
    }

    /**
     *  Get && Set Validation SP
     * */
    public function confirmercalcul(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', 'oui')
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where("commissions.Statut", view()->shared('periode'))
            ->where('confirmercalcule', null)->get();
        $temp_commerciaux = array();
        $error = 0;
        $temp_chef = array();
        
        if (isset($commission) && sizeof($commission) != 0) {
                
            
            // Total des commissions
            // Liste des Apporteurs concernés par les commissions de ce mois
            $commissionTotal = DB::table('commissions')
                    ->where('statutcalculer', 'oui')
                    ->where('confirmercalcule', null)
                    ->where("ctrl", 1)
                    ->where('TypeCommission', 'i')
                    ->where("commissions.Statut", view()->shared('periode'))
                    ->distinct('commissions.Apporteur')->get();
                
            foreach ($commissionTotal as $com){
                    // Vérifier si le commercial est un conseiller
                    //Avec le conseiller, récupérer le chef de son équipe, de son inspection et de son région pour leurs accordés leur commission
    
                    // vérifie s'il est un conseiller
                    $checkcons = Fonction::CheckCons($com->Apporteur);
    
                    if ($checkcons) {
    
                        $commercial = $com->Apporteur;
    
                        $data_cons = DB::table('commerciauxes')->where('codeCom', $commercial)->first();
    
                        if($data_cons->codeEquipe == "") {
                            /******* CONS *******/
    
                            // récuperer le taux aib
                            $tauxaibcons = Fonction::RecupererTaux($commercial);
    
                            // total des montants temporaire
                            //Montant cons
    
                            $montantcons = Fonction::RecupererCompte($commercial);
                            if($montantcons->statueValide == 0){
                                $temp_CONS = $montantcons->compteMoisCalculer + $montantcons->AutreCommissionMoisCalculer + 
                                $montantcons->bonus  + $montantcons->fixe ; 
                                // + $montantcons->dotationTelephonie + $montantcons->dotationCarburant;
        
                                // Calcul du montant aib
                                $mont_aib_cons = $temp_CONS * $tauxaibcons / 100;
        
                                // Calcul du montant net à payer
                                $mont_cons = ($temp_CONS + $montantcons->compteBloquer) - $mont_aib_cons;
        
                                // Set montant net payer
                                DB::table('compteagents')->where('Agent', $commercial)->update([
                                    'compteNetapayerMoisCalculer' => $mont_cons,
                                    'aibMoisCalculer' => $mont_aib_cons
                                ]);
        
                                // Verser net à payer dans le compte
                                Fonction::setCompte($commercial, $mont_cons);
								
								// retenue
                                Fonction::solderretenue($commercial);
                                
                                // naf
                                Fonction::soldernaf($commercial);
                                
                                // avance
                                Fonction::avancesolder($commercial);
                                
                                // carec
                                Fonction::reglementcarec($commercial);
                                
                                // amical
                                Fonction::reglementamical($commercial);
                            }
                            array_push($temp_commerciaux, $data_cons->codeCom);
    
                            $error += 1;
                        }else{
                            // Code commercial du Chef Equipe
                            $chefequipe= 0;
                            if($data_cons->codeEquipe != "" && $data_cons->codeEquipe != null){
                                if(isset(DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH))
                                $chefequipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH;
                            }
                            
                            // Code commercial du Chef Inspection
                            $chefins = 0;
                            if ($data_cons->codeInspection != "" && $data_cons->codeInspection != null){
                                if(isset(DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH))
                                $chefins = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH;
                            }
    
                            // Code commercial du Chef Région
                            $chefrg = 0;
                            if ($data_cons->codeRegion != "" && $data_cons->codeRegion != null){
                                if(isset(DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH))
                                $chefrg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH;
                            }
                            /******* CONS *******/
                            
                            // récuperer le taux aib
                            $tauxaibcons = Fonction::RecupererTaux($commercial);
    
                            $montantcons = Fonction::RecupererCompte($commercial);
                            if($montantcons->statueValide == 0){
                                $temp_CONS = $montantcons->compteMoisCalculer + $montantcons->AutreCommissionMoisCalculer + 
                                $montantcons->bonus + $montantcons->fixe;
                                //  + $montantcons->dotationTelephonie + $montantcons->dotationCarburant;
        
                                // Calcul du montant aib
                                $mont_aib_cons = $temp_CONS * $tauxaibcons / 100;
        
                                // Calcul du montant net à payer
                                $mont_cons = ($temp_CONS + $montantcons->compteBloquer) - $mont_aib_cons;
        
                                // Règlement des retenues
                                //$mont_net_cons = $mont_cons - $montantcons->retenue;
                                
                                // Set montant net payer
                                DB::table('compteagents')->where('Agent', $commercial)->update([
                                    'compteNetapayerMoisCalculer' => $mont_cons,
                                    'aibMoisCalculer' => $mont_aib_cons
                                ]);
        
                                // Verser net à payer dans le compte
                                Fonction::setCompte($commercial, $mont_cons);
								
								// retenue
                                Fonction::solderretenue($commercial);
                                
                                // naf
                                Fonction::soldernaf($commercial);
                                
                                // avance
                                Fonction::avancesolder($commercial);
                                
                                // carec
                                Fonction::reglementcarec($commercial);
                                
                                // amical
                                Fonction::reglementamical($commercial);
                            }
                        }
                    }
            }
            
            // Mise à jour de commission pour confirmer calcule
    
                    DB::table('commissions')
                        ->where('statutcalculer', 'oui')
                        ->where('confirmercalcule', null)
                        ->where("ctrl", 1)
                        ->where('TypeCommission', 'i')
                        ->where("commissions.Statut", view()->shared('periode'))
                        ->update([
                            "confirmercalcule" => "oui"
                        ]);
            
            /*******************************/
                
                $allmanager = DB::table('hierarchies')->select('managerH')->where('managerH', '!=', 0)->get();
                $listmang = array();
                foreach ($allmanager as $manag) {
                    $data = DB::table('compteagents')->select('Agent')->where('statueValide', 0)->where('Agent', $manag->managerH)->first();
                    if(isset($data))
                            if(!in_array($manag->managerH , $listmang))
                                array_push($listmang, $manag->managerH );
                    
                }
                
                $listAgents = DB::table('compteagents')->select('Agent')->where("fixe", "!=", 0)->orwhere("bonus", "!=", 0)->orwhere("AutreCommissionMoisCalculer", "!=", 0)
                ->orwhere("dotationCarburant", "!=", 0)->orwhere("dotationTelephonie", "!=", 0)->get();
                
                
                foreach ($listAgents as $agentbenef) {
                    $data = DB::table('compteagents')->select('Agent')->where('statueValide', 0)->where('Agent', $agentbenef->Agent)->first();
                    if(isset($data))
                            if(!in_array($agentbenef->Agent , $listmang))
                                array_push($listmang, $agentbenef->Agent );
                }
                if(count($listmang) !=0)
                    for($i=0; $i < count($listmang); $i++){
                    $managco =  $listmang[$i];
                    $statut = DB::table('compteagents')->where('statut', 0)->first();
                    if($managco != 0 && $managco != null && isset($statut->Agent)){
                                // récuperer le taux aib
                                $tauxaibchefequipe = Fonction::RecupererTaux($managco);
    
                                // total des montants temporaire
    
                                // Montant Chef Equipe
                                $montantchefequipe = Fonction::RecupererCompte($managco);
                                if($montantchefequipe->statueValide == 0){
                                    
                                    $temp_ChefEquipe = $montantchefequipe->compteMoisCalculer + 
                                    $montantchefequipe->compteEncadrementMoisCalculer + 
                                    $montantchefequipe->AutreCommissionMoisCalculer + 
                                    $montantchefequipe->bonus  + $montantchefequipe->fixe; 
                                    //  + $montantchefequipe->dotationTelephonie + $montantchefequipe->dotationCarburant;
        
                                    // Calcul du montant aib
                                    $mont_aib_eqp = $temp_ChefEquipe * $tauxaibchefequipe / 100;
        
                                    // Calcul du montant net à payer
                                    $mont_eqp = ($temp_ChefEquipe  + $montantchefequipe->compteBloquer) - $mont_aib_eqp;
        
                                    // Règlement des retenues
                                    //$mont_net_eqp = $mont_eqp - $montantchefequipe->retenue;
        
                                    // Set montant net payer
                                    DB::table('compteagents')->where('Agent', $managco)->update([
                                        'compteNetapayerMoisCalculer' => $mont_eqp,
                                        'aibMoisCalculer' => $mont_aib_eqp
                                    ]);
        
                                     // Verser net à payer dans le compte
                                    Fonction::setCompte($managco, $mont_eqp);
									
									// retenue
                                	Fonction::solderretenue($managco);
                                    
                                    // naf
                                    Fonction::soldernaf($managco);
                                    
                                    // avance
                                    Fonction::avancesolder($managco);
                                    
                                    // carec
                                    Fonction::reglementcarec($managco);
                                    
                                    // amical
                                    Fonction::reglementamical($managco);
                                
                                }
                            }
                }
                
                
            
                /*******************************/
                
                
                // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = $request->comment;
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = view()->shared('periode');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save(); 

            
                // Envoi de mail
    
                // Récupérer le ou les sp
                $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "csp")->first();
                
                    $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                    $mails = array();
                    foreach ($allAdmin as $value) {
                        array_push($mails, $value->mailZ);
                    }
                    SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "sp");
                
                $message = "";
                if ($error != 0){
                    //dd(count($temp_commerciaux));
                    $message .= "Les commerciaux dont les codes commerciaux suivants ne sont pas dans une hiérarchie. <br> Codes :  ";
                    $temp = array();
                    for ($i=0; $i < count($temp_commerciaux); $i++) {
                        if (!in_array($temp_commerciaux[$i], $temp )) {
                            $message .= $temp_commerciaux[$i]. " ; ";
                            array_push($temp, $temp_commerciaux[$i]);
                        }
                    }
                    flash($message)->error();
                    
                }
    
                flash("Commissions confirmées avec succès.");
                TraceController::setTrace($message.". Vous avez confirmé les commissions de ce mois.", session("utilisateur")->idUser);
                ProcessusComInd::saveprocessus("sp");
                return $message.". Commissions confirmées avec succès.";
                return redirect()->route('listCom');
            
            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return redirect()->route('listCom');
            }
    }
    
    public function setrejetsp(){
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // Réinitialiser les comptes agents 
            DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')->update([
                    "statutcalculer" => null,
                    "ncom" => 0,
                    "MontantConseiller" => 0,
                    "MontantCEQ" => 0,
                    "MontantInspecteur" => 0,
                    "MontantRG" => 0,
                    "bareme" => "",
                    "ctrl" => 0,
                ]);
            DB::table("compteagents")->update([
                    "compteMoisCalculer" => 0,
                    "MoisCalculer" => "",
                ]);
            /*
            foreach ($commission as $comm) {
                
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "statutcalculer" => null,
                    "ncom" => 0,
                    "MontantConseiller" => 0,
                    "MontantCEQ" => 0,
                    "MontantInspecteur" => 0,
                    "MontantRG" => 0,
                    "bareme" => "",
                    "ctrl" => 0,
                ]);
                
                DB::table("compteagents")->update([
                    "compteMoisCalculer" => 0,
                    "MoisCalculer" => "",
                ]);
            } */

            // Envoi de mail
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                $mail = $allAdmin[0]->mailZ;
                foreach ($allAdmin as $value) {
                    if($mail != $value->mailZ)
                        array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($mail, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetsp");
            
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', view()->shared('periode'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', view()->shared('periode'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions de ce mois.");
            TraceController::setTrace("Vous avez rejeté les commissions de ce mois.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('listCom');
            
        }else{
            // Pas de commission disponible
            flash(" Pas de commission disponible pour ce mois!!! ");
            return redirect()->route('listCom');
        }
        
    }

    /**
     *  Get && Set Validation CSP
     */

    public function getvalidationcsp(){
        $verif = DB::table('processuscominds')
                ->where("sp", 1)
                ->where('csp', 0)
                ->where('mois', view()->shared('periode'))
                ->first();
        $list = array(); 
        if(isset($verif->idpci))
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101])->get();
            
            
        $libelleRole = "CSP";

        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;

        $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->first(); 
        $signaturesp = "";
        if(isset($signature->pathSignataire))
            $signaturesp = $signature->pathSignataire;
        $search = "Rechercher";
        if(request('rec') == 1){
            // Liste des commissions concernés
            $commission = DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', null)
                ->where('TypeCommission', 'i')
                ->where("ctrl", 1)
                ->where("commissions.Statut", view()->shared('periode'))
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('Agent', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commission.listcomcsp', compact('list','libelleRole','sigle', 'search', 'signaturesp'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commission.listcomcsp", compact('list','libelleRole','sigle', 'search', 'signaturesp'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }

        //$list = $list->paginate(10000);

        return view('commission.listcomcsp', compact('list', 'libelleRole', 'sigle', 'signaturesp', 'search'));
    }
    
    public function setrejetcsp(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmercsp', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // réinitialisé les compteagents concernés
            $agents = DB::table('compteagents')->select('compteagents.Agent as Apporteur')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101])->get();
            foreach ($agents as $agent) {
                $compte = DB::table('compteagents')->where('Agent', $agent->Apporteur)->first();
                if($compte->avancesancien <> 0)
                {
                    if($compte->compteBloquer == 0){
                        $avance = ($compte->avancesancien / ($compte->duree + 1)) + $compte->avances;
                        $dure = $compte->duree + 1;
                        
                        
                        DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                            'aibMoisCalculer' => 0,
                            'compteNetapayerMoisCalculer' => 0,
                            'compteEncadrementMoisCalculer' => 0,
                            'compte' => 0,
                            'avances' => $avance,
                            'duree' => $dure,
                            'statueValide' => 0,
                            'statutEng' => 0,
                            'recentrembourcer' => 0
                        ]);    
                    }else{
                        
                        DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                            'aibMoisCalculer' => 0,
                            'compteNetapayerMoisCalculer' => 0,
                            'compteEncadrementMoisCalculer' => 0,
                            'compte' => 0,
                            'statueValide' => 0,
                            'statutEng' => 0,
                            'recentrembourcer' => 0
                        ]); 
                    }
                    
                }else{
                    DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                        'aibMoisCalculer' => 0,
                        'compteNetapayerMoisCalculer' => 0,
                        'compteEncadrementMoisCalculer' => 0,
                        'compte' => 0,
                        'statutEng' => 0,
                        'statueValide' => 0,
                        'tracesAmical' => 0
                    ]);
                }
                
                // Vérifier si son compte était bloquer et restaurer
                if($compte->compteBloquerBackup <> 0){
                    DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                        'statueValide' => 0,
                        'compteBloquer' => $compte->compteBloquerBackup
                    ]);
                }
                
                        if($compte->tracesAmical != 0){
                            $dur = $compte->dureeencourAmical + 1;
                            $compteA = $compte->compteAmical - $compte->tracesAmical;
                            DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                                'tracesAmical' => 0,
                                'dureeencourAmical' => $dur,
                                'compteAmical' => $compteA
                            ]); 
                        }
                        
                        if($compte->traceCarec != 0){
                            $durC = $compte->dureeencourCarec + 1;
                            $compteC = $compte->compteCarec - $compte->traceCarec;
                            DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                                'traceCarec' => 0,
                                'dureeencourCarec' => $durC,
                                'compteCarec' => $compteC
                            ]); 
                        }
                
            }
            
            // mettre à jour la table commission par null
            DB::table('commissions')->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmercsp', null)->update([
                    "confirmercalcule" => null
                ]);
                
            
            /*foreach ($commission as $comm) {
                
            } */

            // Envoi de mail
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "sp")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetcsp");
            //}
        
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', view()->shared('periode'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', view()->shared('periode'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions de ce mois.");
            TraceController::setTrace("Vous avez rejeté les commissions de ce mois.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCCSP');

        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCCSP');
        }
    }

    public function setvalidationcsp(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel
        
        //CommissionController::controleCommercialDETAIl();
            
        //CommissionController::controleCommercialDETAIlResum();
            
          //  dd("Bon");

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;
            
            DB::table('commissions')->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->update([
                    "confirmercsp" => "oui"
            ]);
            
            /*foreach ($commission as $comm) {

                // mettre à jour la table commission par oui

                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmercsp" => "oui"
                ]);

                $a++;

            }
            $a--; */

            // Enregistrer dans la table Signataire
            $addSignataire = new Signataire();
            $addSignataire->idSignataire = session('utilisateur')->idUser;
            $addSignataire->CodeCommission = sizeof($commission);
            $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
            $addSignataire->pathSignataire = session('utilisateur')->signature;
            $addSignataire->DateCalculer = view()->shared('periode');
            $addSignataire->RoleSignataire = session('utilisateur')->Role;
            $addSignataire->save();

            // Envoi de mail

            // Récupérer le ou les dt
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "dt")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mailZ);
            }
            
            SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "csp");
            //}
            
            //CommissionController::controleCommercialDETAIl();
            
            //CommissionController::controleCommercialDETAIlResum();

            flash("Vous avez confirmé les commissions de ce mois.");
            TraceController::setTrace("Vous avez confirmé les commissions de ce mois.",session("utilisateur")->idUser);
            ProcessusComInd::saveprocessus("csp");
            //return Back();
            return redirect()->route('GCCSP');

        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCCSP');
        }
    }
    
    public function controleCommercialDETAIl(){
        
        $listIns = DB::table('hierarchies')->whereIn("structureH", ["INS", "FV", "RG", "CD"])->where("managerH", '!=', null)->where("managerH", '!=', 0)->where("managerH", '!=', 3)->get();
        
        foreach($listIns as $conseiller)
        {
            //dd($conseiller);
            // Code commercial inspecteurs
            $dataCom = DB::table('commerciauxes')->where('codeCom', $conseiller->managerH)->first();
            
            if(isset($dataCom->codeCom))
            {
                $list = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                        ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                        ->where('commissions.Statut', view()->shared('periode'))
                        ->where('statutcalculer', "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('ctrl', 1);
                
                // Cas d'un inspecteur
                if($dataCom->Niveau == "INS" || $dataCom->Niveau == "FV"){
                    /***********************************Inspecteur******************************************************/
                    Session()->put('commissiondetailcontrole', null);
                    $listIns = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                        ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                        ->where('commissions.Statut', view()->shared('periode'))
                        ->where('statutcalculer', "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('ctrl', 1)->where('Apporteur', $dataCom->codeCom)->get();
                    
                    $titre = "INSPECTEUR : ".$dataCom->codeCom;
                    $tabl = array();
                    $tabl[0]["codeApp"] = "";
                    $tabl[0]["nomApp"] = "";
                    $tabl[0]["cat"] = $titre;
                    $tabl[0]["police"] = "";
                    $tabl[0]["quittance"] = "";
                    $tabl[0]["idpayeur"] = ' ';
                    $tabl[0]["periode"] = "";
                    $tabl[0]["base"] = "";
                    $tabl[0]["commission"] = "";
                    $tabl[0]["chefequipe"] = "";
                    $tabl[0]["chefins"] = "";
                    $tabl[0]["chefrg"] = "";
                    $tabl[0]["chefcd"] = "";
                    $sombase = 0;
                    $somcom = 0;
                    $somequi = 0;
                    $somins = 0;
                    $somrg = 0;
                    $somcd = 0;
                    $i = 1;
                    
                    foreach ($listIns as $value){
                        $client = DB::table('clients')->where('idClient', $value->Client)->first();
                        $tabl[$i]["codeApp"] = $value->Apporteur;
                        $tabl[$i]["nomApp"] = $value->nomCom.' '.$value->prenomCom;
                        $tabl[$i]["cat"] = $value->Niveau;
                        $tabl[$i]["police"] = $value->NumPolice;
                        $tabl[$i]["quittance"] = $value->NumQuittance;
                        $payeur = DB::table('clients')->where('idClient', $client->Payeur)->first();
                        if(isset($payeur->nom)){
                            $tabl[$i]["idpayeur"] = $payeur->idClient;
                        }
                        else{
                            $tabl[$i]["idpayeur"] = ' ';
                        }
                        $tabl[$i]["periode"] = $value->Statut;
                        $tabl[$i]["base"] = $value->BaseCommission;
                        $tabl[$i]["commission"] = $value->MontantConseiller;
                        $tabl[$i]["chefequipe"] = $value->MontantCEQ;
                        $tabl[$i]["chefins"] = $value->MontantInspecteur;
                        $tabl[$i]["chefrg"] = $value->MontantRG;
                        $tabl[$i]["chefcd"] = $value->MontantCD;
                        $sombase += $value->BaseCommission;
                        $somcom += $value->MontantConseiller;
                        $somequi += $value->MontantCEQ;
                        $somins += $value->MontantInspecteur;
                        $somrg += $value->MontantRG;
                        $somcd += $value->MontantCD;
                        $i++;
                    }
                    $i++;
                    $tabl[$i]["codeApp"] = "";
                    $tabl[$i]["nomApp"] = "";
                    $tabl[$i]["cat"] = "";
                    $tabl[$i]["police"] = "";
                    $tabl[$i]["quittance"] = "";
                    $tabl[$i]["idpayeur"] = ' ';
                    $tabl[$i]["periode"] = "Total :";
                    $tabl[$i]["base"] = $sombase;
                    $tabl[$i]["commission"] = $somcom;
                    $tabl[$i]["chefequipe"] = $somequi ;
                    $tabl[$i]["chefins"] = $somins;
                    $tabl[$i]["chefrg"] = $somrg;
                    $tabl[$i]["chefcd"] = $somcd;
                    
                    
                    /****************************Equipe**************************************/
                    
                    $allmembres = DB::table('commerciauxes')->where('Niveau', 'CEQP')->where('codeInspection', $dataCom->codeInspection)->get();
                    
                    foreach($allmembres as $com)
                    {
                       // Cas d'un chef d'équipe
                        if($com->Niveau == "CEQP"){
                            
                            
                            $listEqui = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                        ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                        ->where('commissions.Statut', view()->shared('periode'))
                        ->where('statutcalculer', "oui")
                        ->where('confirmercalcule', "oui")
                        ->where('confirmercsp', "oui")
                        ->where('ctrl', 1)->where('Apporteur', $com->codeCom)->get();
                            
                            $titreE = $titre."| Equipe : ".$com->codeCom;
                    
                            $i++;
                            $tabl[$i]["codeApp"] = "";
                            $tabl[$i]["nomApp"] = "";
                            $tabl[$i]["cat"] = "";
                            $tabl[$i]["police"] = "";
                            $tabl[$i]["quittance"] = "";
                            $tabl[$i]["idpayeur"] = ' ';
                            $tabl[$i]["periode"] = "";
                            $tabl[$i]["base"] = "";
                            $tabl[$i]["commission"] = "";
                            $tabl[$i]["chefequipe"] = "" ;
                            $tabl[$i]["chefins"] = "";
                            $tabl[$i]["chefrg"] = "" ;
                            $tabl[$i]["chefcd"] = "";
                            
                            $i++;
                            $tabl[$i]["codeApp"] = "";
                            $tabl[$i]["nomApp"] = "";
                            $tabl[$i]["cat"] = $titreE;
                            $tabl[$i]["police"] = "";
                            $tabl[$i]["quittance"] = "";
                            $tabl[$i]["idpayeur"] = ' ';
                            $tabl[$i]["periode"] = "";
                            $tabl[$i]["base"] = "";
                            $tabl[$i]["commission"] = "";
                            $tabl[$i]["chefequipe"] = "" ;
                            $tabl[$i]["chefins"] = "";
                            $tabl[$i]["chefrg"] = "" ;
                            $tabl[$i]["chefcd"] = "";
                            $sombaseE = 0;
                            $somcomE = 0;
                            $somequiE = 0;
                            $sominsE = 0;
                            $somrgE = 0;
                            $somcdE = 0;
                            $i++;
                            foreach ($listEqui as $value){
                                $client = DB::table('clients')->where('idClient', $value->Client)->first();
                                $tabl[$i]["codeApp"] = $value->Apporteur;
                                $tabl[$i]["nomApp"] = $value->nomCom.' '.$value->prenomCom;
                                $tabl[$i]["cat"] = $value->Niveau;
                                $tabl[$i]["police"] = $value->NumPolice;
                                $tabl[$i]["quittance"] = $value->NumQuittance;
                                $payeur = DB::table('clients')->where('idClient', $client->Payeur)->first();
                                if(isset($payeur->nom)){
                                    $tabl[$i]["idpayeur"] = $payeur->idClient;
                                }
                                else{
                                    $tabl[$i]["idpayeur"] = ' ';
                                }
                                $tabl[$i]["periode"] = $value->Statut;
                                $tabl[$i]["base"] = $value->BaseCommission;
                                $tabl[$i]["commission"] = $value->MontantConseiller;
                                $tabl[$i]["chefequipe"] = $value->MontantCEQ;
                                $tabl[$i]["chefins"] = $value->MontantInspecteur;
                                $tabl[$i]["chefrg"] = $value->MontantRG;
                                $tabl[$i]["chefcd"] = $value->MontantCD;
                                $sombaseE += $value->BaseCommission;
                                $somcomE += $value->MontantConseiller;
                                $somequiE += $value->MontantCEQ;
                                $sominsE += $value->MontantInspecteur;
                                $somrgE = $value->MontantRG;
                                $somcdE = $value->MontantCD;
                                $i++;
                            }
                            $i++;
                            $tabl[$i]["codeApp"] = "";
                            $tabl[$i]["nomApp"] = "";
                            $tabl[$i]["cat"] = "";
                            $tabl[$i]["police"] = "";
                            $tabl[$i]["quittance"] = "";
                            $tabl[$i]["idpayeur"] = ' ';
                            $tabl[$i]["periode"] = "Total :";
                            $tabl[$i]["base"] = $sombaseE;
                            $tabl[$i]["commission"] = $somcomE;
                            $tabl[$i]["chefequipe"] = $somequiE;
                            $tabl[$i]["chefins"] = $sominsE;
                            $tabl[$i]["chefrg"] = $somrgE;
                            $tabl[$i]["chefcd"] = $somcdE;
                            
                            /******************************Conseiller****************************/
                            $allmembress = DB::table('commerciauxes')->where('codeEquipe', $com->codeEquipe)->get();
                        
                            foreach($allmembress as $comm)
                            {
                                // Cas des conseillers
                                if($comm->Niveau != "CEQP"){
                                    $listCons = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                                    ->where('commissions.Statut', view()->shared('periode'))
                                    ->where('statutcalculer', "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('ctrl', 1)->where('Apporteur', $comm->codeCom)->get();
                            
                                    $titreC = $titreE."| Conseiller : ".$comm->codeCom;
                            
                                    $i++;
                                    $tabl[$i]["codeApp"] = "";
                                    $tabl[$i]["nomApp"] = "";
                                    $tabl[$i]["cat"] = "";
                                    $tabl[$i]["police"] = "";
                                    $tabl[$i]["quittance"] = "";
                                    $tabl[$i]["idpayeur"] = ' ';
                                    $tabl[$i]["periode"] = "";
                                    $tabl[$i]["base"] = "";
                                    $tabl[$i]["commission"] = "";
                                    $tabl[$i]["chefequipe"] = "" ;
                                    $tabl[$i]["chefins"] = "";
                                    $tabl[$i]["chefrg"] = "";
                                    $tabl[$i]["chefcd"] = "";
                                    
                                    $i++;
                                    $tabl[$i]["codeApp"] = "";
                                    $tabl[$i]["nomApp"] = "";
                                    $tabl[$i]["cat"] = $titreC;
                                    $tabl[$i]["police"] = "";
                                    $tabl[$i]["quittance"] = "";
                                    $tabl[$i]["idpayeur"] = ' ';
                                    $tabl[$i]["periode"] = "";
                                    $tabl[$i]["base"] = "";
                                    $tabl[$i]["commission"] = "";
                                    $tabl[$i]["chefequipe"] = "" ;
                                    $tabl[$i]["chefins"] = "";
                                    $tabl[$i]["chefrg"] = "";
                                    $tabl[$i]["chefcd"] = "";
                                    $sombaseC = 0;
                                    $somcomC = 0;
                                    $somequiC = 0;
                                    $sominsC = 0;
                                    $somrgC = 0;
                                    $somcdC = 0;
                                    $i++;
                                    foreach ($listCons as $value){
                                        $client = DB::table('clients')->where('idClient', $value->Client)->first();
                                        $tabl[$i]["codeApp"] = $value->Apporteur;
                                        $tabl[$i]["nomApp"] = $value->nomCom.' '.$value->prenomCom;
                                        $tabl[$i]["cat"] = $value->Niveau;
                                        $tabl[$i]["police"] = $value->NumPolice;
                                        $tabl[$i]["quittance"] = $value->NumQuittance;
                                        $payeur = DB::table('clients')->where('idClient', $client->Payeur)->first();
                                        if(isset($payeur->nom)){
                                            $tabl[$i]["idpayeur"] = $payeur->idClient;
                                        }
                                        else{
                                            $tabl[$i]["idpayeur"] = ' ';
                                        }
                                        $tabl[$i]["periode"] = $value->Statut;
                                        $tabl[$i]["base"] = $value->BaseCommission;
                                        $tabl[$i]["commission"] = $value->MontantConseiller;
                                        $tabl[$i]["chefequipe"] = $value->MontantCEQ;
                                        $tabl[$i]["chefins"] = $value->MontantInspecteur;
                                        $tabl[$i]["chefrg"] = $value->MontantRG;
                                        $tabl[$i]["chefcd"] = $value->MontantCD;
                                        $sombaseC += $value->BaseCommission;
                                        $somcomC += $value->MontantConseiller;
                                        $somequiC += $value->MontantCEQ;
                                        $sominsC += $value->MontantInspecteur;
                                        $somrgC += $value->MontantRG;
                                        $somcdC += $value->MontantCD;
                                        $i++;
                                    }
                                    $i++;
                                    $tabl[$i]["codeApp"] = "";
                                    $tabl[$i]["nomApp"] = "";
                                    $tabl[$i]["cat"] = "";
                                    $tabl[$i]["police"] = "";
                                    $tabl[$i]["quittance"] = "";
                                    $tabl[$i]["idpayeur"] = ' ';
                                    $tabl[$i]["periode"] = "Total :";
                                    $tabl[$i]["base"] = $sombaseC;
                                    $tabl[$i]["commission"] = $somcomC;
                                    $tabl[$i]["chefequipe"] = $somequiC;
                                    $tabl[$i]["chefins"] = $sominsC;
                                    $tabl[$i]["chefrg"] = $somrgC;
                                    $tabl[$i]["chefcd"] = $somcdC;
                                }
                            }
                            
                            /****************************** Fin Conseiller****************************/
                            
                            /****************************fin Equipe******************************/
                        }
                    }
                   
                    /**************************** Fin Inspecteur*******************************/
                    
                    
                    
                    // Exporter tous les commissions 
                    
                    // Création du fichier Excel
                    $path = "Commission_detail_du_".view()->shared('periode')."_".$dataCom->codeCom.".xlsx";
                    
                    $autre = new Collection($tabl);
                    Session()->put('commissiondetailcontrole', $autre);
                    Excel::store(new ExportCommissionControleDETAIL, $path, 'excelstore');
                    $data = ["pat" => $path];
					
					if($dataCom->codeRegion != "" || $dataCom->codeRegion != null){
						
						if(isset(DB::table('hierarchies')->where("codeH", $dataCom->codeRegion)->first()->managerH)){
							$mag = DB::table('hierarchies')->where("codeH", $dataCom->codeRegion)->first()->managerH;

							$email = DB::table('commerciauxes')->where('codeCom', $mag)->first()->mail;
							SendMail::sendFichecontr($dataCom->mail, "Détail Commission par Inspection : Inspection de ".$dataCom->nomCom.' '.$dataCom->prenomCom, $data, $email);
						}
					}else{
						SendMail::sendFichecontr($dataCom->mail, "Détail Commission par Inspection : Inspection de ".$dataCom->nomCom.' '.$dataCom->prenomCom, $data);
					}
                    
                }
               
            }
            Session()->put('commissiondetailcontrole', null);
        }
        
        return 1;
        
    }
    
    public function controleCommercialDETAIlResum(){
        $listIns = DB::table('hierarchies')->whereIn("structureH", ["INS", "FV", "RG", "CD"])->where("managerH", '!=', null)->where("managerH", '!=', 0)->where("managerH", '!=', 3)->get();
        
        foreach($listIns as $conseiller)
        {
            // Code commercial inspecteurs
            $dataCom = DB::table('commerciauxes')->where('codeCom', $conseiller->managerH)->first();
            
            if(isset($dataCom->codeCom))
            {
                //$list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->where('tracecomptes.moiscalculer', view()->shared('periode'));
                
                // Cas d'un inspecteur
                if($dataCom->Niveau == "INS" || $dataCom->Niveau == "FV"){
                    Session()->put('commissioncontrlere', null);
                    /***********************************Inspecteur******************************************************/
                    
                    $compteA = DB::table('compteagents')->where('Agent', $dataCom->codeCom)->first();
                    
                    $titre = "INSPECTEUR : ".$dataCom->nomCom." ".$dataCom->prenomCom;
                    
                    $tabl[0]["code"] = "";
                    $tabl[0]["apporteur"] = $titre;
                    $tabl[0]["ifu"] = "";
                    $tabl[0]["email"] = "";
                    $tabl[0]["brute"] = "";
                    $tabl[0]["enc"] = "";
                    $tabl[0]["autrcom"] = "";
                    $tabl[0]["bonus"] = "";
                    $tabl[0]["fixe"] = "";
                    $tabl[0]["deduire"] = "";
                    $tabl[0]["avanceanc"] = "";
                    $tabl[0]["recenrenc"] = "";
                    $tabl[0]["anticenc"] = "";
                    $tabl[0]["echeance"] = "";
                    $tabl[0]["tauxaib"] = "";
                    $tabl[0]["aib"] = "";
                    $tabl[0]["retenue"] = "";
                    $tabl[0]["carec"] = "";
                    $tabl[0]["amical"] = "";
                    $tabl[0]["naf"] = "";
                    $tabl[0]["nette"] = "";
                    $tabl[0]["peri"] = "";
                    $i = 1;
                    
                        $tabl[$i]["code"] = $dataCom->codeCom;
                        $tabl[$i]["apporteur"] = $dataCom->nomCom.' '.$dataCom->prenomCom;
                        $tabl[$i]["ifu"] = $dataCom->AIB;
                        $tabl[$i]["email"] = $dataCom->mail;
                        $tabl[$i]["brute"] = number_format($compteA->compteMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["enc"] = number_format($compteA->compteEncadrementMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["autrcom"] = number_format($compteA->AutreCommissionMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["bonus"] = number_format($compteA->bonus, 0, '.', ' ')." CFA";
                        $tabl[$i]["fixe"] = number_format($compteA->fixe, 0, '.', ' ')." CFA";
                        $tabl[$i]["deduire"] = number_format($compteA->compteBloquer, 0, '.', ' ')." CFA";
                        $tabl[$i]["avanceanc"] = number_format($compteA->avancesancien, 0, '.', ' ')." CFA";
                        $tabl[$i]["recenrenc"] = number_format($compteA->recentrembourcer, 0, '.', ' ')." CFA";
                        $tabl[$i]["anticenc"] = number_format($compteA->anticiper, 0, '.', ' ')." CFA";
                        $tabl[$i]["echeance"] = number_format($compteA->duree, 0, '.', ' ')." ";
                        if($dataCom->AIB == 0 || $dataCom->AIB == "" || $dataCom->AIB == null)
                            $tabl[$i]["tauxaib"] = "5 %";
                        else
                            $tabl[$i]["tauxaib"] = "3 %";
                        $tabl[$i]["aib"] = number_format($compteA->aibMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["retenue"] = number_format($compteA->retenue, 0, '.', ' ')." CFA";
                        $tabl[$i]["carec"] = number_format($compteA->traceCarec, 0, '.', ' ')." CFA";
                        $tabl[$i]["amical"] = number_format($compteA->tracesAmical, 0, '.', ' ')." CFA";
                        $tabl[$i]["naf"] = number_format($compteA->tracenaf, 0, '.', ' ')." CFA";
                        $tabl[$i]["nette"] = number_format($compteA->compte, 0, '.', ' ')." CFA";
                        $tabl[$i]["peri"] = $compteA->MoisCalculer;
                        
                    $i++;
                    
                            $tabl[$i]["code"] = "";
                            $tabl[$i]["apporteur"] = "";
                            $tabl[$i]["ifu"] = "";
                            $tabl[$i]["email"] = "";
                            $tabl[$i]["brute"] = "";
                            $tabl[$i]["enc"] = "";
                            $tabl[$i]["autrcom"] = "";
                            $tabl[$i]["bonus"] = "";
                            $tabl[$i]["fixe"] = "";
                            $tabl[$i]["deduire"] = "";
                            $tabl[$i]["avanceanc"] = "";
                            $tabl[$i]["recenrenc"] = "";
                            $tabl[$i]["anticenc"] = "";
                            $tabl[$i]["echeance"] = "";
                            $tabl[$i]["tauxaib"] = "";
                            $tabl[$i]["aib"] = "";
                            $tabl[$i]["retenue"] = "";
                            $tabl[$i]["carec"] = "";
                            $tabl[$i]["amical"] = "";
                            $tabl[$i]["naf"] = "";
                            $tabl[$i]["nette"] = "";
                            $tabl[$i]["peri"] = "";
                            $i++;
                    
                    /****************************Equipe**************************************/
                    
                    $allmembres = DB::table('commerciauxes')->where('Niveau', 'CEQP')->where('codeInspection', $dataCom->codeInspection)->get();
                    
                    foreach($allmembres as $com)
                    {
                        // Cas d'un chef d'équipe
                        if($com->Niveau == "CEQP"){
                            
                            $compteE = DB::table('compteagents')->where('Agent', $com->codeCom)->first();
                            
                            $titreE = $titre." | Equipe : ".$com->nomCom." ".$com->prenomCom;
                            
                            $tabl[$i]["code"] = "";
                            $tabl[$i]["apporteur"] = $titreE;
                            $tabl[$i]["ifu"] = "";
                            $tabl[$i]["email"] = "";
                            $tabl[$i]["brute"] = "";
                            $tabl[$i]["enc"] = "";
                            $tabl[$i]["autrcom"] = "";
                            $tabl[$i]["bonus"] = "";
                            $tabl[$i]["fixe"] = "";
                            $tabl[$i]["deduire"] = "";
                            $tabl[$i]["avanceanc"] = "";
                            $tabl[$i]["recenrenc"] = "";
                            $tabl[$i]["anticenc"] = "";
                            $tabl[$i]["echeance"] = "";
                            $tabl[$i]["tauxaib"] = "";
                            $tabl[$i]["aib"] = "";
                            $tabl[$i]["retenue"] = "";
                            $tabl[$i]["carec"] = "";
                            $tabl[$i]["amical"] = "";
                            $tabl[$i]["naf"] = "";
                            $tabl[$i]["nette"] = "";
                            $tabl[$i]["peri"] = "";
                            
                            $i++;
                            
                            $tabl[$i]["code"] = $com->codeCom;
                            $tabl[$i]["apporteur"] = $com->nomCom.' '.$com->prenomCom;
                            $tabl[$i]["ifu"] = $com->AIB;
                            $tabl[$i]["email"] = $com->mail;
                            $tabl[$i]["brute"] = number_format($compteE->compteMoisCalculer, 0, '.', ' ')." CFA";
                            $tabl[$i]["enc"] = number_format($compteE->compteEncadrementMoisCalculer, 0, '.', ' ')." CFA";
                            $tabl[$i]["autrcom"] = number_format($compteE->AutreCommissionMoisCalculer, 0, '.', ' ')." CFA";
                            $tabl[$i]["bonus"] = number_format($compteE->bonus, 0, '.', ' ')." CFA";
                            $tabl[$i]["fixe"] = number_format($compteE->fixe, 0, '.', ' ')." CFA";
                            $tabl[$i]["deduire"] = number_format($compteE->compteBloquer, 0, '.', ' ')." CFA";
                            $tabl[$i]["avanceanc"] = number_format($compteE->avancesancien, 0, '.', ' ')." CFA";
                            $tabl[$i]["recenrenc"] = number_format($compteE->recentrembourcer, 0, '.', ' ')." CFA";
                            $tabl[$i]["anticenc"] = number_format($compteE->anticiper, 0, '.', ' ')." CFA";
                            $tabl[$i]["echeance"] = number_format($compteE->duree, 0, '.', ' ')." ";
                            if($com->AIB == 0 || $com->AIB == "" || $com->AIB == null)
                                $tabl[$i]["tauxaib"] = "5 %";
                            else
                                $tabl[$i]["tauxaib"] = "3 %";
                            $tabl[$i]["aib"] = number_format($compteE->aibMoisCalculer, 0, '.', ' ')." CFA";
                            $tabl[$i]["retenue"] = number_format($compteE->retenue, 0, '.', ' ')." CFA";
                            $tabl[$i]["carec"] = number_format($compteE->traceCarec, 0, '.', ' ')." CFA";
                            $tabl[$i]["amical"] = number_format($compteE->tracesAmical, 0, '.', ' ')." CFA";
                            $tabl[$i]["naf"] = number_format($compteE->tracenaf, 0, '.', ' ')." CFA";
                            $tabl[$i]["nette"] = number_format($compteE->compte, 0, '.', ' ')." CFA";
                            $tabl[$i]["peri"] = $compteE->MoisCalculer;
                            
                            $i++;
                            
                            $tabl[$i]["code"] = "";
                            $tabl[$i]["apporteur"] = "";
                            $tabl[$i]["ifu"] = "";
                            $tabl[$i]["email"] = "";
                            $tabl[$i]["brute"] = "";
                            $tabl[$i]["enc"] = "";
                            $tabl[$i]["autrcom"] = "";
                            $tabl[$i]["bonus"] = "";
                            $tabl[$i]["fixe"] = "";
                            $tabl[$i]["deduire"] = "";
                            $tabl[$i]["avanceanc"] = "";
                            $tabl[$i]["recenrenc"] = "";
                            $tabl[$i]["anticenc"] = "";
                            $tabl[$i]["echeance"] = "";
                            $tabl[$i]["tauxaib"] = "";
                            $tabl[$i]["aib"] = "";
                            $tabl[$i]["retenue"] = "";
                            $tabl[$i]["carec"] = "";
                            $tabl[$i]["amical"] = "";
                            $tabl[$i]["naf"] = "";
                            $tabl[$i]["nette"] = "";
                            $tabl[$i]["peri"] = "";
                            $i++;
                            /******************************Conseiller****************************/
                            $allmembress = DB::table('commerciauxes')->where('codeEquipe', $com->codeEquipe)->get();
                        
                            foreach($allmembress as $comm)
                            {
                                // Cas des conseillers
                                if($comm->Niveau != "CEQP"){
                                    
                                    $titreC = $titreE." | Conseiller : ".$comm->nomCom." ".$comm->prenomCom;
                            
                                    $compteC = DB::table('compteagents')->where('Agent', $comm->codeCom)->first();
                                    
                                    $tabl[$i]["code"] = "";
                                    $tabl[$i]["apporteur"] = $titreC;
                                    $tabl[$i]["ifu"] = "";
                                    $tabl[$i]["email"] = "";
                                    $tabl[$i]["brute"] = "";
                                    $tabl[$i]["enc"] = "";
                                    $tabl[$i]["autrcom"] = "";
                                    $tabl[$i]["bonus"] = "";
                                    $tabl[$i]["fixe"] = "";
                                    $tabl[$i]["deduire"] = "";
                                    $tabl[$i]["avanceanc"] = "";
                                    $tabl[$i]["recenrenc"] = "";
                                    $tabl[$i]["anticenc"] = "";
                                    $tabl[$i]["echeance"] = "";
                                    $tabl[$i]["tauxaib"] = "";
                                    $tabl[$i]["aib"] = "";
                                    $tabl[$i]["retenue"] = "";
                                    $tabl[$i]["carec"] = "";
                                    $tabl[$i]["amical"] = "";
                                    $tabl[$i]["naf"] = "";
                                    $tabl[$i]["nette"] = "";
                                    $tabl[$i]["peri"] = "";
                                    
                                    $i++;
                                    
                                    $tabl[$i]["code"] = $comm->codeCom;
                                    $tabl[$i]["apporteur"] = $comm->nomCom.' '.$comm->prenomCom;
                                    $tabl[$i]["ifu"] = $comm->AIB;
                                    $tabl[$i]["email"] = $comm->mail;
                                    $tabl[$i]["brute"] = number_format($compteC->compteMoisCalculer, 0, '.', ' ')." CFA";
                                    $tabl[$i]["enc"] = number_format($compteC->compteEncadrementMoisCalculer, 0, '.', ' ')." CFA";
                                    $tabl[$i]["autrcom"] = number_format($compteC->AutreCommissionMoisCalculer, 0, '.', ' ')." CFA";
                                    $tabl[$i]["bonus"] = number_format($compteC->bonus, 0, '.', ' ')." CFA";
                                    $tabl[$i]["fixe"] = number_format($compteC->fixe, 0, '.', ' ')." CFA";
                                    $tabl[$i]["deduire"] = number_format($compteC->compteBloquer, 0, '.', ' ')." CFA";
                                    $tabl[$i]["avanceanc"] = number_format($compteC->avancesancien, 0, '.', ' ')." CFA";
                                    $tabl[$i]["recenrenc"] = number_format($compteC->recentrembourcer, 0, '.', ' ')." CFA";
                                    $tabl[$i]["anticenc"] = number_format($compteC->anticiper, 0, '.', ' ')." CFA";
                                    $tabl[$i]["echeance"] = number_format($compteC->duree, 0, '.', ' ')."";
                                    if($comm->AIB == 0 || $comm->AIB == "" || $comm->AIB == null)
                                        $tabl[$i]["tauxaib"] = "5 %";
                                    else
                                        $tabl[$i]["tauxaib"] = "3 %";
                                    $tabl[$i]["aib"] = number_format($compteC->aibMoisCalculer, 0, '.', ' ')." CFA";
                                    $tabl[$i]["retenue"] = number_format($compteC->retenue, 0, '.', ' ')." CFA";
                                    $tabl[$i]["carec"] = number_format($compteC->traceCarec, 0, '.', ' ')." CFA";
                                    $tabl[$i]["amical"] = number_format($compteC->tracesAmical, 0, '.', ' ')." CFA";
                                    $tabl[$i]["naf"] = number_format($compteC->tracenaf, 0, '.', ' ')." CFA";
                                    $tabl[$i]["nette"] = number_format($compteC->compte, 0, '.', ' ')." CFA";
                                    $tabl[$i]["peri"] = $compteC->MoisCalculer;
                                    
                                    $i++;
                                    
                                    $tabl[$i]["code"] = "";
                                    $tabl[$i]["apporteur"] = "";
                                    $tabl[$i]["ifu"] = "";
                                    $tabl[$i]["email"] = "";
                                    $tabl[$i]["brute"] = "";
                                    $tabl[$i]["enc"] = "";
                                    $tabl[$i]["autrcom"] = "";
                                    $tabl[$i]["bonus"] = "";
                                    $tabl[$i]["fixe"] = "";
                                    $tabl[$i]["deduire"] = "";
                                    $tabl[$i]["avanceanc"] = "";
                                    $tabl[$i]["recenrenc"] = "";
                                    $tabl[$i]["anticenc"] = "";
                                    $tabl[$i]["echeance"] = "";
                                    $tabl[$i]["tauxaib"] = "";
                                    $tabl[$i]["aib"] = "";
                                    $tabl[$i]["retenue"] = "";
                                    $tabl[$i]["carec"] = "";
                                    $tabl[$i]["amical"] = "";
                                    $tabl[$i]["naf"] = "";
                                    $tabl[$i]["nette"] = "";
                                    $tabl[$i]["peri"] = "";
                                    $i++;
                                    
                                }
                            }
                            
                            /****************************** Fin Conseiller****************************/
                            
                            /****************************fin Equipe******************************/
                        }
                    }
                   
                    /**************************** Fin Inspecteur*******************************/
                    
                    
                    
                    // Exporter tous les commissions 
                    
                    // Création du fichier Excel
                    $path = "Commission_resume_du_".view()->shared('periode')."_".$dataCom->codeCom.".xlsx";
                    
                    $autre = new Collection($tabl);
                    Session()->put('commissioncontrlere', $autre);
                    
                    Excel::store(new ExportCommissionControlleResume, $path, 'excelstore');
                        
                    $data = ["pat" => $path];
                    //echo "<br>".$dataCom->codeCom."<br>";
					if($dataCom->codeRegion != "" || $dataCom->codeRegion != null){
						
						if(isset(DB::table('hierarchies')->where("codeH", $dataCom->codeRegion)->first()->managerH)){
							$mag = DB::table('hierarchies')->where("codeH", $dataCom->codeRegion)->first()->managerH;

							$email = DB::table('commerciauxes')->where('codeCom', $mag)->first()->mail;
							SendMail::sendFichecontr($dataCom->mail, "Résume Commission par Inspection. : Inspection de ".$dataCom->nomCom.' '.$dataCom->prenomCom, $data, $email);
						}
					}else{
                    SendMail::sendFichecontr($dataCom->mail, "Résume Commission par Inspection. : Inspection de ".$dataCom->nomCom.' '.$dataCom->prenomCom, $data);
					}
                    $tabl = array();
                    $i = 0;
                }
                else
                {
                    if($dataCom->Niveau == "RG" || $dataCom->Niveau == "CD"){
                    /***********************************Inspecteur******************************************************/
                    Session()->put('commissioncontrlere', null);
                    $compteA = DB::table('compteagents')->where('Agent', $dataCom->codeCom)->first();
                    
                    $titre = "".$dataCom->nomCom." ".$dataCom->prenomCom;
                    
                    $tabl[0]["code"] = "";
                    $tabl[0]["apporteur"] = $titre;
                    $tabl[0]["ifu"] = "";
                    $tabl[0]["email"] = "";
                    $tabl[0]["brute"] = "";
                    $tabl[0]["enc"] = "";
                    $tabl[0]["autrcom"] = "";
                    $tabl[0]["bonus"] = "";
                    $tabl[0]["fixe"] = "";
                    $tabl[0]["deduire"] = "";
                    $tabl[0]["avanceanc"] = "";
                    $tabl[0]["recenrenc"] = "";
                    $tabl[0]["anticenc"] = "";
                    $tabl[0]["echeance"] = "";
                    $tabl[0]["tauxaib"] = "";
                    $tabl[0]["aib"] = "";
                    $tabl[0]["retenue"] = "";
                    $tabl[0]["carec"] = "";
                    $tabl[0]["amical"] = "";
                    $tabl[0]["naf"] = "";
                    $tabl[0]["nette"] = "";
                    $tabl[0]["peri"] = "";
                    $i = 1;
                    
                        $tabl[$i]["code"] = $dataCom->codeCom;
                        $tabl[$i]["apporteur"] = $dataCom->nomCom.' '.$dataCom->prenomCom;
                        $tabl[$i]["ifu"] = $dataCom->AIB;
                        $tabl[$i]["email"] = $dataCom->mail;
                        $tabl[$i]["brute"] = number_format($compteA->compteMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["enc"] = number_format($compteA->compteEncadrementMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["autrcom"] = number_format($compteA->AutreCommissionMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["bonus"] = number_format($compteA->bonus, 0, '.', ' ')." CFA";
                        $tabl[$i]["fixe"] = number_format($compteA->fixe, 0, '.', ' ')." CFA";
                        $tabl[$i]["deduire"] = number_format($compteA->compteBloquer, 0, '.', ' ')." CFA";
                        $tabl[$i]["avanceanc"] = number_format($compteA->avancesancien, 0, '.', ' ')." CFA";
                        $tabl[$i]["recenrenc"] = number_format($compteA->recentrembourcer, 0, '.', ' ')." CFA";
                        $tabl[$i]["anticenc"] = number_format($compteA->anticiper, 0, '.', ' ')." CFA";
                        $tabl[$i]["echeance"] = number_format($compteA->duree, 0, '.', ' ')." ";
                        if($dataCom->AIB == 0 || $dataCom->AIB == "" || $dataCom->AIB == null)
                            $tabl[$i]["tauxaib"] = "5 %";
                        else
                            $tabl[$i]["tauxaib"] = "3 %";
                        $tabl[$i]["aib"] = number_format($compteA->aibMoisCalculer, 0, '.', ' ')." CFA";
                        $tabl[$i]["retenue"] = number_format($compteA->retenue, 0, '.', ' ')." CFA";
                        $tabl[$i]["carec"] = number_format($compteA->traceCarec, 0, '.', ' ')." CFA";
                        $tabl[$i]["amical"] = number_format($compteA->tracesAmical, 0, '.', ' ')." CFA";
                        $tabl[$i]["naf"] = number_format($compteA->tracenaf, 0, '.', ' ')." CFA";
                        $tabl[$i]["nette"] = number_format($compteA->compte, 0, '.', ' ')." CFA";
                        $tabl[$i]["peri"] = $compteA->MoisCalculer;
                        
                    $i++;
                    
                    // Exporter tous les commissions 
                    
                    // Création du fichier Excel
                    $path = "Commission_resume_du_".view()->shared('periode')."_".$dataCom->codeCom.".xlsx";
                    
                    $autre = new Collection($tabl);
                    Session()->put('commissioncontrlere', $autre);
                    
                    Excel::store(new ExportCommissionControlleResume, $path, 'excelstore');
                        
                    $data = ["pat" => $path];
                    //echo "<br>".$dataCom->codeCom."<br>";
                    SendMail::sendFichecontr($dataCom->mail, "Résume Commission par Inspection. Coordornateur : ".$dataCom->codeCom, $data);
                    $tabl = array();
                    $i = 0;
                    
                    }
                }
                
            }
            $tabl = array();$i = 0;
            Session()->put('commissioncontrlere', null);
        }
        
        return 0;
    }

    public function etatdetailresume(Request $request)
    {
        
        $list = DB::table('compteagents')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'compteagents.Agent')->orderBy("compteagents.Agent", "desc");
                
        
        $titre = "ETAT DES COMMISSIONS";

        $list = $list->where('compteagents.statueValide', 1)->get();
        
        //dd($list);
            setlocale(LC_TIME,  'french');

            $datelettre = strtoupper(strftime('%B %Y'));

            // Création du fichier Excel
            $name = "Etat_Commission_Resume".date('dmYhis');
        
            $path = $name.".xlsx";
            
            $tabl[0]["code"] = "";
            $tabl[0]["nom"] = "";
            $tabl[0]["prenom"] = "";
            $tabl[0]["niv"] = "";
            $tabl[0]["gains"] = "";
            $tabl[0]["enc"] = "";
            $tabl[0]["fixe"] = "";
            $tabl[0]["bonus"] = "";
            $tabl[0]["brut"] = "";
            $tabl[0]["aib"] = "";
            $tabl[0]["restantaib"] = "";
            $tabl[0]["restantant"] = "";
            $tabl[0]["soldedispo"] = "";
            $tabl[0]["prel"] = "";
            $tabl[0]["restantprel"] = "";
            $tabl[0]["naf"] = "";
            $tabl[0]["restantnaf"] = "";
            $tabl[0]["avance"] = "";
            $tabl[0]["echeance"] = "";
            $tabl[0]["avancepayer"] = "";
            $tabl[0]["restantavance"] = "";
            $tabl[0]["carec"] = "";
            $tabl[0]["restantcarec"] = "";
            $tabl[0]["amical"] = "";
            $tabl[0]["restantamical"] = "";
            $tabl[0]["nette"] = "";
            $tabl[0]["peri"] = "";
            
            $som = 0;
            $somgain = 0;
            $somaib = 0;
            $somavan = 0;
            $somprel = 0;
            $somcarec = 0;
            $somnaf = 0;
            $somamical = 0;
            $i = 1;
            
            foreach ($list as $value){
                //$comp = InterfaceServiceProvider::RecupCompteAncien($value->Agent, $value->moiscalculer); 
                $comp = DB::table('compteagents')->where('Agent', $value->Agent)->first();

                $encard = 0;
                $tabl[$i]["code"] = $value->codeCom;
                $tabl[$i]["nom"] = $value->nomCom;
                $tabl[$i]["prenom"] = $value->prenomCom;
                $tabl[$i]["niv"] = $value->Niveau;
                $tabl[$i]["gains"] = $comp->compteMoisCalculer;
                $tabl[$i]["enc"] = $comp->compteEncadrementMoisCalculer;
                $tabl[$i]["fixe"] = $comp->fixe;
                $tabl[$i]["bonus"] = $comp->bonus;
                $brut = $comp->compteMoisCalculer + $comp->compteEncadrementMoisCalculer + $comp->bonus + $comp->fixe;
                $tabl[$i]["brut"] = $brut;
                $tabl[$i]["aib"] = $comp->aibMoisCalculer;
                $restaib = ($brut - $comp->aibMoisCalculer) > 0 ? $brut - $comp->aibMoisCalculer : 0;
                $tabl[$i]["restantaib"] = $restaib;
                $tabl[$i]["restantant"] = $comp->compteBloquerBackup;
                $soldedispo = ($restaib + ($comp->compteBloquerBackup)) > 0 ? $restaib + ($comp->compteBloquerBackup) : 0;
                $tabl[$i]["soldedispo"] = $soldedispo;
                $tabl[$i]["prel"] = ($soldedispo - $comp->traceretenue) > 0 ? ($comp->traceretenue) : $comp->retenue;
                $restprel = ($soldedispo - $comp->traceretenue) > 0 ? ($soldedispo - $comp->traceretenue) : 0;
                $tabl[$i]["restantprel"] = $restprel;
                $tabl[$i]["naf"] = $comp->tracenaf;
                $restnaf = ($restprel - $comp->tracenaf) > 0 ? ($restprel - $comp->tracenaf) : 0;
                $tabl[$i]["restantnaf"] = $restnaf;
                $tabl[$i]["avance"] = $comp->avances;
                $echeance = 0;
                if($comp->duree != 0)
                    $echeance = $comp->avances / $comp->duree;
                $tabl[$i]["echeance"] = $echeance;
                $tabl[$i]["avancepayer"] = $comp->recentrembourcer;
                $restecheance = ($restnaf - $comp->recentrembourcer) > 0 ? ($restnaf - $comp->recentrembourcer) : 0;
                $tabl[$i]["restantavance"] = $restecheance;
                $tabl[$i]["carec"] = $comp->traceCarec;
                $restcarec = ($restecheance - $comp->traceCarec) > 0 ? ($restecheance - $comp->traceCarec) : 0;
                $tabl[$i]["restantcarec"] = $restcarec;
                $tabl[$i]["amical"] = $comp->tracesAmical;
                $restamical = ($restcarec - $comp->tracesAmical) > 0 ? ($restcarec - $comp->tracesAmical) : 0;
                $tabl[$i]["restantamical"] = $restamical;

                $tabl[$i]["nette"] = $comp->compte;
                $tabl[$i]["peri"] = $value->MoisCalculer;
                $i++;
            }
            $autre = new Collection($tabl);
            Session()->put('commissionglobale', $autre);
            
            return Excel::download(new ExportCommissionGlobaleTraitement, $path);
            // Téléchargement du fichier excel
    }

    /**
     *  Get && Set Validation CG
     */

    public function getcommissioncg(){
        $verif = DB::table('processuscominds')
                ->where("csp", 1)
                ->whereRaw(" (cg1 = 0 or cg2 = 0 or cg3 = 0) ")
                ->where('mois', view()->shared('periode'))
                ->first();
        $list = array(); 
        if(isset($verif->idpci))
            if(session('utilisateur')->idUser == 18) // SOUHOUIN
                $list = DB::table('commissions')
                ->select('commissions.Apporteur as Commercial')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', null)
                ->where('cdCom', session('utilisateur')->other)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->distinct('commissions.Apporteur')->get(); 
            else
                $list = DB::table('commissions')
                ->select('commissions.Apporteur as Commercial')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', null)
                ->where('regionCom', session('utilisateur')->other)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->distinct('commissions.Apporteur')->get(); 
                        
            
        $libelleRole = "CG";

        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;

        $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->first(); 
        $signaturesp = "";
        if(isset($signature->pathSignataire))
            $signaturesp = $signature->pathSignataire;
        $search = "Rechercher";
        

        return view('commission.listcomcg', compact('list', 'libelleRole', 'sigle', 'signaturesp', 'search'));
    }
	
	public function setvalidationcg(Request $request){
        
        if(session('utilisateur')->other == 7101)
            ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["cg1" => 1]);
        if(session('utilisateur')->other == 5127)
            ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["cg3" => 1]);
        if(session('utilisateur')->other == 5149)
            ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["cg2" => 1]);
            
            flash("Vous avez confirmé les commissions de ce mois.");
            TraceController::setTrace("Vous avez confirmé les commissions de ce mois.",session("utilisateur")->idUser);
            
            return redirect()->route('listcomcg');
        
    }

    /**
     *  Get Validation DT
     */

    public function getvalidationdt(){
        $verif = DB::table('processuscominds')
                ->where("csp", 1)
                ->whereRaw(" (cg1 = 1 and cg2 = 1 and cg3 = 1) ")
                ->where("dt", 0)
                ->where('mois', view()->shared('periode'))
                ->first();
        $list = array(); 
        if(isset($verif->idpci))
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101])->get();
            
        $libelleRole = "DT";
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;

        // Le premier signataire du mois est bien sur csp
        $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get(); // TEST
        $signaturesp = "";
        $signaturecsp = "";
        if(isset($signature[0]->pathSignataire))
            $signaturesp = $signature[0]->pathSignataire;
        if(isset($signature[1]->pathSignataire))
            $signaturecsp = $signature[1]->pathSignataire;

        $search = "Rechercher";

        return view('commission.listcomdt', compact('list', 'libelleRole', 'sigle', 'signaturesp', 'signaturecsp', 'search'));
    }

    public function setvalidationdt(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmerdt', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;
            
            DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmerdt', null)->update([
                    "confirmerdt" => "oui"
                ]);

            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = sizeof($commission);
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = view()->shared('periode');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();

            // Récupérer le ou les dg
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "dg")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "dt");
            //}

            flash("Vous avez confirmé les commissions de ce mois.");
            TraceController::setTrace("Vous avez confirmé les commissions de ce mois.", session("utilisateur")->idUser);
            ProcessusComInd::saveprocessus("dt");
            return redirect()->route('GCDT');
            //return Back();
        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCDT');
        }
    }
    
    public function setrejetdt(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmerdt', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            
            DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmerdt', null)
                ->update([
                    "confirmercsp" => null
                ]);
            
            // Envoi de mail
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "csp")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetdt");
            //}
        
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', view()->shared('periode'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', view()->shared('periode'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions de ce mois.");
            TraceController::setTrace("Vous avez rejeté les commissions de ce mois.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCDT');

        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCDT');
        }
    }


    /**
     *  Get Validation DG
     * */

    public function getvalidationdg(){
        $verif = DB::table('commissions')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101]);
        else
            $list = DB::table('commissions')
            //->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            //->join('compteagents', 'compteagents.Agent', '=', 'contrats.Agent')
            ->select('commissions.Apporteur as Commercial'
            //, 'compteagents.libCompte as libelleReglement', 'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  'compteagents.avances as navance'
            )
            ->where("statutcalculer", "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            //->where('commissions.moiscalculer', view()->shared('periode')) // Mode TEST
            ////->where('commissions.moiscalculer', view()->shared('periode'))
            ->distinct('commissions.Apporteur'); 
            //->paginate(10000);
        $libelleRole = "DG";
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;
        /*$signature = "";
        if (isset(DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get()) {
            $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get();
        }*/

        // Le premier signataire du mois est bien sur csp
        $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get(); // TEST
        $signaturesp = "";
        $signaturecsp = "";
        $signaturedt = "";
        if(isset($signature[2]->pathSignataire))
            $signaturedt = $signature[2]->pathSignataire;
        if(isset($signature[1]->pathSignataire))
            $signaturecsp = $signature[1]->pathSignataire;
        if(isset($signature[0]->pathSignataire))
            $signaturesp = $signature[0]->pathSignataire;

        $search = "Rechercher";
        if(request('rec') == 1){
            // Liste des commissions concernés
            $commission = DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('TypeCommission', 'i')
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('commissions.Apporteur', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commission.listcomdg', compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commission.listcomdg", compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }

        $list = $list->paginate(10000);

        return view('commission.listcomdg', compact('list', 'search', 'libelleRole', 'sigle', 'signaturesp', 'signaturecsp', 'signaturedt'));
    }

    public function setvalidationdg(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmerdg', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;

            //foreach ($commission as $comm) {
                // mettre à jour la table commission par oui

                DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmerdg', null)
                ->update([
                    "confirmerdg" => "oui"
                ]);
               // $a++;

            //}
            //$a--;
            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = sizeof($commission);
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = view()->shared('periode');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();

            // Récupérer le ou les cdaf
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "cdaf")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mailZ);
            }
            SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "dg");
            //}

            flash("Vous avez confirmé les commissions de ce mois.");
            TraceController::setTrace(
                "Vous avez confirmé les commissions de ce mois.",
                session("utilisateur")->idUser);
            ProcessusComInd::saveprocessus("dg");
            //return Back();
            return redirect()->route('GCDG');
        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCDG');
        }
    }
    
    public function setrejetdg(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmerdg', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            //foreach ($commission as $comm) {
                DB::table('commissions')->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmerdg', null)->update([
                    "confirmerdt" => null
                ]);
          //  }

            // Envoi de mail
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "dt")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetdg");
            //}
        
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', view()->shared('periode'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', view()->shared('periode'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions de ce mois.");
            TraceController::setTrace("Vous avez rejeté les commissions de ce mois.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCDG');

        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCDG');
        }
    }

    /**
     *  Get Validation CDAF
     * */

    public function getvalidationcdaf(){
        $verif = DB::table('commissions')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101]);
        else
            $list = DB::table('commissions')
              //->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            //->join('compteagents', 'compteagents.Agent', '=', 'contrats.Agent')
            ->select('commissions.Apporteur as Commercial'
            //, 'compteagents.libCompte as libelleReglement', 'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  'compteagents.avances as navance'
            )
            ->where("statutcalculer", "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmercdaf', null)
            ->distinct('commissions.Apporteur'); 
            //->paginate(10000);
        $libelleRole = "CDAF";
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;
        /*$signature = "";
        if (isset(DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get()) {
            $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get();
        }*/

        // Le premier signataire du mois est bien sur csp
        $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get(); // TEST
        $signaturesp = "";
        $signaturecsp = "";
        $signaturedt = "";
        $signaturedg = "";
        if(isset($signature[2]->pathSignataire))
            $signaturedt = $signature[2]->pathSignataire;
        if(isset($signature[1]->pathSignataire))
            $signaturecsp = $signature[1]->pathSignataire;
        if(isset($signature[0]->pathSignataire))
            $signaturesp = $signature[0]->pathSignataire;
        if(isset($signature[3]->pathSignataire))
            $signaturedg = $signature[3]->pathSignataire;
        $search = "Rechercher";
        if(request('rec') == 1){
            // Liste des commissions concernés
            $commission = DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('TypeCommission', 'i')
                ->where('confirmercdaf', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('commissions.Apporteur', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commission.listcomdg', compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commission.listcomdg", compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }

        $list = $list->paginate(10000);
        return view('commission.listcomcdaf', compact('list', 'libelleRole','sigle', 'search', 'signaturesp', 'signaturecsp', 'signaturedt', 'signaturedg'));
    }

    public function setvalidationcdaf(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmercdaf', null)
            ->get();
        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
           // $a = 0;

            //foreach ($commission as $comm) {
                // mettre à jour la table commission par oui

                DB::table('commissions')->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmercdaf', null)->update([
                    "confirmercdaf" => "oui"
                ]);

             //   $a++;

            //}
            //$a--;

            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = sizeof($commission);
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = view()->shared('periode');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();  
            // Récupérer le ou les dt
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "T")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mailZ);
            }
            SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "cdaf");
            //}

            flash("Vous avez confirmé les commissions de ce mois.");
            TraceController::setTrace(
                "Vous avez confirmé les commissions de ce mois.",
                session("utilisateur")->idUser);
            ProcessusComInd::saveprocessus("cdaf");
            return redirect()->route('GCCDAF');
            //return Back();
        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            return redirect()->route('GCCDAF');
            //return Back();
        }
    }

    public function setrejetcdaf(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmercdaf', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            //foreach ($commission as $comm) {
                DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmercdaf', null)->update([
                    "confirmerdg" => null
                ]);
            //}

            // Envoi de mail
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "dg")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetcdaf");
            //}
        
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', view()->shared('periode'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', view()->shared('periode'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions de ce mois.");
            TraceController::setTrace("Vous avez rejeté les commissions de ce mois.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCCDAF');

        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCCDAF');
        }
    }

    /**
     *  Get Validation Trésorerie
     **/

    public function getvalidationtresorerie(Request $request){
        
        // Exportation en Excel
        if (request('Excel') == "Excel") {
            
            $tabl = CommissionController::ExporterEnExcel(request('reglement'));
            $request->request->add(['reglement' => 'value']);
            //$date = view()->shared('periode');
            $date = view()->shared('periode'); // Mode TEST
            
            // Exporter tous les commissions 
            $autre = new Collection($tabl);
            Session()->put('allcommission', $autre);
            TraceController::setTrace(
                "Vous avez exporté les commissions du mois de ".utf8_encode(strtoupper(view()->shared('periodelettre')))." en Excel.",
                session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportCommissionAll, 'Commission_'.$date.'_Export_'.date('Y-m-d-h-i-s').'.xlsx');
        }

        // Exportattion en pdf
        if (request('PDF') == "PDF") {
            TraceController::setTrace(
                "Vous avez exporté les commissions du mois de ".utf8_encode(strtoupper(view()->shared('periodelettre')))." en PDF.",
                session("utilisateur")->idUser);
            $path = CommissionController::ExporterEnPDF(request('reglement'));

            dd($path);
            //Storage::put($path, $pdf->output());

            //return \Redirect::to('https://arimaxe.com/apk/i-secu.apk');
        }
        
        // Liste des commissions. Tout ou par règlement suivant la recherche
        $verif = DB::table('commissions')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', "oui")
                ->where('confirmertresorerie', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101]);
        else
            $list = DB::table('commissions') // $list = Commission::with(['contrats.Agent'])
            //->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            //->join('compteagents', 'compteagents.Agent', '=', 'contrats.Agent')
            ->select('commissions.Apporteur as Commercial'
            //, 'compteagents.libCompte as libelleReglement', 'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  'compteagents.avances as navance'
            )
            ->where("statutcalculer", "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where('confirmercdaf', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where('confirmertresorerie', null)
            ->distinct('commissions.Apporteur');

            //->paginate(10000);
        $libelleRole = "Trésorerie ";
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;
        /*$signature = "";
        if (isset(DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get()) {
            $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get();
        }*/

        // Le premier signataire du mois est bien sur sp
        $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get(); // TEST
        $signaturesp = "";
        $signaturecsp = "";
        $signaturedt = "";
        $signaturedg = "";
        $signaturecdaf = "";
        if(isset($signature[2]->pathSignataire))
            $signaturedt = $signature[2]->pathSignataire;
        if(isset($signature[1]->pathSignataire))
            $signaturecsp = $signature[1]->pathSignataire;
        if(isset($signature[0]->pathSignataire))
            $signaturesp = $signature[0]->pathSignataire;
        if(isset($signature[3]->pathSignataire))
            $signaturedg = $signature[3]->pathSignataire;
        if(isset($signature[4]->pathSignataire))
            $signaturecdaf = $signature[4]->pathSignataire;
        $reglement = request('reglement');
        $listPayement = DB::table('structures')->get();
        $search = "Rechercher";
        if(request('rec') == 1){
            // Liste des commissions concernés
            $commission = DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where('confirmertresorerie', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('Agent', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commission.listcomtresorerie', compact('listPayement','list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commission.listcomtresorerie", compact('listPayement','list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }
        
        if ((request('reglement') != null && request('reglement') != "all") && request('recherche') == "rech") {
             /*$list = DB::select("SELECT contrats.Agent as Commercial from commissions, contrats, compteagents
             where contrats.police = commissions.NumPolice and compteagents.Agent = contrats.Agent and statutcalculer = 'oui' and confirmercalcule = 'oui' and confirmercsp = 'oui' and confirmerdt = 'oui' and 
             confirmerdg = 'oui' and confirmercdaf = 'oui' and confirmertresorerie = null and commissions.moiscalculer = '07-2021' and 
             compteagents.libCompte = '".request('reglement')."'"); */ 
                /*$list = DB::table('commissions')
                    //->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('compteagents', 'compteagents.Agent', '=', 'commissions.Apporteur')
                    ->select('commissions.Apporteur as Commercial'
                    //, 'compteagents.libCompte as libelleReglement', 'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  'compteagents.avances as navance'
                    )
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where("ctrl", 1)
                    ->where("commissions.Statut", view()->shared('periode'))
                    ->where('confirmertresorerie', null)
                    //->where('commissions.moiscalculer', view()->shared('periode')) // Mode TEST
                    ////->where('commissions.moiscalculer', view()->shared('periode'))
                    ->distinct('commissions.Apporteur') */
                    $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101])
                    ->where("compteagents.libCompte", 'like', '%'.request('reglement').'%')->paginate(10000); 
                //$control = "contr";
                return view('commission.listcomtresorerie', compact('listPayement', 'list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
        }

        $list = $list->paginate(10000);

        return view('commission.listcomtresorerie', compact('listPayement', 'list', 'search', 'libelleRole', 'sigle', 'signaturesp', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
    }

    public function setvalidationtresorerie(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Sauvegarder le formulaire du mois
        $fiche_O = "";
        $fiche_D = "";
        $detail_D = "";
        $detail_O = "";
        $lib_fiche_O = "";
        $lib_detail_O = "";
        $lib_fiche_D = "";
        $lib_detail_D = "";
        $moisCom = view()->shared('periode');
        $datelettre = utf8_encode(strtoupper(view()->shared('periodelettre')));
        //$datelettre = utf8_encode(strtoupper("Février 2023"));
        $datelettrefile = utf8_encode(strtoupper(view()->shared('periodelettre')));
        // Vérification des montants reverser
        if(!isset( DB::table('recapcommissions')->where('serie', session('qr'))->first()->montantEtat)){
            flash("Le code QR n'existe pas ou n'est pas renseigné.")->error();
            return Back();
        }
        $allreglement = DB::table('reglements')->where('RecapCommission', session('qr'))->get();
        $montglobal = DB::table('recapcommissions')->where('serie', session('qr'))->first()->montantEtat;
        $total = 0;
            // Calcul des Totales
                foreach($allreglement as $com){
                    $total += $com->Montant;
                }
        
        if ($total != $montglobal) {
            flash('Montant reverser est différent du montant global des commissions du mois. Revérifier.')->error();
            return Back();
        }
        else{
            /*
            // Parcourir les commissions
            $a = 0;

            //foreach ($commission as $comm) {
                // mettre à jour la table commission par oui
                DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', "oui")
                ->where('confirmertresorerie', null)
                ->where('TypeCommission', 'i')
                ->where("ctrl", 1)
                ->update([
                    "confirmertresorerie" => "oui"
                ]); */
              //  $a++;
            //}
            /*
            $list = DB::table('compteagents')
            ->join('commerciauxes', 'compteagents.Agent', '=', 'commerciauxes.codecom')
            ->select('compteagents.Agent as Commercial')
            ->where("statueValide", 1)
            ->whereNotIn('Agent', [1,2,3, 101])->get();
            
            // Etablir Fiches de paie pour les apporteurs
            foreach ($list as $apporteur) {

                $infoapporteur = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first();
                
                if(!isset(DB::table('documents')->where('Agent', $infoapporteur->codeCom)->where('periode', $moisCom)->first()->id)){
                
                    if ($infoapporteur->Niveau == "INS") {
                        // Ce que lui même à reçu sur ses affaires
                        $detaillui = DB::table('commissions')
                        ->where("ctrl", 1)
                        
                        ->where("commissions.Apporteur", $infoapporteur->codeCom)->get();
                        
                        // Ce qu'il a gagner en tant chef d'inspection
                        $codeINSP = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                        $detailins = DB::table('commissions')
                        
                        ->join('commerciauxes', 'commissions.Apporteur', '=', 'commerciauxes.codecom')
                        ->where("ctrl", 1)
                        
                        ->where('commerciauxes.codeInspection', $codeINSP)->get(); 
                    }
    
                    //////////////////////FICHE DE PAIE////////////////////////////////
    
                    //if ($infoapporteur->Niveau != "INS" && $infoapporteur->Niveau != "CEQP" && $infoapporteur->Niveau != "RG") {
                            // Ce que lui même à reçu sur ses affaires
                            $detail = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                
                                //
    
                                // Création du PDF
    
                                $titre = "FICHE DE PAIE CONSEILLER COMMERCIAL ";
                                $lib = "FICHEPAIE_".$apporteur->Commercial."_".$datelettrefile;
                                $path = "document/commission/".$lib.".pdf";
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 15, -150);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 22, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 42, "SOCIETE :");
                                $pdf->Line(10, 43, 60, 43);
                                $pdf->Text(70, 42, "CONSEILLER COMMERCIAL :");
                                $pdf->Line(70, 43, 140, 43);
                                $pdf->Text(155, 42, "PERIODE :");
                                $pdf->Line(155, 43, 197, 43);
    
                                // Information sur société
    
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(10, 48,iconv('utf-8','cp1252','Nom :'));
                                $pdf->Text(30, 48,iconv('utf-8','cp1252', $soc->libelleSociete));
                                $pdf->Text(10, 52,iconv('utf-8','cp1252', "Adresse :"));
                                $pdf->Text(30, 52,iconv('utf-8','cp1252', $soc->adresse));
                                $pdf->Text(10, 56,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(30, 56,iconv('utf-8','cp1252', $soc->email));
    
                                // Information sur le commercial
    
                                $pdf->Text(70, 48,iconv('utf-8','cp1252','Code :'));
                                $pdf->Text(90, 48,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(70, 52,iconv('utf-8','cp1252', "Nom :"));
                                $pdf->Text(90, 52,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(70, 56,iconv('utf-8','cp1252','Statut :'));
                                $pdf->Text(90, 56,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
                                $pdf->Text(70, 60,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(90, 60,iconv('utf-8','cp1252', $infoapporteur->mail));
    
                                // Information sur la période
    
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $frais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                // Informations sur les actives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(80, 70, iconv('utf-8','cp1252', "Base"));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Line(10, 71, 100, 71);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(10);
                                $pdf->Text(10, 78, iconv('utf-8','cp1252', "Montant Commission : "));
                                $pdf->Text(80, 78, iconv('utf-8','cp1252', number_format(($frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 80, 100, 80);
                                $pdf->Text(10, 84, iconv('utf-8','cp1252', "Autres Commissions : "));
                                $pdf->Text(80, 84, iconv('utf-8','cp1252', number_format(($frais->AutreCommissionMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 86, 100, 86);
                                $pdf->Text(10, 90, iconv('utf-8','cp1252', "Fixe : "));
                                $pdf->Text(80, 90, iconv('utf-8','cp1252', number_format(($frais->fixe), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 92, 100, 92);
                                $pdf->Text(10, 96, iconv('utf-8','cp1252', "Bonus : "));
                                $pdf->Text(80, 96, iconv('utf-8','cp1252', number_format(($frais->bonus), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 98, 100, 98);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 102, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 102, iconv('utf-8','cp1252', number_format(($frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer ), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 104, 100, 104);
                                
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Text(10, 108, iconv('utf-8','cp1252', "Dotation Téléphonie (nature) : "));
                                $pdf->Text(80, 108, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 110, 100, 110);
                                $pdf->Text(10, 114, iconv('utf-8','cp1252', "Dotation Carburant (nature) : "));
                                $pdf->Text(80, 114, iconv('utf-8','cp1252', number_format(($frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 116, 100, 116);
                                
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 120, iconv('utf-8','cp1252', "Total en nature : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 120, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie + $frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 122, 100, 122);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total brute : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(($frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer + $frais->dotationTelephonie + $frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 128, 100, 128);
    
                                // Informations sur les passives
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(178, 70, iconv('utf-8','cp1252', "Retenues"));
                                $pdf->Line(110, 71, 197, 71);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Text(110, 78, iconv('utf-8','cp1252', "Taux Impôt : "));
                                $pdf->Text(180, 78, iconv('utf-8','cp1252', (Fonction::RecupererTaux($infoapporteur->codeCom))." %"));
                                $pdf->Line(110, 80, 197, 80);
                                $pdf->Text(110, 84, iconv('utf-8','cp1252', "Impôt : "));
                                $pdf->Text(180, 84, iconv('utf-8','cp1252', number_format(($frais->aibMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 86, 197, 86);
                                $pdf->Text(110, 90, iconv('utf-8','cp1252', "Avance payer ce mois : "));
                                $pdf->Text(180, 90, iconv('utf-8','cp1252', number_format(($frais->recentrembourcer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 92, 197, 92);
                                $pdf->Text(110, 96, iconv('utf-8','cp1252', "Prélèvement : "));
                                $pdf->Text(180, 96, iconv('utf-8','cp1252', number_format(($frais->retenue), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 98, 197, 98);
                                $pdf->Text(110, 102, iconv('utf-8','cp1252', "Nature du Prélèvement : "));
                                $pdf->Text(140, 102, iconv('utf-8','cp1252', $frais->libretenue));
                                $pdf->Line(110, 104, 197, 104);
                                $pdf->Text(110, 108, iconv('utf-8','cp1252', "Carec : "));
                                $pdf->Text(180, 108, iconv('utf-8','cp1252', number_format(($frais->traceCarec), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 110, 197, 110);
                                $pdf->Text(110, 114, iconv('utf-8','cp1252', "Amical : "));
                                $pdf->Text(180, 114, iconv('utf-8','cp1252', number_format(($frais->montantAmical), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 116, 197, 116);
                                $pdf->Text(110, 120, iconv('utf-8','cp1252', "Naf : "));
                                $pdf->Text(180, 120, iconv('utf-8','cp1252', number_format(($frais->naf), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 122, 197, 122);
                                if($frais->compteBloquer > 0){
                                    $pdf->Text(110, 126, iconv('utf-8','cp1252', "Solde à débiter le mois prochain : "));
                                    $pdf->Text(180, 126, iconv('utf-8','cp1252', number_format((abs($frais->compteBloquer)), 0, '.', ' ')." CFA"));
                                }
                                
                                $pdf->Line(110, 128, 197, 128);
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 132, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 132, iconv('utf-8','cp1252', number_format(( $frais->retenue + $frais->montantAmical + $frais->traceCarec + $frais->tracenaf + $frais->aibMoisCalculer + $frais->recentrembourcer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 134, 197, 134);
    
                                $pdf->SetFont("Arial", "B", 8);
    
                                // Total sur les actives
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 144, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 144, iconv('utf-8','cp1252', number_format(($frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 146, 197, 146);
                                
    
                                // Total sur les passives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 153, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 153, iconv('utf-8','cp1252', number_format(( $frais->retenue + $frais->montantAmical + $frais->traceCarec + $frais->tracenaf + $frais->aibMoisCalculer + $frais->recentrembourcer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 155, 197, 155);
    
                                // Total
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 162, iconv('utf-8','cp1252', "Total net  : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 162, iconv('utf-8','cp1252', number_format(($frais->compte), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 164, 197, 164);
    
    
                                // Signature Commercial
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(20, 174, iconv('utf-8','cp1252', "Signature du Conseiller Commercial : "));
                                $pdf->SetTextColor(0,0,0);
    
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(35, 194, iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
    
                                // Signature Société
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(150, 174, iconv('utf-8','cp1252', "Signature de la Société : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Image($soc->signature, 150, 176, -150);
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(160, 194, iconv('utf-8','cp1252', $soc->libelleSociete));
    
                                $pdf->Output('F', $path);
                                $fiche_O = $path;
                                $lib_fiche_O = $lib;
    
                                // Envoyer par mail le fichier
    
                                //Storage::put($path, $pdf->output());
                                
                                //return $path;
    
                    //}
                    //////////////////////FIN fiche de paie////////////////////////////////
    
                    //////////////////////FICHE DE PAIE (DUPLICATA) ////////////////////////////////
                    // Ce que lui même à reçu sur ses affaires
                            $detail = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                //
                                
    
                                // Création du PDF
    
                                $titre = "FICHE DE PAIE CONSEILLER COMMERCIAL ";
                                $lib = "FICHEPAIE_DUPLICATA_".$apporteur->Commercial."_".$datelettrefile;
                                $pathd = 'document/commission/'.$lib.'.pdf';
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 15, -150);
                                $pdf->Image($soc->duplicata, 130, 5, -250);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 22, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 42, "SOCIETE :");
                                $pdf->Line(10, 43, 60, 43);
                                $pdf->Text(70, 42, "CONSEILLER COMMERCIAL :");
                                $pdf->Line(70, 43, 140, 43);
                                $pdf->Text(155, 42, "PERIODE :");
                                $pdf->Line(155, 43, 197, 43);
    
                                // Information sur société
    
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(10, 48,iconv('utf-8','cp1252','Nom :'));
                                $pdf->Text(30, 48,iconv('utf-8','cp1252', $soc->libelleSociete));
                                $pdf->Text(10, 52,iconv('utf-8','cp1252', "Adresse :"));
                                $pdf->Text(30, 52,iconv('utf-8','cp1252', $soc->adresse));
                                $pdf->Text(10, 56,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(30, 56,iconv('utf-8','cp1252', $soc->email));
    
                                // Information sur le commercial
    
                                $pdf->Text(70, 48,iconv('utf-8','cp1252','Code :'));
                                $pdf->Text(90, 48,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(70, 52,iconv('utf-8','cp1252', "Nom :"));
                                $pdf->Text(90, 52,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(70, 56,iconv('utf-8','cp1252','Statut :'));
                                $pdf->Text(90, 56,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
                                $pdf->Text(70, 60,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(90, 60,iconv('utf-8','cp1252', $infoapporteur->mail));
    
                                // Information sur la période
    
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $frais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                // Informations sur les actives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(80, 70, iconv('utf-8','cp1252', "Base"));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Line(10, 71, 100, 71);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(10);
                                $pdf->Text(10, 78, iconv('utf-8','cp1252', "Montant Commission : "));
                                $pdf->Text(80, 78, iconv('utf-8','cp1252', number_format(($frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 80, 100, 80);
                                $pdf->Text(10, 84, iconv('utf-8','cp1252', "Autres Commissions : "));
                                $pdf->Text(80, 84, iconv('utf-8','cp1252', number_format(($frais->AutreCommissionMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 86, 100, 86);
                                $pdf->Text(10, 90, iconv('utf-8','cp1252', "Fixe : "));
                                $pdf->Text(80, 90, iconv('utf-8','cp1252', number_format(($frais->fixe), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 92, 100, 92);
                                $pdf->Text(10, 96, iconv('utf-8','cp1252', "Bonus : "));
                                $pdf->Text(80, 96, iconv('utf-8','cp1252', number_format(($frais->bonus), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 98, 100, 98);
                                
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 102, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 102, iconv('utf-8','cp1252', number_format(($frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer ), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 104, 100, 104);
                                
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Text(10, 108, iconv('utf-8','cp1252', "Dotation Téléphonie (nature) : "));
                                $pdf->Text(80, 108, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 110, 100, 110);
                                $pdf->Text(10, 114, iconv('utf-8','cp1252', "Dotation Carburant (nature) : "));
                                $pdf->Text(80, 114, iconv('utf-8','cp1252', number_format(($frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 116, 100, 116);
                                
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 120, iconv('utf-8','cp1252', "Total en nature : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 120, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie + $frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 122, 100, 122);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total brute : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(($frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer + $frais->dotationTelephonie + $frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 128, 100, 128);
    
    
                                // Informations sur les passives
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(178, 70, iconv('utf-8','cp1252', "Retenues"));
                                $pdf->Line(110, 71, 197, 71);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Text(110, 78, iconv('utf-8','cp1252', "Taux Impôt : "));
                                $pdf->Text(180, 78, iconv('utf-8','cp1252', (Fonction::RecupererTaux($infoapporteur->codeCom))." %"));
                                $pdf->Line(110, 80, 197, 80);
                                $pdf->Text(110, 84, iconv('utf-8','cp1252', "Impôt : "));
                                $pdf->Text(180, 84, iconv('utf-8','cp1252', number_format(($frais->aibMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 86, 197, 86);
                                $pdf->Text(110, 90, iconv('utf-8','cp1252', "Avance payer ce mois : "));
                                $pdf->Text(180, 90, iconv('utf-8','cp1252', number_format(($frais->recentrembourcer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 92, 197, 92);
                                $pdf->Text(110, 96, iconv('utf-8','cp1252', "Prélèvement : "));
                                $pdf->Text(180, 96, iconv('utf-8','cp1252', number_format(($frais->retenue), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 98, 197, 98);
                                $pdf->Text(110, 102, iconv('utf-8','cp1252', "Nature du Prélèvement : "));
                                $pdf->Text(140, 102, iconv('utf-8','cp1252', $frais->libretenue));
                                $pdf->Line(110, 104, 197, 104);
                                $pdf->Text(110, 108, iconv('utf-8','cp1252', "Carec : "));
                                $pdf->Text(180, 108, iconv('utf-8','cp1252', number_format(($frais->traceCarec), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 110, 197, 110);
                                $pdf->Text(110, 114, iconv('utf-8','cp1252', "Amical : "));
                                $pdf->Text(180, 114, iconv('utf-8','cp1252', number_format(($frais->montantAmical), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 116, 197, 116);
                                $pdf->Text(110, 120, iconv('utf-8','cp1252', "Naf : "));
                                $pdf->Text(180, 120, iconv('utf-8','cp1252', number_format(($frais->naf), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 122, 197, 122);
                                if($frais->compteBloquer > 0){
                                    $pdf->Text(110, 126, iconv('utf-8','cp1252', "Solde à débiter le mois prochain : "));
                                    $pdf->Text(180, 126, iconv('utf-8','cp1252', number_format((abs($frais->compteBloquer)), 0, '.', ' ')." CFA"));
                                }
                                
                                $pdf->Line(110, 128, 197, 128);
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 132, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 132, iconv('utf-8','cp1252', number_format(( $frais->retenue + $frais->montantAmical + $frais->traceCarec + $frais->tracenaf + $frais->aibMoisCalculer + $frais->recentrembourcer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 134, 197, 134);
    
                                $pdf->SetFont("Arial", "B", 8);
    
                                // Total sur les actives
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 144, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 144, iconv('utf-8','cp1252', number_format(($frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 146, 197, 146);
                                
    
                                // Total sur les passives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 153, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 153, iconv('utf-8','cp1252', number_format(( $frais->retenue + $frais->montantAmical + $frais->traceCarec + $frais->tracenaf + $frais->aibMoisCalculer + $frais->recentrembourcer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 155, 197, 155);
    
                                // Total
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 162, iconv('utf-8','cp1252', "Total net  : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 162, iconv('utf-8','cp1252', number_format(($frais->compte), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 164, 197, 164);
    
    
                                // Signature Commercial
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(20, 174, iconv('utf-8','cp1252', "Signature du Conseiller Commercial : "));
                                $pdf->SetTextColor(0,0,0);
    
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(35, 194, iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
    
                                // Signature Société
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(150, 174, iconv('utf-8','cp1252', "Signature de la Société : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Image($soc->signature, 150, 176, -150);
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(160, 194, iconv('utf-8','cp1252', $soc->libelleSociete));
    
                                $pdf->Output('F', $pathd);
                                $fiche_D = $pathd;
                                $lib_fiche_D = $lib;
    
    
                    //////////////////////FIN fiche de paie (DUPLICATA) ////////////////////////////////
    
    
                    //////////////////////Catégorie CONS////////////////////////////////
    
                    if ($infoapporteur->Niveau != "INS" && $infoapporteur->Niveau != "CEQP" && $infoapporteur->Niveau != "RG" && $infoapporteur->Niveau != "CD") {
                        $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
                            // Ce que lui même à reçu sur ses affaires
                            $detail = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                // // MODE TEST
                                
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_".$apporteur->Commercial."_".$datelettrefile;
                                $path = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->signature, 10, 10, -150);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                //////////////////////////////////////////////////////// Les données
                                foreach ($detail as $value){ 
                                    $sombase += $value->base;
                                    $somcom += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1); 
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $infoapporteur->Niveau, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom - ($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                ///////////////////////////////////////////////////////////
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller Commercial", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                // Sauvegarde du pdf
                                $pdf->Output('F', $path);
    
                                // Envoyer par mail le fichier
                                $detail_O = $path;
                                $lib_detail_O = $lib;
                                
                                //////////////////// (DUPLICATA) //////////////////////
    
                                // Ce que lui même à reçu sur ses affaires
                            $detail = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
                                 setlocale(LC_TIME,  'french');
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_DUPLICATA_".$apporteur->Commercial."_".$datelettrefile;
                                $pathd = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->signature, 10, 10, -150);
                                $pdf->Image($soc->duplicata, 130, 5, -250);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                //////////////////////////////////////////////////////// Les données
                                foreach ($detail as $value){ 
                                    $sombase += $value->base;
                                    $somcom += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $infoapporteur->Niveau, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom - ($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                ///////////////////////////////////////////////////////////
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
    
    
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                // Sauvegarde du pdf
                                $pdf->Output('F', $pathd);
                                $detail_D = $pathd;
                                $lib_detail_D = $lib;
    
    
                                ////////////////////FIN (DUPLICATA) ///////////////////
    
                    }
    
                    //////////////////////FIN Catégorie CONS////////////////////////////////
    
                    //////////////////////Catégorie CEQP ou INS ////////////////////////////////
    
                        if ($infoapporteur->Niveau == "CEQP" || $infoapporteur->Niveau == "INS" || $infoapporteur->Niveau == "RG" || $infoapporteur->Niveau == "CD") {
                            $niv = "";
                            $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
                            // Ce que lui même à reçu sur  ses affaires
                            $detaillui = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            if ($infoapporteur->Niveau == "CEQP") {
                                $niv = "CEQP";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeEquipe = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeEquipe;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police','contrats.Agent as apport', 'contrats.Produit as produit',
                                'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.Toc', $apporteur->Commercial)->get(); 
                            }
                            if ($infoapporteur->Niveau == "INS") {
                                $niv = "INS";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeIns = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police','contrats.Agent as apport', 'contrats.Produit as produit', 
                                'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where("commissions.premiervalidation", $apporteur->Commercial)->get();
                            }
                            if ($infoapporteur->Niveau == "RG") {
                                $niv = "RG";
                                // Ce qu'il a gagner en tant chef region
                                $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeRegion;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantRG as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.MontantRG', '!=', 0)
                                ->where('commerciauxes.codeRegion', $codeRG)->get();
                            }
                            
                            if ($infoapporteur->Niveau == "CD") {
                                $niv = "CD";
                                $codeCD = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeCD;
                                $detail = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCD as comm' )
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commissions.MontantCD', '!=', 0)
                                    ->where('commerciauxes.codeCD', $codeCD)->get();
                            }
                            $detailComA = array(); $niva = "";
                            if ($infoapporteur->Niveau == "CD" || $infoapporteur->Niveau == "RG") {
                                $niva = "ANCIEN INS";
                                
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                    
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ins', $apporteur->Commercial)
                                    ->get();
                            }
                            if ($infoapporteur->Niveau == "INS") {
                                $niva = "ANCIEN CEQP";
                                
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ceqp', $apporteur->Commercial)
                                    ->get();
                                
                            }
    
                            setlocale(LC_TIME,  'french');
                                //
                                
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_".$apporteur->Commercial."_".$datelettrefile;
                                $path = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 10, -150);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
                                $sombase2 = 0;
                                $somcom2 = 0;
                                $sombase1 = 0;
                                $somcom1 = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                /////////////////////////////////////////////////////////////////////////////////////
    
                                foreach ($detaillui as $value){ 
                                    $sombase1 += $value->base;
                                    $somcom1 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, "CONS", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase1, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom1, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 - ($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(20);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(15,6,'Apporteur', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                foreach ($detail as $value){ 
                                    $sombase2 += $value->base;
                                    $somcom2 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $niv, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                foreach ($detailComA as $value){
                                    if( strtotime($value->DateDebutEffet) < strtotime("01-01-2023")  )
                                    {
                                        $sombase2 += $value->base;
                                        $somcom2 += $value->comm;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $niva, 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
                                        
                                }
    
                                // Sous catégorie 2
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase2, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom2, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 - ($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                ////////////////////////////////////////////////////////////////////////////////////
    
                                $somcom = $somcom1 + $somcom2;
                                $sombase = $sombase1 + $sombase2;
    
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
    
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
    
                                // Sauvegarde du pdf
                                $pdf->Output('F', $path);
    
                                $detail_O = $path;
                                $lib_detail_O = $lib;
    
                                // ////////////////////// (DUPLICATA) //////////////////////////////////////////////////////////////////////////////////  
                                $niv = "";
                            // Ce que lui même à reçu sur  ses affaires
                            $detaillui = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            if ($infoapporteur->Niveau == "CEQP") {
                                $niv = "CEQP";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeEquipe = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeEquipe;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.Toc', $apporteur->Commercial)->get(); 
                            }
                            if ($infoapporteur->Niveau == "INS") {
                                $niv = "INS";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeIns = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where("commissions.premiervalidation", $apporteur->Commercial)->get();
                            }
                            if ($infoapporteur->Niveau == "RG") {
                                $niv = "RG";
                                // Ce qu'il a gagner en tant chef region
                                $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeRegion;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantRG as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.MontantRG', '!=', 0)
                                ->where('commerciauxes.codeRegion', $codeRG)->get();
                            }
                            
                            if ($infoapporteur->Niveau == "CD") {
                                $niv = "CD";
                                $codeCD = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeCD;
                                $detail = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCD as comm' )
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commissions.MontantCD', '!=', 0)
                                    ->where('commerciauxes.codeCD', $codeCD)->get();
                            }
                            $detailComA = array();$niva = "";
                            if ($infoapporteur->Niveau == "CD" || $infoapporteur->Niveau == "RG") {
                                $niva = "ANCIEN INS";
                                
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                    
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ins', $apporteur->Commercial)
                                    ->get();
                                
                                
                            }
                            
                            if ($infoapporteur->Niveau == "INS") {
                                $niva = "ANCIEN CEQP";
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                    
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ceqp', $apporteur->Commercial)
                                    ->get();
                            }
    
                            setlocale(LC_TIME,  'french');
                                //
                                
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_DUPLICATA_".$apporteur->Commercial."_".$datelettrefile;
                                $pathd = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 10, -150);
                                $pdf->Image($soc->duplicata, 130, 5, -250);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION ");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
                                $sombase2 = 0;
                                $somcom2 = 0;
                                $sombase1 = 0;
                                $somcom1 = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                /////////////////////////////////////////////////////////////////////////////////////
    
                                foreach ($detaillui as $value){ 
                                    $sombase1 += $value->base;
                                    $somcom1 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, "CONS", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase1, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom1, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 - ($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(20);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(15,6,'Apporteur', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                foreach ($detail as $value){ 
                                    $sombase2 += $value->base;
                                    $somcom2 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $niv, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                foreach ($detailComA as $value){
                                    if( strtotime($value->DateDebutEffet) < strtotime("01-01-2023")  )
                                    {
                                        $sombase2 += $value->base;
                                        $somcom2 += $value->comm;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $niva, 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
                                        
                                }
    
                                // Sous catégorie 2
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase2, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom2, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 - ($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                ////////////////////////////////////////////////////////////////////////////////////
    
                                $somcom = $somcom1 + $somcom2;
                                $sombase = $sombase1 + $sombase2;
    
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
                                
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
    
                                // Sauvegarde du pdf
                                $pdf->Output('F', $pathd);
    
                                $detail_D = $pathd;
                                $lib_detail_D = $lib;
                                
                        } 
    
    
                    //////////////////////////////FIN Catégorie CEQP ou INS////////////////////////
    
                        
    
                    // Sauvegarde 
                    $addDocD = new Document();
                    $addDocD->path = $fiche_O;
                    $addDocD->libelle = $lib_fiche_O;
                    $addDocD->pathFD = $fiche_D;
                    $addDocD->libelleFD = $lib_fiche_D;
                    $addDocD->pathD = $detail_O;
                    $addDocD->libelleD = $lib_detail_O;
                    $addDocD->pathDD = $detail_D;
                    $addDocD->libelleDD = $lib_detail_D;
                    $addDocD->periode = $moisCom; //$datelettrefile;
                    $addDocD->Agent = $apporteur->Commercial;
                    $addDocD->save();
                    
                }
            } 

           
            //  Vidée tous les comptes apporteurs du mois
            
            foreach ($list as $apporteur) {
                if(!isset(DB::table('tracecomptes')->where('Commercial', $apporteur->Commercial)->where('moiscalculer', $moisCom)->first()->id)){
                    // mettre à jour la table compte
                    $occurence = json_encode(DB::table('compteagents')->where('Agent', $apporteur->Commercial)->first());
                    $addt = new Tracecompte();
                    $addt->contenu = "Compte réinitialisé : ".$occurence;
                    $addt->moiscalculer = $moisCom;
                    $addt->Commercial = $apporteur->Commercial;
                    $addt->save();
                    DB::table('compteagents')->where('Agent', $apporteur->Commercial)->update([
                        'compteMoisCalculer' => 0,
                        'aibMoisCalculer' => 0,
                        'compteNetapayerMoisCalculer' => 0,
                        'compteEncadrementMoisCalculer' => 0,
                        'AutreCommissionMoisCalculer' => 0,
                        'MoisCalculer' => "",
                        'compte' => 0,
                        'avancesancien' => 0,
                        'bonus' => 0,
                        'retenue' => 0,
                        'libretenue' => "",
                        'periodicite' => "",
                        'statutEng' => 0,
                        'recentrembourcer' => 0,
                        'dotationCarburant' => 0,
                        'dotationTelephonie' => 0,
                        'traceCarec' => 0,
                        'tracesAmical' => 0,
                        'tracenaf' => 0,
                        'statueValide' => 0
                    ]);
                }
            }
            
            
            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = $moisCom;
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
                
            // Validé tous les commissions de ce mois
                DB::table('commissions')->where("ctrl", 1)
                ->where('TypeCommission', 'i')->update([
                    "ctrl" => 2
                ]);
            
            $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mail);
            }
            $allSup = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "cfvi")->get();
            foreach ($allSup as $value) {
                array_push($mails, $value->mail);
            }
            array_push($mails);
            
            SendMail::sendnotificationfin("rogerkpovi@gmail.com", $mails, "Commissions. NSIA VIE ASSURANCES", []); */
            
            flash("Vous avez confirmé le règlement des commissions de ce mois. Les fiches de paie sont en cours d'envoi..");
            TraceController::setTrace("Vous avez confirmé le règlement des commissions de ce mois. Les fiches de paie sont en cours d'envoi..", session("utilisateur")->idUser);
            ProcessusComInd::saveprocessus("tres");
            return Back(); 
        }
    }
    
    public static function setgenerationfiche(){
        // La confirmation concerne les commissions calculé du mois actuel

        // Sauvegarder le formulaire du mois
        $fiche_O = "";
        $fiche_D = "";
        $detail_D = "";
        $detail_O = "";
        $lib_fiche_O = "";
        $lib_detail_O = "";
        $lib_fiche_D = "";
        $lib_detail_D = "";
        $moisCom = view()->shared('periode');
        $datelettre = utf8_encode(strtoupper(view()->shared('periodelettre')));
        $datelettrefile = utf8_encode(strtoupper(view()->shared('periodelettre')));
        
            // Parcourir les commissions
            $a = 0;

            //foreach ($commission as $comm) {
                // mettre à jour la table commission par oui
                DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', "oui")
                ->where('confirmertresorerie', null)
                ->where('TypeCommission', 'i')
                ->where("ctrl", 1)
                ->update([
                    "confirmertresorerie" => "oui"
                ]);
              //  $a++;
            //}
                    
            $list = DB::table('compteagents')
            ->join('commerciauxes', 'compteagents.Agent', '=', 'commerciauxes.codecom')
            ->select('compteagents.Agent as Commercial')
            ->where("statueValide", 1)
            ->whereNotIn('Agent', [1,2,3, 101])->get();
        
            // récap trésorerie 
            $soc = DB::table('societes')->where("id", 1)->first();
                            
            // Ce que lui même à reçu sur ses affaires
            setlocale(LC_TIME,  'french');
    
            $titre = "RAPPORT SUR LE PAIEMENT DES COMMISSIONS INDIVIDUELLES";
            $lib = "RAPPORT_SUR_LE_PAIEMENT_".$datelettrefile;
            $path = "document/recapcomission/".$lib.".pdf";
            $pdf = new PDFF();
            //$pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
            $pdf->AddPage('L', '', 0, '', 15, 15, 16, 16, 9, 9);
            $pdf->SetTitle($titre , true);
            $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
            $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
            $pdf->SetSubject($titre, true);
            $pdf->Image($soc->signature, 10, 10, -150);
            $pdf->SetFont("Arial", "B", 12);
            $pdf->Text(80, 17, $titre);
            $pdf->Ln(25);
            
            $pdf->SetFont("Arial", "B", 9);
            $pdf->setFillColor(255,255,255);
            $pdf->Cell(30,6,'Mois de :', 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(25,6, $datelettrefile, 0, 0, 'C', 1);
            $pdf->Cell(5,6,'', 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(40,6,iconv('utf-8','cp1252','Date de paiement : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', date("d/m/Y")), 0, 0, 'C', 1);
            $pdf->Cell(5,6,'', 0, 0, 'C', 1);
			$pdf->Cell(35,6,iconv('utf-8','cp1252','Montant Total Payé : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
			$montant_total_paye = DB::table('compteagents')->where("statueValide", 1)->sum('compte');	
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($montant_total_paye, 0, '.', ' ')), 0, 0, 'C', 1);
            $pdf->Ln(12);
            $pdf->SetFont("Arial", "B", 9);
            $commissions_all = DB::table('commissions')
				->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
				->where('commissions.Statut', view()->shared('periode'))
				->where('ctrl', 1)
				->where('confirmercalcule', 'oui')
				->select(DB::raw('
					SUM(CASE WHEN commissions.Toc IS NOT NULL THEN MontantCEQ ELSE 0 END) +
					SUM(CASE WHEN commissions.premiervalidation IS NOT NULL THEN MontantInspecteur ELSE 0 END) +
					SUM(CASE WHEN commissions.regionCom IS NOT NULL THEN MontantRG ELSE 0 END) +
					SUM(CASE WHEN commissions.cdCom IS NOT NULL THEN MontantCD ELSE 0 END) +
					SUM(MontantConseiller) as commissions
				'))
				->first()->commissions;
            $pdf->Cell(35,6,iconv('utf-8','cp1252','Montant Total Brute : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($commissions_all, 0, '.', ' ')), 0, 0, 'C', 1);
            $pdf->Cell(5,6,'', 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
            $montant_total_aib = DB::table('compteagents')->where("statueValide", 1)->sum('aibMoisCalculer');
            $pdf->Cell(20,6,iconv('utf-8','cp1252','Montant AIB : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($montant_total_aib, 0, '.', ' ')), 0, 0, 'C', 1);
            $pdf->Cell(5,6,'', 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
        $montant_total_naf = DB::table('compteagents')->where("statueValide", 1)->sum('tracenaf');
            $pdf->Cell(20,6,iconv('utf-8','cp1252','Montant NAF : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($montant_total_naf, 0, '.', ' ')), 0, 0, 'C', 1);
            $pdf->Cell(5,6,'', 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
        $montant_total_carec = DB::table('compteagents')->where("statueValide", 1)->sum('traceCarec');
            $pdf->Cell(26,6,iconv('utf-8','cp1252','Montant CAREC : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($montant_total_carec, 0, '.', ' ')), 0, 0, 'C', 1);
            $pdf->Cell(5,6,'', 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
        $montant_total_amical = DB::table('compteagents')->where("statueValide", 1)->sum('tracesAmical');
            $pdf->Cell(26,6,iconv('utf-8','cp1252','Montant AMICAL : '), 0, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($montant_total_amical, 0, '.', ' ')), 0, 0, 'C', 1);
            
            $pdf->setFillColor(255,255,255);
            $pdf->Ln(12);
            $pdf->setFillColor(230,230,230);
            $pdf->SetFont("Arial", "B", 9);
        $nombreconcer = DB::table('compteagents')->where("statueValide", 1)->count();
            $pdf->Cell(60,6,iconv('utf-8','cp1252','Nombre total de Conseillers payés : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombreconcer, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(60,6,iconv('utf-8','cp1252','Montant total payé : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format($montant_total_paye, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
        $nombre_police = DB::table('commissions')
			->where('Statut', view()->shared('periode'))
            ->where('ctrl', 1)
			->where('confirmercalcule', "oui")
			->distinct()
    		->pluck('NumPolice')
            ->count();
            $pdf->Cell(60,6,iconv('utf-8','cp1252','Nombre total de police concernées : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_police, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(50,6,iconv('utf-8','cp1252','Montant payé par branche : '), 1, 0, 'C', 1);
            //$pdf->Cell(20,6,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Ln(6);
            $pdf->SetFont("Arial", "I", 9);
        $nombre_coord = DB::table('compteagents')
                            ->join("commerciauxes", "commerciauxes.codeCom", "=", "compteagents.Agent")
                            ->where("statueValide", 1)
                            ->whereNotIn("commerciauxes.codeCom", [1, 2, 3])
                            ->whereIn("commerciauxes.Niveau", ["CD", "RG"])
                            ->count();
            $pdf->Cell(13,24,iconv('utf-8','cp1252','dont : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Coordonateurs : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_coord, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(13,24,iconv('utf-8','cp1252','dont : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Paiement par MoMo : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Cell(13,24,iconv('utf-8','cp1252','dont : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "B", 9);
			$nombre_police_epargne = DB::table('commissions')->join("contrats", "contrats.police", "=", "commissions.NumPolice")
				->where('commissions.Statut', view()->shared('periode'))
				->where('ctrl', 1)
				->whereIn("Produit", [2160,2180,2300,2400,2410,2420,2430,2440,2450,2460,2600,3620,6100,6120,6200,7620,6150] )
				->where('confirmercalcule', "oui")
				->distinct()
    			->pluck('NumPolice')
				->count();
			$commissions_epargne = DB::table('commissions')
				->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
				->where('commissions.Statut', view()->shared('periode'))
				->where('ctrl', 1)
				->where('confirmercalcule', 'oui')
				->whereIn('Produit', [2160,2180,2300,2400,2410,2420,2430,2440,2450,2460,2600,3620,6100,6120,6200,7620,6150])
				->select(DB::raw('
					SUM(CASE WHEN commissions.Toc IS NOT NULL THEN MontantCEQ ELSE 0 END) +
					SUM(CASE WHEN commissions.premiervalidation IS NOT NULL THEN MontantInspecteur ELSE 0 END) +
					SUM(CASE WHEN commissions.regionCom IS NOT NULL THEN MontantRG ELSE 0 END) +
					SUM(CASE WHEN commissions.cdCom IS NOT NULL THEN MontantCD ELSE 0 END) +
					SUM(MontantConseiller) as commissions
				'))
				->first()->commissions;
		
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Polices Epargne : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_police_epargne, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(50,6,iconv('utf-8','cp1252', number_format($commissions_epargne, 0, '.', ' ')), 1, 0, 'C', 1);
            //$pdf->Cell(20,6,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Ln(6);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
            $nombre_ins = DB::table('compteagents')
                            ->join("commerciauxes", "commerciauxes.codeCom", "=", "compteagents.Agent")
                            ->where("statueValide", 1)
                            ->whereNotIn("commerciauxes.codeCom", [1, 2, 3])
                            ->whereIn("commerciauxes.Niveau", ["INS"])
                            ->count();
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Inspecteurs / Chefs Bureaux : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_ins, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Paiement par chèques : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,6,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
		$nombre_police_mixte = DB::table('commissions')->join("contrats", "contrats.police", "=", "commissions.NumPolice")
			
            ->where('commissions.Statut', view()->shared('periode'))
            ->where('ctrl', 1)
			->whereIn("Produit", [3100,3120,7960] )
			->where('confirmercalcule', "oui")
			->distinct()
    		->pluck('NumPolice')
            ->count();
		
		$commissions_mixte = DB::table('commissions')
				->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
				->where('commissions.Statut', view()->shared('periode'))
				->where('ctrl', 1)
				->where('confirmercalcule', 'oui')
				->whereIn('Produit', [3100,3120,7960])
				->select(DB::raw('
					SUM(CASE WHEN commissions.Toc IS NOT NULL THEN MontantCEQ ELSE 0 END) +
					SUM(CASE WHEN commissions.premiervalidation IS NOT NULL THEN MontantInspecteur ELSE 0 END) +
					SUM(CASE WHEN commissions.regionCom IS NOT NULL THEN MontantRG ELSE 0 END) +
					SUM(CASE WHEN commissions.cdCom IS NOT NULL THEN MontantCD ELSE 0 END) +
					SUM(MontantConseiller) as commissions
				'))
				->first()->commissions;
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Polices Mixte : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_police_mixte, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(50,6,iconv('utf-8','cp1252', number_format($commissions_mixte, 0, '.', ' ')), 1, 0, 'C', 1);
            //$pdf->Cell(20,6,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Ln(6);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
            $nombre_ceq = DB::table('compteagents')
                            ->join("commerciauxes", "commerciauxes.codeCom", "=", "compteagents.Agent")
                            ->where("statueValide", 1)
                            ->whereNotIn("commerciauxes.codeCom", [1, 2, 3])
                            ->whereIn("commerciauxes.Niveau", ["CEQP"])
                            ->count();
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Chefs d\'Equipe : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_ceq, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
            $pdf->Cell(47,12,iconv('utf-8','cp1252','Paiement par virement : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(20,12,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
		$nombre_police_prevoyance = DB::table('commissions')->join("contrats", "contrats.police", "=", "commissions.NumPolice")
			->where('commissions.Statut', view()->shared('periode'))
            ->where('ctrl', 1)
			->whereIn("Produit", [5100,5120,5140,5155,5160,5330,7400,7420,7520,1120,1130,1160,1165,1620,1630,1640,1660,1665,1680,3330,5100,5120,5200,5300,7520,7550,7580,7730] )
			->where('confirmercalcule', "oui")
            ->distinct()
    		->pluck('NumPolice')
			->count();
		
			$commissions_prevoyance = DB::table('commissions')
				->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
				->where('commissions.Statut', view()->shared('periode'))
				
				->where('ctrl', 1)
				->where('confirmercalcule', 'oui')
				->whereIn('Produit', [5100,5120,5140,5155,5160,5330,7400,7420,7520,1120,1130,1160,1165,1620,1630,1640,1660,1665,1680,3330,5100,5120,5200,5300,7520,7550,7580,7730])
				->select(DB::raw('
					SUM(CASE WHEN commissions.Toc IS NOT NULL THEN MontantCEQ ELSE 0 END) +
					SUM(CASE WHEN commissions.premiervalidation IS NOT NULL THEN MontantInspecteur ELSE 0 END) +
					SUM(CASE WHEN commissions.regionCom IS NOT NULL THEN MontantRG ELSE 0 END) +
					SUM(CASE WHEN commissions.cdCom IS NOT NULL THEN MontantCD ELSE 0 END) +
					SUM(MontantConseiller) as commissions
				'))
				->first()->commissions;
		
            $pdf->Cell(47,12,iconv('utf-8','cp1252','Polices Prévoyance : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,12,iconv('utf-8','cp1252', number_format($nombre_police_prevoyance, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(50,12,iconv('utf-8','cp1252', number_format($commissions_prevoyance, 0, '.', ' ')), 1, 0, 'C', 1);
            //$pdf->Cell(20,12,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            $pdf->Ln(6);
            $pdf->Cell(13);
            $pdf->SetFont("Arial", "B", 9);
            $nombre_cons = DB::table('compteagents')
                            ->join("commerciauxes", "commerciauxes.codeCom", "=", "compteagents.Agent")
                            ->where("statueValide", 1)
                            ->whereNotIn("commerciauxes.codeCom", [1, 2, 3])
                            ->whereNotIn("commerciauxes.Niveau", ["CD", "RG", "CEQP", "INS"])
                            ->count();
            $pdf->Cell(47,6,iconv('utf-8','cp1252','Conseillers simples : '), 1, 0, 'C', 1);
            $pdf->SetFont("Arial", "I", 9);
            $pdf->Cell(10,6,iconv('utf-8','cp1252', number_format($nombre_cons, 0, '.', ' ')), 1, 0, 'C', 1);
            $pdf->Cell(145);
            $pdf->Ln(6);
            //$pdf->Cell(40,6,iconv('utf-8','cp1252','Montant total d\'AIB collecté : '), 1, 0, 'C', 1);
            //$pdf->Cell(18,6,iconv('utf-8','cp1252',' 0 '), 1, 0, 'C', 1);
            
        
        
            $pdf->Ln(20);
            $pdf->SetFont("Arial", "", 10);
            $pdf->Cell(40,6,"Validation R/SE : ", 0, 0, 'L');
            $pdf->Cell(50,6,"", 0, 0, 'L');
            $pdf->Cell(40,6,"Validation C/SPPE : ", 0, 0, 'L');
            $pdf->Cell(50,6,"", 0, 0, 'L');
            $pdf->Cell(40,6,iconv('utf-8','cp1252',"Validation DT"), 0, 0, 'L');
            $pdf->Cell(60,6," ", 0, 0, 'C');
    
            $pdf->Ln(30);
    
            $pdf->SetFont("Arial", "", 10);
            $pdf->Cell(40,6,"Validation DG : ", 0, 0, 'L');
            $pdf->Cell(50,6,"", 0, 0, 'L');
            $pdf->Cell(40,6,"Validation CDAF : ", 0, 0, 'L');
            $pdf->Cell(50,6,"", 0, 0, 'L');
            $pdf->Cell(40,6,iconv('utf-8','cp1252',"Validation Trésorerie"), 0, 0, 'L');
            $pdf->Cell(60,6," ", 0, 0, 'C');
            // Sauvegarde du pdf
            $pdf->Output('F', $path);
		
			
            // Etablir Fiches de paie pour les apporteurs
            foreach ($list as $apporteur) {

                $infoapporteur = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first();
                
                if(!isset(DB::table('documents')->where('Agent', $infoapporteur->codeCom)->where('periode', $moisCom)->first()->id)){
               
                    //////////////////////FICHE DE PAIE////////////////////////////////
                                 setlocale(LC_TIME,  'french');
    
                                
                                //
    
                                // Création du PDF
    
                                $titre = "FICHE DE PAIE CONSEILLER COMMERCIAL ";
                                $lib = "FICHEPAIE_".$apporteur->Commercial."_".$datelettrefile;
                                $path = "document/commission/".$lib.".pdf";
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                //$pdf->Image($soc->logo, 10, 15, -150); https://fees.nsiaviebenin.com/assets/images/logo.png
                                $pdf->Image(public_path()."/".$soc->logo, 10, 15, -150);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 22, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 42, "SOCIETE :");
                                $pdf->Line(10, 43, 60, 43);
                                $pdf->Text(70, 42, "CONSEILLER COMMERCIAL :");
                                $pdf->Line(70, 43, 140, 43);
                                $pdf->Text(155, 42, "PERIODE :");
                                $pdf->Line(155, 43, 197, 43);
    
                                // Information sur société
    
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(10, 48,iconv('utf-8','cp1252','Nom :'));
                                $pdf->Text(30, 48,iconv('utf-8','cp1252', $soc->libelleSociete));
                                $pdf->Text(10, 52,iconv('utf-8','cp1252', "Adresse :"));
                                $pdf->Text(30, 52,iconv('utf-8','cp1252', $soc->adresse));
                                $pdf->Text(10, 56,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(30, 56,iconv('utf-8','cp1252', $soc->email));
    
                                // Information sur le commercial
    
                                $pdf->Text(70, 48,iconv('utf-8','cp1252','Code :'));
                                $pdf->Text(90, 48,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(70, 52,iconv('utf-8','cp1252', "Nom :"));
                                $pdf->Text(90, 52,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                
                                $pdf->Text(70, 56,iconv('utf-8','cp1252','Statut :'));
                                $pdf->Text(90, 56,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
                                
                                $pdf->Text(70, 60,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(90, 60,iconv('utf-8','cp1252', $infoapporteur->mail));
                                
                                $pdf->Text(70, 64,iconv('utf-8','cp1252','IFU :'));
                                $pdf->Text(90, 64,iconv('utf-8','cp1252', (Fonction::RecupererTaux($infoapporteur->codeCom))." %"));
    
                                // Information sur la période
    
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $frais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                // Informations sur les actives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->SetDrawColor(211, 211, 211);
                                $pdf->Text(10, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(80, 70, iconv('utf-8','cp1252', "Base"));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Line(10, 71, 100, 71);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(10);
                                $pdf->Text(10, 78, iconv('utf-8','cp1252', "Montant Commission : "));
                                $pdf->Text(80, 78, iconv('utf-8','cp1252', number_format(($frais->compteMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 80, 100, 80);

                                $pdf->Text(10, 84, iconv('utf-8','cp1252', "Commission Encadrement : "));
                                $pdf->Text(80, 84, iconv('utf-8','cp1252', number_format(($frais->compteEncadrementMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 86, 100, 86);

                                $pdf->Text(10, 90, iconv('utf-8','cp1252', "Fixe : "));
                                $pdf->Text(80, 90, iconv('utf-8','cp1252', number_format(($frais->fixe), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 92, 100, 92);
                                
                                $pdf->Text(10, 96, iconv('utf-8','cp1252', "Bonus : "));
                                $pdf->Text(80, 96, iconv('utf-8','cp1252', number_format(($frais->bonus), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 98, 100, 98);
                                $pdf->Text(10, 102, iconv('utf-8','cp1252', "Nature du bonus : "));
								$pdf->AdjustText(80, 102, $frais->libbonus, 50);
                                //$pdf->Text(80, 102, iconv('utf-8','cp1252', $frais->libbonus));
                                $pdf->Line(10, 104, 100, 104);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 108, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $gains = $frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer;
                                $pdf->Text(80, 108, iconv('utf-8','cp1252', number_format(($gains), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 110, 100, 110);
                                
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Text(10, 114, iconv('utf-8','cp1252', "Dotation Téléphonie (nature) : "));
                                $pdf->Text(80, 114, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 116, 100, 116);
                                $pdf->Text(10, 120, iconv('utf-8','cp1252', "Dotation Carburant (nature) : "));
                                $pdf->Text(80, 120, iconv('utf-8','cp1252', number_format(($frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 122, 100, 122);
                                
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total en nature : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie + $frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 128, 100, 128);

                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 132, iconv('utf-8','cp1252', "Solde antérieur : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 132, iconv('utf-8','cp1252', number_format(($frais->compteBloquerBackup), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 134, 100, 134);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 138, iconv('utf-8','cp1252', "Total brute : "));
                                $pdf->SetTextColor(0,0,0);
                                $brute = $gains + $frais->dotationTelephonie + $frais->dotationCarburant + $frais->compteBloquerBackup;
                                $pdf->Text(80, 138, iconv('utf-8','cp1252', number_format(($brute), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 140, 100, 140);


    
                                
                                if($frais->compteBloquer > 0){
                                    // Les engagements non honoré
                                    $pdf->Ln(10);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 150, iconv('utf-8','cp1252', "Les engagements non honorés :"));
                                    $pdf->Line(10, 152, 100, 152);
                                    // défalcation
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 156, iconv('utf-8','cp1252', "Défalcation : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(80, 156, iconv('utf-8','cp1252', number_format(($frais->retenue), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 158, 100, 158);

                                    // naf
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 162, iconv('utf-8','cp1252', "Naf : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(80, 162, iconv('utf-8','cp1252', number_format(($frais->impayernaf * $frais->naf), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 164, 100, 164);

                                    // avances
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 168, iconv('utf-8','cp1252', "Avance : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $impayeavance = $frais->impayeravances * ($frais->duree > 0 ? $frais->avances / $frais->duree : $frais->avances);
                                    $pdf->Text(80, 168, iconv('utf-8','cp1252', number_format(( $impayeavance ), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 170, 100, 170);
                                    $pdf->SetFillColor(0, 0, 0);
                                }


    
                                // Informations sur les passives
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(178, 70, iconv('utf-8','cp1252', "Retenues"));
                                $pdf->Line(110, 71, 197, 71);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Text(110, 78, iconv('utf-8','cp1252', "Impôt sur le gains : "));
                                $pdf->Text(180, 78, iconv('utf-8','cp1252', number_format(($frais->aibMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 80, 197, 80);
                                $pdf->Text(110, 84, iconv('utf-8','cp1252', "Défalcation : "));
                                $pdf->Text(180, 84, iconv('utf-8','cp1252', number_format(($frais->traceretenue), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 86, 197, 86);
                                $pdf->Text(110, 90, iconv('utf-8','cp1252', "Nature de la défalcation : "));
								$pdf->AdjustText(140, 90, $frais->libretenue, 57);
                                //$pdf->Text(180, 90, iconv('utf-8','cp1252', $frais->libretenue));
                                $pdf->Line(110, 92, 197, 92);
                                $pdf->Text(110, 96, iconv('utf-8','cp1252', "Naf : "));
                                $pdf->Text(180, 96, iconv('utf-8','cp1252', number_format(($frais->tracenaf), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 98, 197, 98);
                                $pdf->Text(110, 102, iconv('utf-8','cp1252', "Avance payer ce mois : "));
                                $pdf->Text(180, 102, iconv('utf-8','cp1252', number_format($frais->recentrembourcer, 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 104, 197, 104);
                                $pdf->Text(110, 108, iconv('utf-8','cp1252', "Carec : "));
                                $pdf->Text(180, 108, iconv('utf-8','cp1252', number_format(($frais->traceCarec), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 110, 197, 110);
                                $pdf->Text(110, 114, iconv('utf-8','cp1252', "Amical : "));
                                $pdf->Text(180, 114, iconv('utf-8','cp1252', number_format(($frais->tracesAmical), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 116, 197, 116);
                                $pdf->Line(110, 122, 197, 122);
                                $pdf->Line(110, 128, 197, 128);
                                $pdf->Line(110, 134, 197, 134);
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 138, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $retenues = $frais->traceretenue + $frais->tracesAmical + $frais->traceCarec + $frais->tracenaf + $frais->aibMoisCalculer + $frais->recentrembourcer;
                                $pdf->Text(180, 138, iconv('utf-8','cp1252', number_format(( $retenues ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 140, 197, 140);
    
                                $pdf->SetFont("Arial", "B", 8);
    
                                // Total sur les actives
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 150, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 150, iconv('utf-8','cp1252', number_format(($brute), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 152, 197, 152);
                                
    
                                // Total sur les passives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 156, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 156, iconv('utf-8','cp1252', number_format(( $retenues ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 158, 197, 158);
    
                                // Retention 
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 162, iconv('utf-8','cp1252', "Retention pour engagement non honoré : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 162, iconv('utf-8','cp1252', number_format(($frais->compteBloquer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 164, 197, 164);

                                // Total
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 168, iconv('utf-8','cp1252', "Total net  : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 168, iconv('utf-8','cp1252', number_format(($frais->compte), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 170, 197, 170);
    
    
                                // Signature Commercial
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(20, 200, iconv('utf-8','cp1252', "Signature du Conseiller Commercial : "));
                                $pdf->SetTextColor(0,0,0);
    
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(35, 220, iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
    
                                // Signature Société
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(150, 200, iconv('utf-8','cp1252', "Signature de la Société : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Image($soc->signature, 150, 202, -150);
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(160, 220, iconv('utf-8','cp1252', $soc->libelleSociete));
    
                                $pdf->Output('F', $path);
                                $fiche_O = $path;
                                $lib_fiche_O = $lib;
                    
                                
                    //}
                    //////////////////////FIN fiche de paie////////////////////////////////
    
                    //////////////////////FICHE DE PAIE (DUPLICATA) ////////////////////////////////
                    // Ce que lui même à reçu sur ses affaires
                            
                                setlocale(LC_TIME,  'french');
    
                                // Création du PDF
    
                                $titre = "FICHE DE PAIE CONSEILLER COMMERCIAL ";
                                $lib = "FICHEPAIE_DUPLICATA_".$apporteur->Commercial."_".$datelettrefile;
                                $pathd = 'document/commission/'.$lib.'.pdf';
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 15, -150);
                                $pdf->Image($soc->duplicata, 130, 5, -250);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 22, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 42, "SOCIETE :");
                                $pdf->Line(10, 43, 60, 43);
                                $pdf->Text(70, 42, "CONSEILLER COMMERCIAL :");
                                $pdf->Line(70, 43, 140, 43);
                                $pdf->Text(155, 42, "PERIODE :");
                                $pdf->Line(155, 43, 197, 43);
    
                                // Information sur société
    
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(10, 48,iconv('utf-8','cp1252','Nom :'));
                                $pdf->Text(30, 48,iconv('utf-8','cp1252', $soc->libelleSociete));
                                $pdf->Text(10, 52,iconv('utf-8','cp1252', "Adresse :"));
                                $pdf->Text(30, 52,iconv('utf-8','cp1252', $soc->adresse));
                                $pdf->Text(10, 56,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(30, 56,iconv('utf-8','cp1252', $soc->email));
    
                                // Information sur le commercial
    
                                $pdf->Text(70, 48,iconv('utf-8','cp1252','Code :'));
                                $pdf->Text(90, 48,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(70, 52,iconv('utf-8','cp1252', "Nom :"));
                                $pdf->Text(90, 52,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                
                                $pdf->Text(70, 56,iconv('utf-8','cp1252','Statut :'));
                                $pdf->Text(90, 56,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
                                
                                $pdf->Text(70, 60,iconv('utf-8','cp1252','Email :'));
                                $pdf->Text(90, 60,iconv('utf-8','cp1252', $infoapporteur->mail));
                                
                                $pdf->Text(70, 64,iconv('utf-8','cp1252','IFU :'));
                                $pdf->Text(90, 64,iconv('utf-8','cp1252', (Fonction::RecupererTaux($infoapporteur->codeCom))." %"));
    
                                // Information sur la période
    
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $frais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                // Informations sur les actives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->SetDrawColor(211, 211, 211);
                                $pdf->Text(10, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(80, 70, iconv('utf-8','cp1252', "Base"));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Line(10, 71, 100, 71);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(10);
                                $pdf->Text(10, 78, iconv('utf-8','cp1252', "Montant Commission : "));
                                $pdf->Text(80, 78, iconv('utf-8','cp1252', number_format(($frais->compteMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 80, 100, 80);

                                $pdf->Text(10, 84, iconv('utf-8','cp1252', "Commission Encadrement : "));
                                $pdf->Text(80, 84, iconv('utf-8','cp1252', number_format(($frais->compteEncadrementMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 86, 100, 86);
					
								$pdf->Text(10, 90, iconv('utf-8','cp1252', "Fixe : "));
                                $pdf->Text(80, 90, iconv('utf-8','cp1252', number_format(($frais->fixe), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 92, 100, 92);
                                
                                $pdf->Text(10, 96, iconv('utf-8','cp1252', "Bonus : "));
                                $pdf->Text(80, 96, iconv('utf-8','cp1252', number_format(($frais->bonus), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 98, 100, 98);
                                $pdf->Text(10, 102, iconv('utf-8','cp1252', "Nature du bonus : "));
								$pdf->AdjustText(80, 102, $frais->libbonus, 50);
                                //$pdf->Text(80, 102, iconv('utf-8','cp1252', $frais->libbonus));
                                $pdf->Line(10, 104, 100, 104);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 108, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $gains = $frais->bonus + $frais->fixe + $frais->AutreCommissionMoisCalculer + $frais->compteMoisCalculer + $frais->compteEncadrementMoisCalculer;
                                $pdf->Text(80, 108, iconv('utf-8','cp1252', number_format(($gains), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 110, 100, 110);
                                
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Text(10, 114, iconv('utf-8','cp1252', "Dotation Téléphonie (nature) : "));
                                $pdf->Text(80, 114, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 116, 100, 116);
                                $pdf->Text(10, 120, iconv('utf-8','cp1252', "Dotation Carburant (nature) : "));
                                $pdf->Text(80, 120, iconv('utf-8','cp1252', number_format(($frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 122, 100, 122);
                                
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total en nature : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(($frais->dotationTelephonie + $frais->dotationCarburant), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 128, 100, 128);

                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 132, iconv('utf-8','cp1252', "Solde antérieur : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 132, iconv('utf-8','cp1252', number_format(($frais->compteBloquerBackup), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 134, 100, 134);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 138, iconv('utf-8','cp1252', "Total brute : "));
                                $pdf->SetTextColor(0,0,0);
                                $brute = $gains + $frais->dotationTelephonie + $frais->dotationCarburant + $frais->compteBloquerBackup;
                                $pdf->Text(80, 138, iconv('utf-8','cp1252', number_format(($brute), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 140, 100, 140);


    
                                
                                if($frais->compteBloquer > 0){
                                    // Les engagements non honoré
                                    $pdf->Ln(10);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 150, iconv('utf-8','cp1252', "Les engagements non honorés :"));
                                    $pdf->Line(10, 152, 100, 152);
                                    // défalcation
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 156, iconv('utf-8','cp1252', "Défalcation : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(80, 156, iconv('utf-8','cp1252', number_format(($frais->retenue), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 158, 100, 158);

                                    // naf
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 162, iconv('utf-8','cp1252', "Naf : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(80, 162, iconv('utf-8','cp1252', number_format(($frais->impayernaf * $frais->naf), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 164, 100, 164);

                                    // avances
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $pdf->Text(10, 168, iconv('utf-8','cp1252', "Avance : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->SetFillColor(211, 211, 211);
                                    $impayeavance = $frais->impayeravances * ($frais->duree > 0 ? $frais->avances / $frais->duree : $frais->avances);
                                    $pdf->Text(80, 168, iconv('utf-8','cp1252', number_format(( $impayeavance ), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 170, 100, 170);
                                    $pdf->SetFillColor(0, 0, 0);
                                }


    
                                // Informations sur les passives
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 70, iconv('utf-8','cp1252', "Libellé"));
                                $pdf->Text(178, 70, iconv('utf-8','cp1252', "Retenues"));
                                $pdf->Line(110, 71, 197, 71);
                                $pdf->SetTextColor(0,0,0);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Text(110, 78, iconv('utf-8','cp1252', "Impôt sur le gains : "));
                                $pdf->Text(180, 78, iconv('utf-8','cp1252', number_format(($frais->aibMoisCalculer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 80, 197, 80);
                                $pdf->Text(110, 84, iconv('utf-8','cp1252', "Défalcation : "));
                                $pdf->Text(180, 84, iconv('utf-8','cp1252', number_format(($frais->traceretenue), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 86, 197, 86);
                                $pdf->Text(110, 90, iconv('utf-8','cp1252', "Nature de la défalcation : "));
								$pdf->AdjustText(140, 90, $frais->libretenue, 57);
                                //$pdf->Text(180, 90, iconv('utf-8','cp1252', $frais->libretenue));
                                $pdf->Line(110, 92, 197, 92);
                                $pdf->Text(110, 96, iconv('utf-8','cp1252', "Naf : "));
                                $pdf->Text(180, 96, iconv('utf-8','cp1252', number_format(($frais->tracenaf), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 98, 197, 98);
                                $pdf->Text(110, 102, iconv('utf-8','cp1252', "Avance payer ce mois : "));
                                $pdf->Text(180, 102, iconv('utf-8','cp1252', number_format($frais->recentrembourcer, 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 104, 197, 104);
                                $pdf->Text(110, 108, iconv('utf-8','cp1252', "Carec : "));
                                $pdf->Text(180, 108, iconv('utf-8','cp1252', number_format(($frais->traceCarec), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 110, 197, 110);
                                $pdf->Text(110, 114, iconv('utf-8','cp1252', "Amical : "));
                                $pdf->Text(180, 114, iconv('utf-8','cp1252', number_format(($frais->tracesAmical), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 116, 197, 116);
                                $pdf->Line(110, 122, 197, 122);
                                $pdf->Line(110, 128, 197, 128);
                                $pdf->Line(110, 134, 197, 134);
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 138, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $retenues = $frais->traceretenue + $frais->tracesAmical + $frais->traceCarec + $frais->tracenaf + $frais->aibMoisCalculer + $frais->recentrembourcer;
                                $pdf->Text(180, 138, iconv('utf-8','cp1252', number_format(( $retenues ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 140, 197, 140);
    
                                $pdf->SetFont("Arial", "B", 8);
    
                                // Total sur les actives
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 150, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 150, iconv('utf-8','cp1252', number_format(($brute), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 152, 197, 152);
                                
    
                                // Total sur les passives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 156, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 156, iconv('utf-8','cp1252', number_format(( $retenues ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 158, 197, 158);
    
                                // Retention 
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 162, iconv('utf-8','cp1252', "Retention pour engagement non honoré : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 162, iconv('utf-8','cp1252', number_format(($frais->compteBloquer), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 164, 197, 164);

                                // Total
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 168, iconv('utf-8','cp1252', "Total net  : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 168, iconv('utf-8','cp1252', number_format(($frais->compte), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 170, 197, 170);
    
    
                                // Signature Commercial
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(20, 200, iconv('utf-8','cp1252', "Signature du Conseiller Commercial : "));
                                $pdf->SetTextColor(0,0,0);
    
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(35, 220, iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
    
                                // Signature Société
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(150, 200, iconv('utf-8','cp1252', "Signature de la Société : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Image($soc->signature, 150, 202, -150);
                                $pdf->SetFont("Arial", "", 6);
                                $pdf->Text(160, 220, iconv('utf-8','cp1252', $soc->libelleSociete));
    
                                $pdf->Output('F', $pathd);
                                $fiche_D = $pathd;
                                $lib_fiche_D = $lib;
    
                    //dd("Bon 1");
                    //////////////////////FIN fiche de paie (DUPLICATA) ////////////////////////////////
    
    
                    //////////////////////Catégorie CONS////////////////////////////////
    
                    if ($infoapporteur->Niveau != "INS" && $infoapporteur->Niveau != "CEQP" && $infoapporteur->Niveau != "RG" && $infoapporteur->Niveau != "CD") {
                        $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
                            // Ce que lui même à reçu sur ses affaires
                            $detail = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                // // MODE TEST
                                
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_".$apporteur->Commercial."_".$datelettrefile;
                                $path = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->signature, 10, 10, -150);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                //////////////////////////////////////////////////////// Les données
                                foreach ($detail as $value){ 
                                    $sombase += $value->base;
                                    $somcom += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1); 
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $infoapporteur->Niveau, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom - ($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                ///////////////////////////////////////////////////////////
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller Commercial", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                // Sauvegarde du pdf
                                $pdf->Output('F', $path);
    
                                // Envoyer par mail le fichier
                                $detail_O = $path;
                                $lib_detail_O = $lib;
                                
                                //////////////////// (DUPLICATA) //////////////////////
    
                                // Ce que lui même à reçu sur ses affaires
                            $detail = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
                                 setlocale(LC_TIME,  'french');
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_DUPLICATA_".$apporteur->Commercial."_".$datelettrefile;
                                $pathd = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->signature, 10, 10, -150);
                                $pdf->Image($soc->duplicata, 130, 5, -250);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                //////////////////////////////////////////////////////// Les données
                                foreach ($detail as $value){ 
                                    $sombase += $value->base;
                                    $somcom += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $infoapporteur->Niveau, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom - ($somcom * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                ///////////////////////////////////////////////////////////
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
    
    
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                // Sauvegarde du pdf
                                $pdf->Output('F', $pathd);
                                $detail_D = $pathd;
                                $lib_detail_D = $lib;
    
    
                                ////////////////////FIN (DUPLICATA) ///////////////////
    
                    }
    
                    //////////////////////FIN Catégorie CONS////////////////////////////////
    
                    //////////////////////Catégorie CEQP ou INS ////////////////////////////////
    
                        if ($infoapporteur->Niveau == "CEQP" || $infoapporteur->Niveau == "INS" || $infoapporteur->Niveau == "RG" || $infoapporteur->Niveau == "CD") {
                            $niv = "";
                            $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
                            // Ce que lui même à reçu sur  ses affaires
                            $detaillui = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            if ($infoapporteur->Niveau == "CEQP") {
                                $niv = "CEQP";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeEquipe = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeEquipe;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police','contrats.Agent as apport', 'contrats.Produit as produit',
                                'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.Toc', $apporteur->Commercial)->get(); 
                            }
                            if ($infoapporteur->Niveau == "INS") {
                                $niv = "INS";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeIns = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police','contrats.Agent as apport', 'contrats.Produit as produit', 
                                'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where("commissions.premiervalidation", $apporteur->Commercial)->get();
                            }
                            if ($infoapporteur->Niveau == "RG") {
                                $niv = "RG";
                                // Ce qu'il a gagner en tant chef region
                                $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeRegion;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantRG as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.MontantRG', '!=', 0)
                                ->where('commerciauxes.codeRegion', $codeRG)->get();
                            }
                            
                            if ($infoapporteur->Niveau == "CD") {
                                $niv = "CD";
                                $codeCD = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeCD;
                                $detail = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCD as comm' )
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commissions.MontantCD', '!=', 0)
                                    ->where('commerciauxes.codeCD', $codeCD)->get();
                            }
                            $detailComA = array(); $niva = "";
                            if ($infoapporteur->Niveau == "CD" || $infoapporteur->Niveau == "RG") {
                                $niva = "ANCIEN INS";
                                
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                    
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ins', $apporteur->Commercial)
                                    ->get();
                            }
                            if ($infoapporteur->Niveau == "INS") {
                                $niva = "ANCIEN CEQP";
                                
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ceqp', $apporteur->Commercial)
                                    ->get();
                                
                            }
    
                            setlocale(LC_TIME,  'french');
                                //
                                
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_".$apporteur->Commercial."_".$datelettrefile;
                                $path = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 10, -150);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
                                $sombase2 = 0;
                                $somcom2 = 0;
                                $sombase1 = 0;
                                $somcom1 = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                /////////////////////////////////////////////////////////////////////////////////////
    
                                foreach ($detaillui as $value){ 
                                    $sombase1 += $value->base;
                                    $somcom1 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, "CONS", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase1, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom1, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 - ($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(20);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(15,6,'Apporteur', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                foreach ($detail as $value){ 
                                    $sombase2 += $value->base;
                                    $somcom2 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $niv, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                foreach ($detailComA as $value){
                                    if( strtotime($value->DateDebutEffet) < strtotime("01-01-2023")  )
                                    {
                                        $sombase2 += $value->base;
                                        $somcom2 += $value->comm;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $niva, 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
                                        
                                }
    
                                // Sous catégorie 2
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase2, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom2, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 - ($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                ////////////////////////////////////////////////////////////////////////////////////
    
                                $somcom = $somcom1 + $somcom2;
                                $sombase = $sombase1 + $sombase2;
    
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
    
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
    
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
    
                                // Sauvegarde du pdf
                                $pdf->Output('F', $path);
    
                                $detail_O = $path;
                                $lib_detail_O = $lib;
    
                                // ////////////////////// (DUPLICATA) //////////////////////////////////////////////////////////////////////////////////  
                                $niv = "";
                            // Ce que lui même à reçu sur  ses affaires
                            $detaillui = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            if ($infoapporteur->Niveau == "CEQP") {
                                $niv = "CEQP";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeEquipe = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeEquipe;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.Toc', $apporteur->Commercial)->get(); 
                            }
                            if ($infoapporteur->Niveau == "INS") {
                                $niv = "INS";
                                // Ce qu'il a gagner en tant chef d'équipe
                                $codeIns = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where("commissions.premiervalidation", $apporteur->Commercial)->get();
                            }
                            if ($infoapporteur->Niveau == "RG") {
                                $niv = "RG";
                                // Ce qu'il a gagner en tant chef region
                                $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeRegion;
                                $detail = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantRG as comm' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commissions.MontantRG', '!=', 0)
                                ->where('commerciauxes.codeRegion', $codeRG)->get();
                            }
                            
                            if ($infoapporteur->Niveau == "CD") {
                                $niv = "CD";
                                $codeCD = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeCD;
                                $detail = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    ->select('contrats.police as police', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCD as comm' )
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commissions.MontantCD', '!=', 0)
                                    ->where('commerciauxes.codeCD', $codeCD)->get();
                            }
                            $detailComA = array();$niva = "";
                            if ($infoapporteur->Niveau == "CD" || $infoapporteur->Niveau == "RG") {
                                $niva = "ANCIEN INS";
                                
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantInspecteur as comm' )
                                    
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ins', $apporteur->Commercial)
                                    ->get();
                                
                                
                            }
                            
                            if ($infoapporteur->Niveau == "INS") {
                                $niva = "ANCIEN CEQP";
                                $detailComA = DB::table('commissions')
                                    ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                    ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                                    
                                    ->select('contrats.police as police', 'contrats.DateDebutEffet as DateDebutEffet', 'contrats.Agent as apport', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantCEQ as comm' )
                                    
                                    ->where("statutcalculer", "oui")
                                    ->where('confirmercalcule', "oui")
                                    ->where('confirmercsp', "oui")
                                    ->where('confirmerdt', "oui")
                                    ->where('confirmerdg', "oui")
                                    ->where('confirmercdaf', "oui")
                                    ->where('confirmertresorerie', "oui")
                                    ->where("ctrl", 1)
                                    
                                    ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                    ->where('commerciauxes.Niveau', 'CONS')
                                    ->where('commerciauxes.codeEquipe', '!=', 0)
                                    ->where('contrats.ceqp', $apporteur->Commercial)
                                    ->get();
                            }
    
                            setlocale(LC_TIME,  'french');
                                //
                                
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "DETAIL_FICHE_PAIE_DUPLICATA_".$apporteur->Commercial."_".$datelettrefile;
                                $pathd = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFF();
                                $pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
                                //$pdf->AddPage('L');
                                $pdf->SetTitle($titre , true);
                                $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
                                $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
                                $pdf->SetSubject($titre, true);
                                $pdf->Image($soc->logo, 10, 10, -150);
                                $pdf->Image($soc->duplicata, 130, 5, -250);
                                $pdf->SetFont("Arial", "B", 12);
                                $pdf->Text(60, 17, $titre);
                                $pdf->Ln(25);
    
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Text(10, 32, "INFORMATIONS SUR LE CONSEILLER");
                                $pdf->Line(10, 33, 180, 33);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Text(10, 40,iconv('utf-8','cp1252','Code Apporteur'));
                                $pdf->Text(50, 40,iconv('utf-8','cp1252', $infoapporteur->codeCom));
                                $pdf->Text(120, 40,iconv('utf-8','cp1252','Période'));
                                $pdf->Text(160, 40,iconv('utf-8','cp1252', $datelettre));
    
                                $pdf->Text(10, 48,iconv('utf-8','cp1252', "Nom de l'Apporteur"));
                                $pdf->Text(50, 48,iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
                                $pdf->Text(120, 48,iconv('utf-8','cp1252','Statut'));
                                $pdf->Text(160, 48,iconv('utf-8','cp1252', CommissionController::LibelleNiveau($infoapporteur->Niveau)));
    
                                $pdf->SetFont("Arial", "B", 10);
    
                                $pdf->Text(10, 60, "DETAILS DE LA PRODUCTION ");
                                $pdf->Line(10, 61, 180, 61);
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(30);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                $sombase = 0;
                                $somcom = 0;
                                $sombase2 = 0;
                                $somcom2 = 0;
                                $sombase1 = 0;
                                $somcom1 = 0;
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                /////////////////////////////////////////////////////////////////////////////////////
    
                                foreach ($detaillui as $value){ 
                                    $sombase1 += $value->base;
                                    $somcom1 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, "CONS", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                // Sous catégorie
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(45,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase1, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom1, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom1 - ($somcom1 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                $pdf->SetFont("Arial", "B", 6);
                                $pdf->setFillColor(230,230,230);
                                $pdf->Ln(20);
                                $pdf->Cell(15,6,'Police', 1, 0, 'C', 1);
                                $pdf->Cell(15,6,'Apporteur', 1, 0, 'C', 1);
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                foreach ($detail as $value){ 
                                    $sombase2 += $value->base;
                                    $somcom2 += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                    $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                    $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                    $pdf->Cell(15,6, $niv, 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                }
                                foreach ($detailComA as $value){
                                    if( strtotime($value->DateDebutEffet) < strtotime("01-01-2023")  )
                                    {
                                        $sombase2 += $value->base;
                                        $somcom2 += $value->comm;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $value->apport, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, InterfaceServiceProvider::RecupInfoPayeur($value->police), 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $niva, 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->comm, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($value->comm - ($value->comm * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
                                        
                                }
    
                                // Sous catégorie 2
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase2, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom2, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 - ($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                ////////////////////////////////////////////////////////////////////////////////////
    
                                $somcom = $somcom1 + $somcom2;
                                $sombase = $sombase1 + $sombase2;
    
                                $autrefrais = DB::table('compteagents')->where('Agent', $infoapporteur->codeCom)->first();
                                
                                $nette = $somcom + $autrefrais->AutreCommissionMoisCalculer + $autrefrais->fixe + $autrefrais->bonus + $autrefrais->dotationTelephonie + $autrefrais->dotationCarburant;
    
                                $retenue = ($nette * Fonction::RecupererTaux($infoapporteur->codeCom) / 100) + $autrefrais->recentrembourcer + $autrefrais->retenue ;
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                $pdf->Ln(20);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6,"Signature du Conseiller", 0, 0, 'L');
                                $pdf->Cell(60,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature de la Société "), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C');
    
                                $pdf->Ln(10);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(70,6," ", 0, 0, 'L');
                                $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                $pdf->Ln(10);
    
                                $pdf->SetFont("Arial", "", 8);
                                $pdf->Cell(50,6, $infoapporteur->nomCom.' '.$infoapporteur->prenomCom, 0, 0, 'C');
                                $pdf->Cell(90,6,"", 0, 0, 'L');
                                $pdf->Cell(70,6,iconv('utf-8','cp1252', $soc->libelleSociete), 0, 0, 'L');
                                $pdf->Cell(60,6," ", 0, 0, 'C'); 
    
                                // Sauvegarde du pdf
                                $pdf->Output('F', $pathd);
    
                                $detail_D = $pathd;
                                $lib_detail_D = $lib;
                                
                        } 
    
    
                    //////////////////////////////FIN Catégorie CEQP ou INS////////////////////////
    
                    // Sauvegarde 
                    $addDocD = new Document();
                    $addDocD->path = $fiche_O;
                    $addDocD->libelle = $lib_fiche_O;
                    $addDocD->pathFD = $fiche_D;
                    $addDocD->libelleFD = $lib_fiche_D;
                    $addDocD->pathD = $detail_O;
                    $addDocD->libelleD = $lib_detail_O;
                    $addDocD->pathDD = $detail_D;
                    $addDocD->libelleDD = $lib_detail_D;
                    $addDocD->periode = $moisCom; //$datelettrefile;
                    $addDocD->Agent = $apporteur->Commercial;
                    $addDocD->save();
                    
                }
            } 

           
            //  Vidée tous les comptes apporteurs du mois/
            
            foreach ($list as $apporteur) {
                if(!isset(DB::table('tracecomptes')->where('Commercial', $apporteur->Commercial)->where('moiscalculer', $moisCom)->first()->id)){
                    // mettre à jour la table compte
                    $occurence = json_encode(DB::table('compteagents')->where('Agent', $apporteur->Commercial)->first());
                    $addt = new Tracecompte();
                    $addt->contenu = "Compte réinitialisé : ".$occurence;
                    $addt->moiscalculer = $moisCom;
                    $addt->Commercial = $apporteur->Commercial;
                    $addt->save();
                    DB::table('compteagents')->where('Agent', $apporteur->Commercial)->update([
                        'compteMoisCalculer' => 0,
                        'aibMoisCalculer' => 0,
                        'compteNetapayerMoisCalculer' => 0,
                        'compteEncadrementMoisCalculer' => 0,
                        'AutreCommissionMoisCalculer' => 0,
                        'MoisCalculer' => "",
                        'compte' => 0,
                        'avancesancien' => 0,
                        'bonus' => 0,
                        'traceretenue' => 0,
                        'libretenue' => "",
                        'periodicite' => "",
                        'statutEng' => 0,
                        'recentrembourcer' => 0,
                        'dotationCarburant' => 0,
                        'dotationTelephonie' => 0,
                        'traceCarec' => 0,
                        'tracesAmical' => 0,
                        'tracenaf' => 0,
                        'anticiper' => 0,
                        'statueValide' => 0
                    ]);
                }
            }
            
            
            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = $moisCom;
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
                
            // Validé tous les commissions de ce mois
                DB::table('commissions')->where("ctrl", 1)
                ->where('TypeCommission', 'i')->update([
                    "ctrl" => 2
                ]);
            
            $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mail);
            }
            $allSup = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "cfvi")->get();
            foreach ($allSup as $value) {
                array_push($mails, $value->mail);
            }
            array_push($mails);
            
            SendMail::sendnotificationfin("rogerkpovi@gmail.com", $mails, "Commissions. NSIA VIE ASSURANCES", []);
            
            return json_encode(['response' =>  1, 'message' => "Toutes les fiches de paie sont générées."]); 
    }
    
    public function setrejettresorerie(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where('TypeCommission', 'g')
            ->where('confirmercdaf', "oui")
            ->where("ctrl", 1)
            ->where("commissions.Statut", view()->shared('periode'))
            ->where('confirmertresorerie', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            foreach ($commission as $comm) {
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmercdaf" => null
                ]);
            }

            // Envoi de mail
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "cdaf")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejett");
            //}
        
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', view()->shared('periode'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', view()->shared('periode'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "all";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions de ce mois.");
            TraceController::setTrace("Vous avez rejeté les commissions de ce mois.", session("utilisateur")->idUser);
            
            return Back();
            //return redirect()->route('GCCDAF');

        }else{
            flash(" Pas de commission grouple disponible!!! ");
            return Back();
        }
    }

    public function ExporterEnExcel($reglement)
    {
        if ($reglement == "all") {

            $list = DB::table('compteagents')->select('Agent as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 
            'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  
            'compteagents.avances as navance')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101])->orderBy("compteagents.libCompte", "desc")->get();
            
            $apayer = 0;
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["agent"] = "";
            $tabl[$i]["reglement"] = "";
            $tabl[$i]["num"] = " ";
            $tabl[$i]["com"] = " ";
            $i++;
            $a = 0;
            $libreglementencours = "";
            $comptelibencours = 0;
            foreach ($list as $item){
                $tabl[$i]["code"] = $item->Commercial;
                $tabl[$i]["agent"] = InterfaceServiceProvider::infomanageur($item->Commercial);
                $tabl[$i]["reglement"] = $item->libelleReglement;
                $tabl[$i]["num"] = $item->NumReglement;
                $tabl[$i]["com"] = $item->compte;
                $apayer += $item->compte;
                $comptelibencours += $item->compte;
                $libreglementencours = $item->libelleReglement;
                $i++;
                $p = $a + 1;
                if(isset($list[$p]->libelleReglement)){
                    
                    if($list[$a]->libelleReglement != $list[$p]->libelleReglement){
                        $tabl[$i]["code"] = "";
                        $tabl[$i]["agent"] = "";
                        $tabl[$i]["reglement"] = "Sous Total ";
                        $tabl[$i]["num"] = $libreglementencours;
                        $tabl[$i]["com"] = $comptelibencours;
                        $i++;
                        $libreglementencours = "";
                        $comptelibencours = 0;
                    }
                }else{
                    $tabl[$i]["code"] = "";
                        $tabl[$i]["agent"] = "";
                        $tabl[$i]["reglement"] = "Sous Total ";
                        $tabl[$i]["num"] = $libreglementencours;
                        $tabl[$i]["com"] = $comptelibencours;
                        $i++;
                        $libreglementencours = "";
                        $comptelibencours = 0;
                }
                $a++;
            }

            $tabl[$i]["code"] = "";
            $tabl[$i]["agent"] = "";
            $tabl[$i]["reglement"] = "";
            $tabl[$i]["num"] = "Total ";
            $tabl[$i]["com"] = $apayer;

            return $tabl;
            
        }else{
            // Exporter un règlement spécifique
           
            
            $list = DB::table('compteagents')->select('Agent as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 
            'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  
            'compteagents.avances as navance')->where("compteagents.libCompte", $reglement)->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101])->get();

            $apayer = 0;
            $i = 1;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["agent"] = "";
            $tabl[$i]["reglement"] = "";
            $tabl[$i]["num"] = " ";
            $tabl[$i]["com"] = " ";
            $i++;
            foreach ($list as $item){
                $tabl[$i]["code"] = $item->Commercial;
                $tabl[$i]["agent"] = InterfaceServiceProvider::infomanageur($item->Commercial);
                $tabl[$i]["reglement"] = $item->libelleReglement;
                $tabl[$i]["num"] = $item->NumReglement;
                $tabl[$i]["com"] = $item->compte;
                $apayer += $item->compte;
                $i++;
            }
            $i++;
            $tabl[$i]["code"] = "";
            $tabl[$i]["agent"] = "";
            $tabl[$i]["reglement"] = "";
            $tabl[$i]["num"] = " ";
            $tabl[$i]["com"] = " ";
            $i++;
            $tabl[$i]["code"] = "";
            $tabl[$i]["agent"] = "";
            $tabl[$i]["reglement"] = "";
            $tabl[$i]["num"] = "Total ";
            $tabl[$i]["com"] = $apayer;

            return $tabl;
        }
    }

    public function ExporterEnPDF($reglement){
        
            $list = DB::table('compteagents')->select('Agent as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 
            'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  
            'compteagents.avances as navance')->where("statueValide", 1)->whereNotIn('Agent', [1,2,3, 101]);
            if ($reglement != "all") {
                $list = $list->where("compteagents.libCompte", $reglement);
            }
            $list = $list//->where('commissions.moiscalculer', view()->shared('periode')) // Mode TEST
            ////->where('commissions.moiscalculer', view()->shared('periode'))
            //->whereIn('commissions.Apporteur', [6013, 5638, 1319, 518, 517, 5460, 5509, 1318, 5259, 211, 5647, 151, 522, 5649, 401, 512, 5212])
            //->distinct('commissions.Apporteur')
            ->get();
            
            
            
            ////////////
            /*
            $listComplement = DB::table('commissions')
                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                ->where("contrats.Produit", 6120)
                ->where("statutcalculer", "oui")
                //->where("commissions.Apporteur", 6013)
                ->where("confirmercalcule", "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', "oui")
                ->where('confirmertresorerie', null)->get();
                
            
            $tabCompteApp = array(); $tabApp = array();
                
            foreach ($listComplement as $valuec) {
                $taux = 0.25;
                
                $baseCom = $valuec->BaseCommission;
                
                $montantCons = $baseCom * $taux / 100;
                
                $recupcompte = DB::table('compteagents')->where('Agent', $valuec->Apporteur)->first();
        		$newcompte = $recupcompte->compteMoisCalculer + $montantCons;
        		
        		//Compteagent::where('Agent', $valuec->Apporteur)->update([
        		//	'compteMoisCalculer' => $newcompte
        		//]);
        		
        		// Mise à  jour de la table Commission
        		$mont = $valuec->MontantConseiller + $montantCons;
                //Commission::where('NumCommission', $valuec->NumCommission)->update([
                   'MontantConseiller' => round($mont),
                //]); 
                //dd($montantCons);
                $tauxaibcons = Fonction::RecupererTaux($valuec->Apporteur);
                
                // Calcul du montant aib
                        $mont_aib_cons = $montantCons * $tauxaibcons / 100;

                        // Calcul du montant net à payer
                        $mont_cons = $montantCons - $mont_aib_cons;
                        
                        $anet = $recupcompte->compteNetapayerMoisCalculer + round($mont_cons);
                        
                        $baib = $recupcompte->aibMoisCalculer + $mont_aib_cons;
                        
                        // Set montant net payer
                    //DB::table('compteagents')->where('Agent', $valuec->Apporteur)->update([
                      //      'compteNetapayerMoisCalculer' => $anet,
                        //    'aibMoisCalculer' => $baib
                        //]); 
                        
                    $ccompte = $recupcompte->compte + round($mont_cons);
            
                //Compteagent::where('Agent', $valuec->Apporteur)->update([
        		//	'compte' => $ccompte
        		//]); 
        		
        		if (!in_array($valuec->Apporteur, $tabApp)){ // $tabApp $tabCompteApp
        		    array_push($tabApp, $valuec->Apporteur);
        		    array_push($tabCompteApp, round($mont_cons));
        		    //echo $mont_cons.'<br>';
        		}else{
        		    $a = 0;
                    foreach ($tabApp as $appt) {
                        if ($appt == $valuec->Apporteur) {
                            $montactuel = $tabCompteApp[$a];
                            
                            $somactuel = $montactuel + round($mont_cons);
                            
                            $tabCompteApp[$a] = $somactuel;
                        }
                        $a++;
                    }
        		}
            }
            
            $listCompl = DB::table('commissions')
            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->join('compteagents', 'compteagents.Agent', '=', 'commissions.Apporteur')
            ->select('commissions.Apporteur as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerMoisCalculer as nette', 
            'compteagents.avancesancien as avance', 'compteagents.recentrembourcer as rembourser', 'compteagents.compte as compte',  
            'compteagents.avances as navance')
            ->where("statutcalculer", "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where('confirmercdaf', "oui")
            ->where('confirmertresorerie', null)
            ->distinct('commissions.Apporteur')
            ->where("contrats.Produit", 6120)
            ->get();
            
            */
            ///////////

            // Générer code série QR
            $serieQR = CommissionController::genererserie();

            // Créer l'image QRCODE montant, periode, nombre apporteur

            // Recherche des données de l'image
            $i = 0; $mont = 0;
            foreach ($list as $value) {
                $mont += $value->compte;
                $i++;
            }
            //dd($mont);
            /////////////////////// $tabCompteApp = array(); $tabApp = array();
            /*
            foreach ($listCompl as $valuC) {
                $a = 0;
                foreach ($tabApp as $appt) {
                        if ($appt == $valuC->Commercial) {
                            $mont += $tabCompteApp[$a];
                        }
                        $a++;
                    }
                $i++;
                
            }  */
            //dd($mont);
            
            ////////////////////////
            $nombreApporteur = $i;

            $texte = "Montant de l'Etat : ".$mont." CFA. Période : ".view()->shared('periode').". Nombre Apporteur : ".$nombreApporteur.".";

            $pathQr = CommissionController::scanqrcode($texte, $serieQR);

            // Enregistrer la trace dans la table récap_commission

            // Controller si série existe déjà ou pas

            //if (isset(DB::table('recapcommissions')->where('periode', view()->shared('periode'))->first()->code)){
            if (!isset(DB::table('recapcommissions')->where('periode', view()->shared('periode'))->first()->serie)){ // Mode TEST
                $add = new  Recapcommission();
                $add->serie = $serieQR;
                $add->codeQR = $pathQr;
                $add->periode = view()->shared('periode'); // Mode TEST
                //$add->periode = view()->shared('periode');
                $add->montantEtat = $mont;
                $add->nombreAgent = $nombreApporteur;
                if ($reglement == "all") {
                   $add->all = 1;
                }
                $add->save();
            } 

            setlocale(LC_TIME,  'french');

            
            //

            $signature = DB::table("signataires")->where('DateCalculer', view()->shared('periode'))->get(); // TEST
            $signaturecdaf = $signature[4]->pathSignataire;
            $signaturedg = $signature[3]->pathSignataire;
            $signaturedt = $signature[2]->pathSignataire;
            $signaturecsp = $signature[1]->pathSignataire;
            $signaturesp = $signature[0]->pathSignataire;

            // Création du PDF

            //$titre = "REPARTITION DES COMMISSIONS COMPLEMENT DU MOIS DE ".$datelettre;
            $titre = "REPARTITION DES COMMISSIONS DU MOIS DE ".view()->shared('periode');
            $path = "document/commission/".$serieQR.".pdf";

            //create pdf document
            $pdf = new PDFF();
            //$pdf->AddPage('', 0, '', 15, 15, 16, 16, 9, 9, 'L');
            $pdf->AddPage('L');
            $pdf->SetTitle($titre , true);
            $pdf->SetAuthor("NSIA VIE ASSURANCES", true);
            $pdf->SetCreator("Crée par NSIA VIE ASSURANCES" , true);
            $pdf->SetSubject($titre, true);
            $pdf->Image('assets/images/logo.png', 10, 10, -150);
            $pdf->SetFont("Arial", "B", 12);
            $pdf->Text(90, 20, $titre);
            $pdf->Image($pathQr, 270, 10, -300);
            //$pdf->Image("document/commission/nn.png", 270, 10, -300);
            $pdf->SetFont("Arial", "", 6);
            $pdf->Text(269, 30, $serieQR);
            $pdf->SetFont("Arial", "", 9);
            if(isset($signaturesp))
                $pdf->Image($signaturesp, 27, 22, -1000); // SP
            if(isset($signaturecsp))
                $pdf->Image($signaturecsp, 36, 22, -1000); // CSP
            if(isset($signaturedt))
                $pdf->Image($signaturedt, 44, 22, -1000); // DT
            if(isset($signaturedg))
                $pdf->Image($signaturedg, 51, 22, -1000); // DG
            if(isset($signaturecdaf))
                $pdf->Image($signaturecdaf, 60, 22, -1000); // CDAF
            $pdf->Text(20, 30, "Ref SP / CSP / DT / DG / CDAF ");
            $pdf->Ln(25);
            $pdf->SetFont("Arial", "B", 8);
            $pdf->setFillColor(230,230,230);
            $pdf->Cell(25,6,'CODE', 1, 0, 'C', 1);
            $pdf->Cell(80,6,'AGENT', 1, 0, 'C', 1);
            $pdf->Cell(60,6,'COMPTE', 1, 0, 'C', 1);
            $pdf->Cell(40,6,iconv('utf-8','cp1252','N° DE COMPTE'), 1, 0, 'C', 1);
            $pdf->Cell(60,6,' COMMISSION A REVERSER', 1, 0, 'C', 1);

            $pdf->SetFont("Arial", "", 7);
            foreach ($list as $value){ 
            $pdf->Ln(6);
            $pdf->Cell(25,6,iconv('utf-8','cp1252', $value->Commercial ), 1, 0, 'C');
            $pdf->Cell(80,6, iconv('utf-8','cp1252', InterfaceServiceProvider::infomanageur($value->Commercial)), 1, 0, 'L');
            $pdf->Cell(60,6,iconv('utf-8','cp1252', $value->libelleReglement), 1, 0, 'C');
            $pdf->Cell(40,6,iconv('utf-8','cp1252', $value->NumReglement), 1, 0, 'C');
            $pdf->Cell(60,6,iconv('utf-8','cp1252', $value->compte." CFA"), 1, 0, 'C');

            }
            /////////////////////////////////////////
             /*foreach ($listCompl as $valueCV){ 
                
            $pdf->Ln(6);
            $pdf->Cell(25,6,iconv('utf-8','cp1252', $valueCV->Commercial ), 1, 0, 'C');
            $pdf->Cell(80,6, iconv('utf-8','cp1252', InterfaceServiceProvider::infomanageur($valueCV->Commercial)), 1, 0, 'L');
            $pdf->Cell(60,6,iconv('utf-8','cp1252', $valueCV->libelleReglement), 1, 0, 'C');
            $pdf->Cell(40,6,iconv('utf-8','cp1252', $valueCV->NumReglement), 1, 0, 'C');
                $a = 0;
                foreach ($tabApp as $appt) {
                        if ($appt == $valueCV->Commercial) {
                            $pdf->Cell(60,6,iconv('utf-8','cp1252', $tabCompteApp[$a]." CFA"), 1, 0, 'C');
                            
                        }
                        $a++;
                    }
            } */ 
            /////////////////////////////////////////////////////
            $pdf->Ln(6);
            $pdf->Cell(25,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
            $pdf->Cell(80,6, iconv('utf-8','cp1252', ""), 1, 0, 'L');
            $pdf->Cell(60,6,iconv('utf-8','cp1252', " "), 1, 0, 'C');
            $pdf->Cell(40,6,iconv('utf-8','cp1252', "Total général : "), 1, 0, 'C');
            $pdf->Cell(60,6,iconv('utf-8','cp1252', $mont." CFA"), 1, 0, 'C');

            // Sauvegarde du pdf
            $pdf->Output('F', $path);
            Storage::put($path, $pdf->output());
            return $path;
    }

    public function getreglement(){
        $montall = "";
        $qr = "";
        $allreglement = "";

        $temp = Session::get('montall');
        //dd(Session::get('allreglement'));
        if (isset($temp) && $temp == 0) {
            Session::put('montall', 0);
            Session::put('qr', "" );
            Session::put('allreglement', "" );
        }

        if (Session::get('montall') != "") {
            $montall = Session::get('montall');
            $qr = Session::get('qr');
            $allreglement = Session::get('allreglement');
        }
       
        if (request("vue") == "vue" || request('recherchevue') == 1) {
            $qr = trim(htmlspecialchars(request("qr")));
            if(isset(DB::table('recapcommissions')->where('all', 1)->where('serie', trim(htmlspecialchars(request("qr"))))->first()->montantEtat))
            {
                $montall = DB::table('recapcommissions')->where('all', 1)->where('serie', trim(htmlspecialchars(request("qr"))))->first()->montantEtat;
                $allreglement = DB::table('reglements')->where('RecapCommission', trim(htmlspecialchars(request("qr"))))->get();
                Session::put('montall', $montall );
                Session::put('qr', $qr );
                Session::put('allreglement', $allreglement );
                flash("La somme du code QR est affiché.");
            }else{
                flash("Le code QR n'existe pas")->error();
                return Back();
            }
            
        }
        
        $listPayement = DB::table('structures')->get();

        return view("commission.reglement", compact("listPayement", "montall", "qr", "allreglement"));
    }

    public function setreglementEtape(Request $request){
        if (request("qr") == "") {
            flash('Veuillez renseigné la référence du QrCode.')->error();
            return Back();
        }
        
        $addR = new Reglement();
        $addR->ModePaiement = trim(htmlspecialchars(request('modepaiement')));
        $addR->Montant = request('montreglement');
        $addR->Date = trim(htmlspecialchars(request('datpaiement')));
        $addR->RefPaiement = trim(htmlspecialchars(request('refpaiement')));
        $addR->RecapCommission = trim(htmlspecialchars(request('qr')));
        $addR->user_action = session('utilisateur')->idUser;
        $addR->save();


        flash('Règlement par '.trim(htmlspecialchars(request('modepaiement'))).' enregistré avec succès.')->success();
        
        $montall = "";
        $qr = "";
        $allreglement = "";
        $qr = trim(htmlspecialchars(request("qr")));
        $montall = DB::table('recapcommissions')->where('all', 1)->where('serie', trim(htmlspecialchars(request("qr"))))->first()->montantEtat;
        $allreglement = DB::table('reglements')->where('RecapCommission', trim(htmlspecialchars(request("qr"))))->get();
        Session::put('montall', $montall );
        Session::put('qr', $qr );
        Session::put('allreglement', $allreglement );
        TraceController::setTrace(
                "Vous avez enregistré un règlement dont le montant est ".request('montreglement')." par ".trim(htmlspecialchars(request('modepaiement'))),
                session("utilisateur")->idUser);

        return Back();
    }

    public function deletereglement()
    {
        $occurence = json_encode(Reglement::where('idReglement', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Règlement supprimé : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        Reglement::where('idReglement', request("id"))->delete();
        flash("Règlement supprimé avec succès."); return Back();
    }

    public function scanqrcode($chaine, $value)
    {
        $val = 'document/commission/'.$value.'.png';
        QrCode::size(200)->backgroundColor(255,255,255)->format('png')->generate($chaine, $val);
        //$val = 'codeimage/'.$value.'.png';
        //QRcode::png($chaine, $val);
        return $val;
    }

    public static function LibelleNiveau($code){
        return DB::table('niveaux')->where('codeNiveau', $code)->first()->libelleNiveau;;
    }

    public function LibelleProduit($code)
    {
        return DB::table('produits')->where('idProduit', $code)->first()->libelle;

     }
    public function genererserie()
    {
        
        // Générer série
            $string = "";
            $universal_key = 6;

            $user_ramdom_key = "aLABbC0cEd1eDf2FghR3ij4kYXQl5UmOPn6pVq7rJs8tuW9IvGwxHTyKZMS";
            srand((double)microtime()*time());
            for($i=0; $i<$universal_key; $i++) {
            $string .= $user_ramdom_key[rand()%strlen($user_ramdom_key)];
            }

            $code = "NVA".$string.date("mY"); 
            //$code = "NVA".$string."072021";// MODE TEST

            // vérification si le pdf à été déjà généré pour la période
            //if (!isset(DB::table('recapcommissions')->where('periode', view()->shared('periode'))->first()->code)){
            if (!isset(DB::table('recapcommissions')->where('periode', view()->shared('periode'))->first()->serie)){
                if (!isset(DB::table('recapcommissions')->where('serie', $code)->first()->serie)){
                    return $code;
                }
                else
                    CommissionController::genererserie();
            }else

            return DB::table('recapcommissions')->where('periode', view()->shared('periode'))->first()->serie;
                //return DB::table('recapcommissions')->where('periode', view()->shared('periode'))->first()->code;
    }
	
	public static function setcroncommission()
    {
        //Récupérer les derniers état de commissions non calculé
        $comm = Fonction::Commission();
        //dd($comm);
        $tab_agents = array();
        $tab_taux = array();
        $tab_taux_affaireconcerner = array();
        $tab_police = array();
        
        // Listes des chefs d'équipes et inspecteurs qui ont bénéficier de commissions ???

        foreach ($comm as $commission) {
            //Pour chaque commissions, récupérer le numéro de police
            $numPolice = $commission->NumPolice;
            
            $Contrat = Fonction::GetContrat($numPolice);
            
            //dd($Contrat == null);
            if($Contrat == null){
                if (!in_array($numPolice, $tab_police))
                    array_push($tab_police, $numPolice);
                $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                $comupdate->commentaire = $comupdate->commentaire." ; Le contrat n'existe pas.";
                $comupdate->ctrl = 4;
                $comupdate->save();
            }else{
               
                // Omis les contrats résilié
                if($Contrat->statutSunshine == "Actif"){

                    //recherche l'agent concerner  par le calcul de commissionnement dans contrat
                    $Commercial = Fonction::GetCommercial($Contrat->Agent);
        
                    // vérification du code de produit qu'il fait partir bien des produits concerner par le calcul
                    // NSIA Etudes, NSIA Retraite, NSIA Pension
                    $check = Fonction::Verification($Contrat->Produit);
                    
                    ////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    $ts2 = strtotime($commission->DateFinQuittance);
                    $ts1 = strtotime($Contrat->DateDebutEffet);
                        
                        $year1 = date('Y', $ts1);
                        $year2 = date('Y', $ts2);
                        
                        $month1 = date('m', $ts1);
                        $month2 = date('m', $ts2);
                        
                        $ncom = (($year2 - $year1) * 12) + ($month2 - $month1);
                        
                        if($ncom < 0) $ncom = 0;
                    
                    ////////////////////////////////////////////////////////////////////////////////////////////////////
        
                    if ($check && strtotime($Contrat->DateDebutEffet) >= strtotime("01-02-2021")) { 
                        $ncom = $ncom / Fonction::ValeurPeriocite($Contrat->fractionnement);
                        Commission::where('NumCommission', $commission->NumCommission)->update([
                            'ncom' => $ncom
                        ]);
                        // Nouveau Schéma
                        $schema = "NOUVEAU";
        
                        //récuperer baseCommission dans commissions 
                        $baseCommission = $commission->BaseCommission;
        
                        // récupérer le fractionnement dans la table contrat
                        $fractionnement = $Contrat->fractionnement;
        
                        // durée du contrat
                        //$dureecontrat = abs(intval((strtotime($Contrat->DateEcheance) - strtotime($Contrat->DateDebutEffet)) / 60 / 60 / 24 / 30));
                        $date1 = $Contrat->DateEcheance;
                        $date2 = $Contrat->DateDebutEffet;
                        $mois1 = intval(substr($date1, -7, 2));
                        $an1 = intval(substr($date1, -4, 4));
                        $mois2 = intval(substr($date2, -7, 2));
                        $an2 = intval(substr($date2, -4, 4));
        
                        $dureecontrat =  ($an1 - $an2) * 12 + abs($mois1 - $mois2);
        
                        //Vérifier nombre de commission déjà pour le numéro de quittance suivant le fractionnement
                        $checkFrac = Fonction::VerificationNombreQuittanceDejaCalculer($commission->NumQuittance, $Contrat->Produit, $schema, $fractionnement, $dureecontrat);
        
                        if ($checkFrac == -1) {
                            // Enregistrer les taux qui n'est pas paramétré
                            if (!in_array($Contrat->Produit, $tab_taux)){
                                array_push($tab_taux, $Contrat->Produit);
                                array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                            }

                            $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                            $comupdate->commentaire = $comupdate->commentaire." ; Les taux pour calculé cette commission n'existe pas.";
                            $comupdate->ctrl = 4;
                            $comupdate->save();
                        }else{
                            // Si ça dépasse, pas de calcul 
                            if ($checkFrac == 0) {
        
                                // Taux 
                                $taux_niveauCons = "";
                                
                                $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.cons'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                
                                if(isset($Commercial->codeCom)){
                                if($Commercial->Niveau == "COU" || $Commercial->Niveau == "BA"){
                                    $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, ["COU", "BA"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                }
                                }else{
                                         // Enregistrer les agents qui n'existent pas
                                        if (!in_array($Contrat->Agent, $tab_agents))
                                            array_push($tab_agents, $Contrat->Agent);
                                        $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                                        $comupdate->commentaire = $comupdate->commentaire." ; l'Apporteur n'est pas créée";
                                        $comupdate->ctrl = 0;
                                        $comupdate->save();

                                }
                                $taux_niveauCEQP = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.ceqp'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                $taux_niveauINSPECTION = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.inspecteur'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                $taux_niveauRG = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.rgt'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                $taux_niveauCD = Fonction::GetTauxNiveau($fractionnement, $schema, ["CD"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                
                                if(isset($taux_niveauCons->tauxCommissionnement))
                                {
                                    // Calcul des montant des commissions sans aib
                                    $montantCons = (($baseCommission * $taux_niveauCons->tauxCommissionnement / 100) * $taux_niveauCons->pourcentage) / 100;
                                    $montant_CEQP = (($baseCommission * $taux_niveauCEQP->tauxCommissionnement / 100) * $taux_niveauCEQP->pourcentage) / 100;
                                    $montant_INSPECTION = (($baseCommission * $taux_niveauINSPECTION->tauxCommissionnement / 100) * $taux_niveauINSPECTION->pourcentage) / 100;
                                    $montant_RG = 0;
                                    $montantCD = 0;
                                    if($Contrat->Produit == 6120){
                                            $montant_RG = ($baseCommission * 0.25 / 100);
                                            if(isset($taux_niveauCD->tauxCommissionnement))
                                                $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                    }else{
                                        $montant_RG = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                        if(isset($taux_niveauCD->tauxCommissionnement))
                                            $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                        else
                                            $montantCD = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                    }
                                    if(isset($Commercial->codeCom)){
                                        
                                        $montSun = Commission::where('NumCommission', $commission->NumCommission)->first()->MontantSunShine;
                                        
                                        if( (abs($montSun - round($montantCons))) == 1 )
                                            $montantCons = $montSun;
        
                                        // Mise à  jour de la table Commission
                                        Commission::where('NumCommission', $commission->NumCommission)->update([
                                            'MontantConseiller' => round($montantCons),
                                            'MontantCEQ' => round($montant_CEQP),
                                            'MontantInspecteur' => round($montant_INSPECTION),
                                            'MontantRG' => round($montant_RG),
                                            'MontantCD' => round($montantCD),
                                            'statutcalculer' => 'oui',
                                            'bareme' => $schema,
                                            'ctrl' => 1
                                        ]);
                                    }
                                }else{
                                    // Enregistrer les taux qui ne pas paramétré
                                    if (!in_array($commission->NumCommission, $tab_taux_affaireconcerner)){
                                        array_push($tab_taux, $Contrat->Produit);
                                        array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                                    }
                                    $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                                    $comupdate->commentaire = $comupdate->commentaire." ; le taux pour cette commission n'existe pas.";
                                    $comupdate->ctrl = 4;
                                    $comupdate->save();
                                }
        
                            }
                            else
                            {
                                $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                                $comupdate->commentaire = $comupdate->commentaire." ; La limite de calcul dépassé.";
                                $comupdate->ctrl = 4;
                                $comupdate->save();
                            }
                        }
                    }
                    else{
                        
                        Commission::where('NumCommission', $commission->NumCommission)->update([
                            'ncom' => $ncom
                        ]);
                        
                        // Ancien Schéma
                        $schema = "ANCIEN";
        
                        //récuperer baseCommission dans commissions
                        $baseCommission = $commission->BaseCommission;
        
                        // récupérer le fractionnement dans la table contrat
                        $fractionnement = $Contrat->fractionnement;
        
                        // durée du contrat en mois
                        //$dureecontrat = abs(intval((strtotime($Contrat->DateEcheance) - strtotime($Contrat->DateDebutEffet)) / 60 / 60 / 24 / 30));
                        $date1 = $Contrat->DateEcheance;
                        $date2 = $Contrat->DateDebutEffet;
                        $mois1 = intval(substr($date1, -7, 2));
                        $an1 = intval(substr($date1, -4, 4));
                        $mois2 = intval(substr($date2, -7, 2));
                        $an2 = intval(substr($date2, -4, 4));
        
                        $dureecontrat =  ($an1 - $an2) * 12 + abs($mois1 - $mois2);
        
                        //Vérifier nombre de commission déjà pour le numéro de quittance suivant le fractionnement
                        $checkFrac = Fonction::VerificationNombreQuittanceDejaCalculer($commission->NumQuittance, $Contrat->Produit, $schema, $fractionnement, $dureecontrat);
                        //if($Contrat->Produit == 2300)
                            //dd($commission->NumCommission);
                        
                        if ($checkFrac == -1) {
                            // Enregistrer les taux qui ne pas paramétré
                            if (!in_array($Contrat->Produit, $tab_taux)){
                                array_push($tab_taux, $Contrat->Produit);
                                array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                            }
                            $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                            $comupdate->commentaire = $comupdate->commentaire." ; Le taux pour calculé cette commission n'existe pas.";
                            $comupdate->ctrl = 4;
                            $comupdate->save();
                        }else{
                            // Si ça dépasse, pas de calcul
                            
                            if ($checkFrac == 0) {
                                $taux_niveauCons = ""; $taux_niveauCEQP = ""; $taux_niveauINSPECTION = ""; $taux_niveauRG = "";
                                // Taux
                                //if(in_array($Commercial->Niveau, trans('var.ceqp')) || in_array($Commercial->Niveau, trans('var.inspecteur')) || in_array($Commercial->Niveau, trans('var.rgt')))
                                    $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.cons'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                //else
                                  //  $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, [$Commercial->Niveau], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent);
                                if(isset($Commercial->codeCom)){
                                if($Commercial->Niveau == "COU" || $Commercial->Niveau == "BA"){
                                    $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, ["COU", "BA"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                }
                                }else{
                                         // Enregistrer les agents qui n'existent pas
                                        if (!in_array($Contrat->Agent, $tab_agents))
                                            array_push($tab_agents, $Contrat->Agent);
                                     $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                                        $comupdate->commentaire = $comupdate->commentaire." ; l'Apporteur n'est pas créée";
                                        $comupdate->ctrl = 0;
                                        $comupdate->save();
                                }
                                $taux_niveauCEQP = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.ceqp'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                $taux_niveauINSPECTION = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.inspecteur'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                $taux_niveauRG = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.rgt'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                $taux_niveauCD = Fonction::GetTauxNiveau($fractionnement, $schema, ["CD"], $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $commission->NumPolice);
                                
                            
                                if(isset($taux_niveauCons->tauxCommissionnement)){
                                    // Calcul des montant des commissions sans aib
                                    $montantCons = (($baseCommission * $taux_niveauCons->tauxCommissionnement / 100) * $taux_niveauCons->pourcentage) / 100;
                                    $montant_CEQP = (($baseCommission * $taux_niveauCEQP->tauxCommissionnement / 100) * $taux_niveauCEQP->pourcentage) / 100;
                                    $montant_INSPECTION = (($baseCommission * $taux_niveauINSPECTION->tauxCommissionnement / 100) * $taux_niveauINSPECTION->pourcentage) / 100;
                                    $montant_RG = 0;
                                    $montantCD = 0;
                                    if($Contrat->Produit == 6120){
                                            $montant_RG = ($baseCommission * 0.25 / 100);
                                            if(isset($taux_niveauCD->tauxCommissionnement))
                                                $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                        
                                    }else{
                                        $montant_RG = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                        if(isset($taux_niveauCD->tauxCommissionnement))
                                            $montantCD = (($baseCommission * $taux_niveauCD->tauxCommissionnement / 100) * $taux_niveauCD->pourcentage) / 100;
                                        else
                                            $montantCD = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
                                    }
                                    
                                    if(isset($Commercial->codeCom)){
                                        $montSun = Commission::where('NumCommission', $commission->NumCommission)->first()->MontantSunShine;
                                        if( (abs($montSun - round($montantCons))) == 1 )
                                            $montantCons = $montSun;
                                            
                                        Commission::where('NumCommission', $commission->NumCommission)->update([
                                            'MontantConseiller' => round($montantCons),
                                            'MontantCEQ' => round($montant_CEQP),
                                            'MontantInspecteur' => round($montant_INSPECTION),
                                            'MontantRG' => round($montant_RG),
                                            'MontantCD' => round($montantCD),
                                            'statutcalculer' => 'oui',
                                            'bareme' => $schema,
                                            'ctrl' => 1
                                        ]);
                                    }
                                }else{
                                    //dd($Contrat);
                                        // Enregistrer les taux qui ne pas paramétré
                                    if (!in_array($commission->NumCommission, $tab_taux_affaireconcerner)){
                                        array_push($tab_taux, $Contrat->Produit);
                                        array_push($tab_taux_affaireconcerner, $commission->NumCommission);
                                    }
                                    $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                                    $comupdate->commentaire = $comupdate->commentaire." ; le taux pour cette commission n'existe pas.";
                                    $comupdate->ctrl = 4;
                                    $comupdate->save();
                                }
                            }
                            else
                            {
                                $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                                $comupdate->commentaire = $comupdate->commentaire." ; La limite de calcul dépassé.";
                                $comupdate->ctrl = 4;
                                $comupdate->save();
                            }
                        }
                    }
                }else{
                    $comupdate = Commission::where('NumCommission', $commission->NumCommission)->first(); 
                    $comupdate->commentaire = $comupdate->commentaire." ; Le contrat n'est pas actif.";
                    $comupdate->ctrl = 4;
                    $comupdate->save();
                }
            }   
        }
        $message = "";
        return json_encode(['response' =>  1, 'message' => $message, 'succes' => "Commission calculée."]);
        return response()->json(['response' =>  1, 'message' => $message, 'succes' => "Commission calculée."]);
        
    }


}
