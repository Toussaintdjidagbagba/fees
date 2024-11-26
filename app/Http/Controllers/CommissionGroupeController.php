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
use App\Http\Model\Tracecompte;
use Crabbly\Fpdf\Fpdf;

class PDFFF extends Fpdf
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
}

class CommissionGroupeController extends Controller
{
    public function __construct()
    {
        set_time_limit(142000);
        ini_set("memory_limit", "512M");
    }
    
    public function updatecom()
    {
        DB::table('commissions')->update(['TypeCommission' => 'i']);
        dd("Bon");
    }
    
    public function setcommission()
    {
        //Récupérer les derniers état de commissions non calculé
        $comm = Fonction::CommissionGroupe();
        
        $tab_agents = array();
        $tab_taux = array();
        $tab_police = array();
        $tab_com_taux = array();
        
        // Limiter le total

        // Listes des chefs d'équipes et inspecteurs qui ont bénéficier de commissions ???

        foreach ($comm as $commission) {
            //Pour chaque commissions, récupérer le numéro de police
            $numPolice = $commission->NumPolice;
            
            $Contrat = Fonction::GetContrat($numPolice);
            
            if($Contrat == null){
                if (!in_array($numPolice, $tab_police))
                    array_push($tab_police, $numPolice);
                    
            }else{
               
            // Omis les contrats résilié
            if($Contrat->statutSunshine == "Actif"){
                
                //recherche l'agent concerner  par le calcul de commissionnement dans contrat
                $Commercial = Fonction::GetCommercial($Contrat->Agent);
    
                // vérification du code de produit qu'il fait partir bien des produits concerner par le calcul
                // NSIA Etudes, NSIA Retraite, NSIA Pension, NSIA FORTUNE
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
                /*
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
                        if (!in_array($Contrat->Produit, $tab_taux))
                            array_push($tab_taux, $Contrat->Produit);
                        array_push($tab_com_taux, json_encode($commission));
                    }else{
                        // Si ça dépasse, pas de calcul 
                        if ($checkFrac == 0) {
    
                            // Taux 
                            $taux_niveauCons = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.cons'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent);
                            $taux_niveauCEQP = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.ceqp'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent);
                            $taux_niveauINSPECTION = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.inspecteur'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent);
                            $taux_niveauRG = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.rgt'), $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent);
    
                            if(isset($taux_niveauCons->tauxCommissionnement))
                            {
                                // Calcul des montant des commissions sans aib
                                $montantCons = (($baseCommission * $taux_niveauCons->tauxCommissionnement / 100) * $taux_niveauCons->pourcentage) / 100;
                                $montant_CEQP = (($baseCommission * $taux_niveauCEQP->tauxCommissionnement / 100) * $taux_niveauCEQP->pourcentage) / 100;
                                $montant_INSPECTION = (($baseCommission * $taux_niveauINSPECTION->tauxCommissionnement / 100) * $taux_niveauINSPECTION->pourcentage) / 100;
                                $montant_RG = (($baseCommission * $taux_niveauRG->tauxCommissionnement / 100) * $taux_niveauRG->pourcentage) / 100;
    
                                if(isset($Commercial->codeCom)){
                                    // Enregistrement des montants dans leurs compte avoirs respective
                                    Fonction::SetMontantAgentGroupe($montantCons, $Commercial->codeCom);
    
                                    // récupérer ncom du dernier quittance
                                    //$nombre = DB::table("commissions")->where("NumQuittance", $commission->NumQuittance)->orderby("id", "DESC")->first()->ncom;
                                    //$nombre++; 
                                    $montSun = Commission::where('NumCommission', $commission->NumCommission)->first()->MontantSunShine;
                                    
                                    if( (abs($montSun - round($montantCons))) == 1 )
                                        $montantCons = $montSun;
    
                                    // Mise à  jour de la table Commission
                                    Commission::where('NumCommission', $commission->NumCommission)->update([
                                        'MontantConseiller' => round($montantCons),
                                        'MontantCEQ' => round($montant_CEQP),
                                        'MontantInspecteur' => round($montant_INSPECTION),
                                        'MontantRG' => round($montant_RG),
                                        'statutcalculer' => 'oui',
                                        'bareme' => $schema
                                    ]);
                                }else{
                                     // Enregistrer les agents qui n'existent pas
                                    if (!in_array($Contrat->Agent, $tab_agents))
                                        array_push($tab_agents, $Contrat->Agent);
                                }
                            }else{
                                // Enregistrer les taux qui ne pas paramétré
                                if (!in_array($Contrat->Produit, $tab_taux))
                                    array_push($tab_taux, $Contrat->Produit);
                                array_push($tab_com_taux, json_encode($commission));
                            }
    
                        }
                    }
                }
                else{*/
                
                
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
                        
                        // Enregistrer les taux qui n'est pas paramétré
                        
                        if (!in_array($Contrat->Produit, $tab_taux))
                            array_push($tab_taux, $Contrat->Produit);
                        array_push($tab_com_taux, json_encode($commission));
                    }else{
                        // Si ça dépasse, pas de calcul
                        
                        if ($checkFrac == 0) {
                            $taux_niveauCons = ""; $taux_niveauCEQP = ""; $taux_niveauINSPECTION = ""; $taux_niveauRG = "";
                            
                            if($Contrat->conv != null && $Contrat->conv != "") // Traitement des commissions groupes
                                // trans('var.group')
                            {
                                $taux_niveau = Fonction::GetAllTauxGroup($fractionnement, $schema, trans('var.group'), 
                                                        $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $Contrat->Agent, $numPolice, $Contrat->conv, $baseCommission);
                                
                                //if($Contrat->Agent == 350) dd($taux_niveau);
                                // S'il y a des apporteurs qui bénéficie sur le même affaire alors accorder lui ses commissions
                                /* if($Contrat->NumExterne != "" || $Contrat->NumExterne != null){
                                    $appExternes = json_decode($Contrat->NumExterne); 
                                    
                                    for( $i = 0; $i < count($appExternes) ; $i++) {
                                        $taux_niveau_ext = Fonction::GetTauxNiveau($fractionnement, $schema, trans('var.group'), 
                                                            $Contrat->Produit, $commission->NumQuittance, $dureecontrat, $appExternes[$i], $numPolice, $baseCommission);
                                        $montant_externe = 0;
                                        if($taux_niveau_ext->tauxCommissionnement >= 0){
                                            $montant_externe = (($baseCommission * $taux_niveau_ext->tauxCommissionnement / 100) * $taux_niveau_ext->pourcentage) / 100;
                                        }else{
                                            $montant_externe = $taux_niveau_ext->comfixe;
                                        }
                                        
                                        Fonction::SetMontantAgentGroupe($montant_externe, $appExternes[$i]);
                                    }
                                } */
                                //dd($baseCommission);
                                if(count($taux_niveau) != 0){
                                    for( $i = 0; $i < count($taux_niveau) ; $i++) {
                                        $montantCons = 0;
                                        $montant_CEQP = 0;
                                        $montant_INSPECTION = 0;
                                        $montant_RG = 0;
                                        $baseCommission = $baseCommission - $taux_niveau[$i]->acces;
                                        if($taux_niveau[$i]->tauxCommissionnement >= 0){
                                            $montantCons = (($baseCommission * $taux_niveau[$i]->tauxCommissionnement / 100) * $taux_niveau[$i]->pourcentage) / 100;
                                        }else{
                                            if($baseCommission < 0)
                                                $montantCons = -($taux_niveau[$i]->comfixe);
                                            else
                                                $montantCons = $taux_niveau[$i]->comfixe;
                                        }
                                        
                                        if(isset($taux_niveau[$i]->Agent)){
                                            // Enregistrement des montants dans leurs compte avoirs respective
                                            Fonction::SetMontantAgentGroupe($montantCons, $taux_niveau[$i]->Agent);
                                           
                                            // Mise à  jour de la table Commission
                                            Commission::where('NumCommission', $commission->NumCommission)->where('Apporteur', $taux_niveau[$i]->Agent)->update([
                                                'MontantConseiller' => round($montantCons),
                                                'MontantCEQ' => round($montant_CEQP),
                                                'MontantInspecteur' => round($montant_INSPECTION),
                                                'MontantRG' => round($montant_RG),
                                                'statutcalculer' => 'oui',
                                                'bareme' => $schema
                                            ]);
                                            
                                            // gestion d'apporteur externe
                                            if($taux_niveau[$i]->Agent != Commission::where('NumCommission', $commission->NumCommission)->first()->Apporteur){
                                                $appexterne = Commission::where('NumCommission', $commission->NumCommission)->first()->Toc;
                                                if($appexterne != null && $appexterne != ""){
                                                    $appexterne_decode = json_decode($appexterne);
                                                    if (!in_array($taux_niveau[$i]->Agent, $appexterne_decode)){
                                                        array_push($appexterne_decode, $taux_niveau[$i]->Agent);
                                                        $tabapp_encode = json_encode($appexterne_decode); 
                                                        
                                                        $comexterne = Commission::where('NumCommission', $commission->NumCommission)->first()->Taux;
                                                        $numexterne_decode = json_decode($comexterne);
                                                        array_push($numexterne_decode, $montantCons);
                                                        $tabnum_encode = json_encode($numexterne_decode);
                                                        Commission::where('NumCommission', $commission->NumCommission)->update(["Toc" => $tabapp_encode, "Taux" => $tabnum_encode]);
                                                    }
                                                }else{
                                                    $tabapp = array();
                                                    $tabnum = array();
                                                    array_push($tabapp, $taux_niveau[$i]->Agent);
                                                    array_push($tabnum, $montantCons);
                                                    $tabnum_encode = json_encode($tabnum);
                                                    $tabapp_encode = json_encode($tabapp);
                                                    Commission::where('NumCommission', $commission->NumCommission)->update(["Toc" => $tabapp_encode, "Taux" => $tabnum_encode]);
                                                }
                                            }
                                        }else{
                                            // Enregistrer les agents qui n'existent pas
                                            if (!in_array($taux_niveau[$i]->Agent, $tab_agents))
                                                array_push($tab_agents,$taux_niveau[$i]->Agent);
                                        }
                                    }
                                }
                                else{
                                    //dd($dureecontrat);
                                    if (!in_array($Contrat->Produit, $tab_taux))
                                        array_push($tab_taux, $Contrat->Produit);
                                    array_push($tab_com_taux, json_encode($commission));
                                }
                            }
                        }
                    }
                }
            //}
            }
            
            // Mise à  jour de la table Commission traiter
            Commission::where('NumCommission', $commission->NumCommission)->where('TypeCommission', 'g')->update([
                'ctrl' => 1
            ]);
        }
        // Envoi de mail

        // Récupérer le ou les sp
        $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "sp")->first();
        $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
        $mails = array();
        foreach ($allAdmin as $value) {
            array_push($mails, $value->mailZ);
        }
        //if (isset($infomail->mail)) {
            
                //SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", []);
            
        //}

        TraceController::setTrace("Vous avez lancé le calcul manuel des commissions groupe",session("utilisateur")->idUser);
        
        $message = "";

        if(count($tab_agents) != 0 && count($mails) != 0){
            // Envoyer Erreur
            //dd($tab_agents);
            //flash('Erreur ! Agent(s) inconnu(s) : '.json_encode($tab_agents))->error();
            $message .= ' <br><br> Erreur ! Agent(s) inconnu(s) : '.json_encode($tab_agents);
            //SendMail::sendnotificationErreurAgent($mails, "Erreur lors des calculs des commissions de ce mois !", ["Agent" => $tab_agents]);
            
        }
        if(count($tab_police) != 0 && count($mails) != 0){
            // Envoyer Erreur
            //dd($tab_police);
            $message .= ' <br><br> Erreur ! Contrat(s) inexistant(s) : '.json_encode($tab_police);
            //SendMail::sendnotificationErreurAgent($mails, "Erreur lors des calculs des commissions de ce mois !", ["Contrat" => $tab_police]);
            
        }
        if(count($tab_taux) != 0 && count($mails) != 0){
            // Envoyer erreur
            //dd($tab_taux);
            $message .= ' <br><br> Erreur ! Taux lié au Produit(s) non paramétré(s) : '.json_encode($tab_taux).' <br> Data Commission : '.json_encode($tab_com_taux);
            //SendMail::sendnotificationErreurTaux($mails, "Erreur lors des calculs des commissions de ce mois !", ["Produit" => json_encode($tab_taux)]);
            
        }            
            
        $message .= ' <br><br> Commission groupe calculée';
        return $message;

        //echo "Commission brute";
    }
    
    /**
     *  Get && Set Validation SP
     * */
    public function getcommissioncons() 
    {
        $moisencours = date('m-Y');
        $list = DB::table('commissions')
            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->select('NumCommission as Commission', 'NumQuittance as Quittance', 'NumPolice as Police',
                'contrats.Produit as Produit', 'commissions.bareme as sch',
                'commissions.BaseCommission as base', 'commissions.MontantConseiller as mont', 'commissions.MontantSunShine as montSun',
                'Garantie', 'contrats.Agent as Commercial', 'conv')
            ->where("statutcalculer", "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", $moisencours)
            ->where('confirmercalcule', null); 
            
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;
        
        $search = "Rechercher";
        //dd(request('check'));
        if(request('rec') == 1){
            $commission = DB::table('commissions')
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 1)
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->where('confirmercalcule', null)->get();
            
            if (isset($commission) && sizeof($commission) != 0) {
                if(request('check') != "" && request('check') != null){
                    $search = request('check');
                    $list= $list->whereRaw(" (NumPolice = ".request('check')." or NumCommission = ".request('check')." or NumQuittance = ".request('check')." or Apporteur = ".request('check')." ) ")
                    ->paginate(20);
                    return view('commissiongroupe.listcomcons', compact('list','sigle', 'search'));
                }else{
                    $list = $list->paginate(20);
                    return view("commissiongroupe.listcomcons", compact('list','sigle', 'search'));
                }
            }else{
                
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }
        
        $list = $list->paginate(20);
        
        return view('commissiongroupe.listcomcons', compact('list', 'sigle', 'search'));
    }

    public function confirmercalcul(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', 'oui')
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmercalcule', null)->get();
        $temp_commerciaux = array();
        $error = 0;
        $temp_chef = array();

        if (isset($commission) && sizeof($commission) != 0) {
                
            // Parcourir les commissions

            foreach ($commission as $comm) {
                // Vérifier si le commercial est un conseiller
                //Avec le conseiller, récupérer le chef de son équipe, de son inspection et de son région pour leurs accordés leur commission

                // vérifie s'il est un conseiller
                $checkcons = Fonction::CheckCons($comm->NumPolice);

                if ($checkcons) {

                    $commercial = DB::table('contrats')->where('police', $comm->NumPolice)->first()->Agent;

                    $data_cons = DB::table('commerciauxes')->where('codeCom', $commercial)->first();

                    if($data_cons->codeEquipe == "" || $data_cons->codeEquipe == null) {
                        array_push($temp_commerciaux, $data_cons->codeCom);
                        $error += 1;
                    }else{
                        
                        if($data_cons->codeEquipe != "" && $data_cons->codeEquipe != null){
                            // Code commercial du Chef Equipe
                            if(isset(DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH)){
                                $chefequipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH;
                                if ($chefequipe != 0 && $chefequipe != null){
                                    // Attribuer les soldes au sup
                                    Fonction::SetMontantSupGroupe(($comm->MontantCEQ), $chefequipe);
                                    if (!in_array($chefequipe, $temp_chef))
                                    array_push($temp_chef, $chefequipe);
                                }
                            }
                        }

                        if ($data_cons->codeInspection != "" && $data_cons->codeInspection != null) {
                            
                            // Code commercial du Chef Inspection
                            if(isset(DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH)){
                                $chefins = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH;
                                
                                if ($chefins != 0 && $chefins != null){
                                    // Attribuer les soldes au sup
                                    Fonction::SetMontantSupGroupe(($comm->MontantInspecteur), $chefins);
                                    if (!in_array($chefins, $temp_chef))
                                    array_push($temp_chef, $chefins);
                                }
                            }
                        }
                        
                        if ($data_cons->codeRegion != "" && $data_cons->codeRegion != null) {
                            // Code commercial du Chef Région
                            if(isset(DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH)){
                                $chefrg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH;
                                if ($chefrg != 0 && $chefrg != null){
                                    // Attribuer les soldes au sup
                                    Fonction::SetMontantSupGroupe(($comm->MontantRG), $chefrg);
                                    if (!in_array($chefrg, $temp_chef))
                                    array_push($temp_chef, $chefrg);
                                }
                            }
                        }
                    }
                }
            }
            
            // Validater les commissions d'encadrement 
            if(count($temp_chef) !=0)
                for($i=0; $i < count($temp_chef); $i++){
                    $chefco =  $temp_chef[$i];
                    if($chefco != 0 && $chefco != null){
                        Compteagent::where('Agent', $chefco)->update([
                			'statutEngG' => 1
                		]);
                    }
                }
            
            // Total des commissions
            // Liste des Apporteurs concernés par les commissions de ce mois
            $commissionTotal = DB::table('commissions')
                ->where('statutcalculer', 'oui')
                ->where('confirmercalcule', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->distinct('commissions.Apporteur')->get();
            
            foreach ($commissionTotal as $com){
                // Vérifier si le commercial est un conseiller
                //Avec le conseiller, récupérer le chef de son équipe, de son inspection et de son région pour leurs accordés leur commission

                // vérifie s'il est un conseiller
                $checkcons = Fonction::CheckCons($com->NumPolice);

                if ($checkcons) {

                    $commercial = DB::table('contrats')->where('police', $com->NumPolice)->first()->Agent;

                    $data_cons = DB::table('commerciauxes')->where('codeCom', $commercial)->first();

                    if($data_cons->codeEquipe == "") {
                        /******* CONS *******/

                        // récuperer le taux aib
                        $tauxaibcons = Fonction::RecupererTaux($commercial);

                        // total des montants temporaire
                        //Montant cons

                        $montantcons = Fonction::RecupererCompte($commercial);
                        if($montantcons->statueValideG == 0){
                            $temp_CONS = $montantcons->compteMoisGroupe ;
                            
                            // Calcul du montant aib
                            $mont_aib_cons = $temp_CONS * $tauxaibcons / 100;
    
                            // Calcul du montant net à payer
                            $mont_cons = $temp_CONS - $mont_aib_cons;
    
                            // Règlement des retenues
                            $mont_net_cons = $mont_cons ; //- $montantcons->retenue;
                            
                            // Set montant net payer
                            DB::table('compteagents')->where('Agent', $commercial)->update([
                                'compteNetapayerGroupe' => $mont_net_cons,
                                'aibCalculerGroupe' => $mont_aib_cons
                            ]);
    
                            // Verser net à payer dans le compte
                            Fonction::setCompteGroupe($commercial, $mont_net_cons);
    
                        }
                        array_push($temp_commerciaux, $data_cons->codeCom);

                        $error += 1;
                    }else{
                        // Code commercial du Chef Equipe
                        $chefequipe= 0;
                        if($data_cons->codeEquipe != "" && $data_cons->codeEquipe != null)
                            $chefequipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH;

                        // Code commercial du Chef Inspection
                        $chefins = 0;
                        if ($data_cons->codeInspection != "" && $data_cons->codeInspection != null)
                            $chefins = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $data_cons->codeInspection)->first()->managerH;

                        // Code commercial du Chef Région
                        $chefrg = 0;
                        if ($data_cons->codeRegion != "" && $data_cons->codeRegion != null)
                            $chefrg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $data_cons->codeRegion)->first()->managerH;

                        /******* CONS *******/

                        // récuperer le taux aib
                        $tauxaibcons = Fonction::RecupererTaux($commercial);

                        // total des montants temporaire
                        //Montant cons

                        $montantcons = Fonction::RecupererCompte($commercial);
                        if($montantcons->statueValideG == 0){
                            $temp_CONS = $montantcons->compteMoisGroupe ;
                            // Calcul du montant aib
                            $mont_aib_cons = $temp_CONS * $tauxaibcons / 100;
    
                            // Calcul du montant net à payer
                            $mont_cons = $temp_CONS - $mont_aib_cons;
    
                            // Règlement des retenues
                            $mont_net_cons = $mont_cons ;//- $montantcons->retenue;
                            
                            // Set montant net payer
                            DB::table('compteagents')->where('Agent', $commercial)->update([
                                'compteNetapayerGroupe' => $mont_net_cons,
                                'aibCalculerGroupe' => $mont_aib_cons
                            ]);
    
                            // Verser net à payer dans le compte
                            Fonction::setCompteGroupe($commercial, $mont_net_cons);
    
                        }

                        /******* CHEF EQUIPE *******/

                        if($chefequipe != 0 && $chefequipe != null){
                            // récuperer le taux aib
                            $tauxaibchefequipe = Fonction::RecupererTaux($chefequipe);

                            // total des montants temporaire 

                            // Montant Chef Equipe
                            $montantchefequipe = Fonction::RecupererCompte($chefequipe);
                            if($montantchefequipe->statueValideG == 0){
                                
                                $temp_ChefEquipe = $montantchefequipe->compteMoisGroupe + 
                                $montantchefequipe->compteEncadrementMoisCalculerGroupe ;
    
                                // Calcul du montant aib
                                $mont_aib_eqp = $temp_ChefEquipe * $tauxaibchefequipe / 100;
    
                                // Calcul du montant net à payer
                                $mont_eqp = $temp_ChefEquipe - $mont_aib_eqp;
    
                                // Règlement des retenues
                                $mont_net_eqp = $mont_eqp ;//- $montantchefequipe->retenue;
    
                                // Set montant net payer
                                DB::table('compteagents')->where('Agent', $chefequipe)->update([
                                    'compteNetapayerGroupe' => $mont_net_eqp,
                                    'aibCalculerGroupe' => $mont_aib_eqp
                                ]);
    
                                 // Verser net à payer dans le compte
                                Fonction::setCompteGroupe($chefequipe, $mont_net_eqp);   
                            
                            }
                        }


                        /******* CHEF INSPECTEUR *******/

                        if($chefins != 0 && $chefins != null){
                            // récuperer le taux aib
                            $tauxaibchefins = Fonction::RecupererTaux($chefins);

                            // total des montants temporaire

                            // Montant INS
                            $montantchefins = Fonction::RecupererCompte($chefins);
                            if($montantchefins->statueValideG == 0){
                                
                                $temp_ChefIns = $montantchefins->compteMoisGroupe + 
                                $montantchefins->compteEncadrementMoisCalculerGroupe; 
    
                                // Calcul du montant aib
                                $mont_aib_ins = $temp_ChefIns * $tauxaibchefins / 100;
    
                                // Calcul du montant net à payer
                                $mont_ins = $temp_ChefIns - $mont_aib_ins;
    
                                // Règlement des retenues
                                $mont_net_ins = $mont_ins ;//- $montantchefins->retenue;
    
                                // Set montant net payer
                                DB::table('compteagents')->where('Agent', $chefins)->update([
                                    'compteNetapayerGroupe' => $mont_net_ins,
                                    'aibCalculerGroupe' => $mont_aib_ins
                                ]);
    
                                // Verser net à payer dans le compte
                                Fonction::setCompteGroupe($chefins, $mont_net_ins);
                                
                            }
                        }

                        /******* CHEF REGION *******/

                        if($chefrg != 0 && $chefrg != null){
                            // récuperer le taux aib
                            $tauxaibrg = Fonction::RecupererTaux($chefrg);

                            // total des montants temporaire
                            // Montant RG
                            $montantchefrg = Fonction::RecupererCompte($chefrg);
                            if($montantchefrg->statueValideG == 0){
                                $temp_ChefRg = $montantchefrg->compteMoisGroupe + $montantchefrg->compteEncadrementMoisCalculerGroupe;
                                // Calcul du montant aib
                                $mont_aib_rg = $temp_ChefRg * $tauxaibrg / 100;
    
                                // Calcul du montant net à payer
                                $mont_rg = $temp_ChefRg - $mont_aib_rg;
    
                                // Règlement des retenues
                                $mont_net_rg = $mont_rg; // - $montantchefrg->retenue;
    
                                // Set montant net payer
                                DB::table('compteagents')->where('Agent', $chefrg)->update([
                                    'compteNetapayerGroupe' => $mont_net_rg,
                                    'aibCalculerGroupe' => $mont_aib_rg
                                ]);
    
                                // Verser net à payer dans le compte
                                Fonction::setCompteGroupe($chefrg, $mont_net_rg);
                                  
                            }
                        }
                    }
                }

                // Mise à jour de commission pour confirmer calcule

                DB::table('commissions')
                    ->where('NumCommission', $com->NumCommission)
                    ->update([
                        "confirmercalcule" => "oui"
                    ]);
            }
            
            /*******************************/
            
            $listAgents = DB::table('compteagents')->select('Agent')->where("compteMoisGroupe", "!=", 0)->where('statueValideG', 0)->get();
            
            if(count($listAgents) !=0)
                for($i=0; $i < count($listAgents); $i++){
                $managco =  $listAgents[$i]->Agent;
                if($managco != 0 && $managco != null){
                            // récuperer le taux aib
                            $tauxaibchefequipe = Fonction::RecupererTaux($managco);

                            // total des montants temporaire

                            // Montant Chef 
                            $montantchefequipe = Fonction::RecupererCompte($managco);
                            if($montantchefequipe->statueValideG == 0){
                                
                                $temp_ChefEquipe = $montantchefequipe->compteMoisGroupe + 
                                $montantchefequipe->compteEncadrementMoisCalculerGroupe ;
                                
                                // Calcul du montant aib
                                $mont_aib_eqp = $temp_ChefEquipe * $tauxaibchefequipe / 100;
    
                                // Calcul du montant net à payer
                                $mont_eqp = $temp_ChefEquipe - $mont_aib_eqp;
    
                                // Règlement des retenues
                                $mont_net_eqp = $mont_eqp ;//- $montantchefequipe->retenue;
    
                                // Set montant net payer
                                DB::table('compteagents')->where('Agent', $managco)->update([
                                    'compteNetapayerGroupe' => $mont_net_eqp,
                                    'aibCalculerGroupe' => $mont_aib_eqp
                                ]);
    
                                 // Verser net à payer dans le compte
                                Fonction::setCompteGroupe($managco, $mont_net_eqp);  
                            }
                        }
            }
            
            /*******************************/
            
            
            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "allGroupe";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = date('m-Y');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save(); 

            $message = "Les commerciaux dont les codes commerciaux suivants ne sont pas dans une hiérarchie. <br> Codes :  ";
            if ($error != 0){
                //dd(count($temp_commerciaux));
                $temp = array();
                for ($i=0; $i < count($temp_commerciaux); $i++) {
                    if (!in_array($temp_commerciaux[$i], $temp )) {
                        $message .= $temp_commerciaux[$i]. " ; ";
                        array_push($temp, $temp_commerciaux[$i]);
                    }
                }
                
            }

            // Envoi de mail

            // Récupérer le ou les sp
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "csp")->first();
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }

            TraceController::setTrace("Vous avez confirmé groupe les commissions.",session("utilisateur")->idUser);
            return $message.". Commissions confirmées avec succès.";
        }else{
            return " Pas de commission groupe en attente!!!";
            flash(" Pas de commission groupe en attente!!! ");
        }
    }
    
    public function setrejetsp(){
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // Réinitialiser les comptes agents 
            
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
                    "compteMoisGroupe" => 0,
                ]);
            }

            // Envoi de mail
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                $mail = $allAdmin[0]->mailZ;
                foreach ($allAdmin as $value) {
                    if($mail != $value->mailZ)
                        array_push($mails, $value->mailZ);
                }
                //SendMail::sendnotification($mail, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetsp");
            
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('CodeCommission', "allgroupe")->where('DateCalculer', date('m-Y'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', date('m-Y'))
                    ->update([
                        'DateCalculer' => request('motif')
                    ]);
            }else{
                
                // Sinon enregistrer en tant que rejet 
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "allgroupe";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->signature;
                $addSignataire->DateCalculer = request('motif');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
            }
            
            flash("Vous avez rejeté les commissions groupe en cours.");
            TraceController::setTrace("Vous avez rejeté les commissions groupe en cours.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('listComG');
            
        }else{
            // Pas de commission disponible
            flash(" Pas de commission groupe en attente!!! ");
            return redirect()->route('listComG');
        }
        
    }
    
    /**
     *  Get && Set Validation CSP
     * */
    
    public function getvalidationcsp(){
        $verif = DB::table('commissions')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101]);
        else
            $list = DB::table('commissions')->select('commissions.Apporteur as Commercial')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->distinct('commissions.Apporteur');
            
        $libelleRole = "CSP";

        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;

        $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->first(); // TEST
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
                ->where("ctrl", 1)
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('Agent', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commissiongroupe.listcomcsp', compact('list','libelleRole','sigle', 'search', 'signaturesp'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commissiongroupe.listcomcsp", compact('list','libelleRole','sigle', 'search', 'signaturesp'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }

        $list = $list->paginate(10000);

        return view('commissiongroupe.listcomcsp', compact('list', 'libelleRole', 'sigle', 'signaturesp', 'search'));
    }
    
    public function setrejetcsp(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmercsp', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            $agents = DB::table('compteagents')->select('compteagents.Agent as Apporteur')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])->get();
            foreach ($agents as $agent) {
                $compte = DB::table('compteagents')->where('Agent', $agent->Apporteur)->first();
                
                    DB::table('compteagents')->where('Agent', $agent->Apporteur)->update([
                        'aibCalculerGroupe' => 0,
                        'compteNetapayerGroupe' => 0,
                        'compteEncadrementMoisCalculerGroupe' => 0,
                        'compteGroupe' => 0,
                        'statueValideG' => 0
                    ]);
            }
            
            // mettre à jour la table commission par null
            foreach ($commission as $comm) {
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmercalcule" => null
                ]);
            }

            // Envoi de mail
            $infomail = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailA')->where("roles.code", "sp")->first();
            //if (isset($infomail->mail)) {
                $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
                $mails = array();
                foreach ($allAdmin as $value) {
                    array_push($mails, $value->mailZ);
                }
                //SendMail::sendnotification($infomail->mailA, $mails, "Vérification et validation des commissions NSIA VIE ASSURANCES", [], "rejetcsp");
            //}
        
            // Vérifie s'il avait validé
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', date('m-Y'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', date('m-Y'))
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
            
            flash("Vous avez rejeté les commissions groupe.");
            TraceController::setTrace("Vous avez rejeté les commissions groupe.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCCSPG');

        }else{
            flash(" Pas de commission groupe disponible!!! ");
            //return Back();
            return redirect()->route('GCCSPG');
        }
    }

    public function setvalidationcsp(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;

            foreach ($commission as $comm) {

                // mettre à jour la table commission par oui

                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmercsp" => "oui"
                ]);
                $a++;
            }
            $a--;

            // Enregistrer dans la table Signataire
            $addSignataire = new Signataire();
            $addSignataire->idSignataire = session('utilisateur')->idUser;
            $addSignataire->CodeCommission = $commission[$a]->NumCommission;
            $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
            $addSignataire->pathSignataire = session('utilisateur')->signature;
            $addSignataire->DateCalculer = date('m-Y');
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

            flash("Vous avez confirmé les commissions groupe.");
            TraceController::setTrace("Vous avez confirmé les commissions groupe.", session("utilisateur")->idUser);

            //return Back();
            return redirect()->route('GCCSPG');

        }else{
            flash(" Pas de commission groupe disponible!!! ");
            //return Back();
            return redirect()->route('GCCSPG');
        }
    }
    
    /**
     *  Get Validation DT
     * */

    public function getvalidationdt(){
        $verif = DB::table('commissions')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', null)
                ->where("ctrl", 1)
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101]);
        else
            $list = DB::table('commissions')
            ->select('commissions.Apporteur as Commercial')
            ->where("statutcalculer", "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', null)
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->distinct('commissions.Apporteur'); 
            //->paginate(10000);
        $libelleRole = "DT";
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;

        // signature du précédent 
        /*$signaturecsp = "";
        if (isset(DB::table("signataires")->where('DateCalculer', date('m-Y'))->first()->pathSignataire)) {
            $signaturecsp = DB::table("signataires")->where('DateCalculer', date('m-Y'))->first()->pathSignataire;
        } */

        // Le premier signataire du mois est bien sur csp
        $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get(); // TEST
        $signaturesp = "";
        $signaturecsp = "";
        if(isset($signature[0]->pathSignataire))
            $signaturesp = $signature[0]->pathSignataire;
        if(isset($signature[1]->pathSignataire))
            $signaturecsp = $signature[1]->pathSignataire;

        $search = "Rechercher";
        if(request('rec') == 1){
            // Liste des commissions concernés
            $commission = DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('TypeCommission', 'g')
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('commissions.Apporteur', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commissiongroupe.listcomdt', compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commissiongroupe.listcomdt", compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp'));
                }

            }else{
                flash(" Pas de commission groupe disponible!!! ");
                return Back();
            }
        }

        $list = $list->paginate(10000);

        return view('commissiongroupe.listcomdt', compact('list', 'libelleRole', 'sigle', 'signaturesp', 'signaturecsp', 'search'));
    }

    public function setvalidationdt(Request $request){
        // La confirmation concerne les commissions calculé du mois actuel

        // Liste des commissions concernés
        $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmerdt', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;

            foreach ($commission as $comm) {
                // mettre à jour la table commission par oui

                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmerdt" => "oui"
                ]);
                $a++;

            }
            $a--;

            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = $commission[$a]->NumCommission;
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = date('m-Y');
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

            flash("Vous avez confirmé les commissions groupe.");
            TraceController::setTrace("Vous avez confirmé les commissions groupe.", session("utilisateur")->idUser);
            return redirect()->route('GCDTG');
            //return Back();
        }else{
            flash(" Pas de commission groupe disponible!!! ");
            //return Back();
            return redirect()->route('GCDTG');
        }
    }
    
    public function setrejetdt(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmerdt', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            foreach ($commission as $comm) {
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmercsp" => null
                ]);
            }

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
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', date('m-Y'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', date('m-Y'))
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
            
            flash("Vous avez rejeté les commissions groupe.");
            TraceController::setTrace("Vous avez rejeté les commissions groupe.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCDTG');

        }else{
            flash(" Pas de commission groupe disponible!!! ");
            //return Back();
            return redirect()->route('GCDTG');
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
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101]);
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
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            //->where('commissions.moiscalculer', date('m-Y')) // Mode TEST
            ////->where('commissions.moiscalculer', date('m-Y'))
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
        if (isset(DB::table("signataires")->where('DateCalculer', date('m-Y'))->get()) {
            $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get();
        }*/

        // Le premier signataire du mois est bien sur csp
        $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get(); // TEST
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
                ->where('TypeCommission', 'g')
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('commissions.Apporteur', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commissiongroupe.listcomdg', compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commissiongroupe.listcomdg", compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }

        $list = $list->paginate(10000);

        return view('commissiongroupe.listcomdg', compact('list', 'search', 'libelleRole', 'sigle', 'signaturesp', 'signaturecsp', 'signaturedt'));
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
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmerdg', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;

            foreach ($commission as $comm) {
                // mettre à jour la table commission par oui

                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmerdg" => "oui"
                ]);
                $a++;

            }
            $a--;
            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = $commission[$a]->NumCommission;
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = date('m-Y');
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

            flash("Vous avez confirmé les commissions groupe.");
            TraceController::setTrace(
                "Vous avez confirmé les commissions groupe.",
                session("utilisateur")->idUser);

            //return Back();
            return redirect()->route('GCDGG');
        }else{
            flash(" Pas de commission groupe disponible !!! ");
            //return Back();
            return redirect()->route('GCDGG');
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
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmerdg', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            foreach ($commission as $comm) {
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmerdt" => null
                ]);
            }

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
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', date('m-Y'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', date('m-Y'))
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
            
            flash("Vous avez rejeté les commissions groupe.");
            TraceController::setTrace("Vous avez rejeté les commissions groupe.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCDGG');

        }else{
            flash(" Pas de commission disponible pour ce mois!!! ");
            //return Back();
            return redirect()->route('GCDGG');
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
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101]);
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
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmercdaf', null)
            //->where('commissions.moiscalculer', date('m-Y')) // Mode TEST
            ////->where('commissions.moiscalculer', date('m-Y'))
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
        if (isset(DB::table("signataires")->where('DateCalculer', date('m-Y'))->get()) {
            $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get();
        }*/

        // Le premier signataire du mois est bien sur csp
        $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get(); // TEST
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
                ->where('TypeCommission', 'g')
                ->where('confirmercdaf', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('commissions.Apporteur', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commissiongroupe.listcomdg', compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commissiongroupe.listcomdg", compact('list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg'));
                }

            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }

        $list = $list->paginate(10000);
        return view('commissiongroupe.listcomcdaf', compact('list', 'libelleRole','sigle', 'search', 'signaturesp', 'signaturecsp', 'signaturedt', 'signaturedg'));
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
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmercdaf', null)
            ->get();
        if (isset($commission) && sizeof($commission) != 0) {

            // Parcourir les commissions
            $a = 0;

            foreach ($commission as $comm) {
                // mettre à jour la table commission par oui

                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmercdaf" => "oui"
                ]);

                $a++;

            }
            $a--;

            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = $commission[$a]->NumCommission;
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = date('m-Y');
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

            flash("Vous avez confirmé les commissions groupe.");
            TraceController::setTrace(
                "Vous avez confirmé les commissions groupe.",
                session("utilisateur")->idUser);
            return redirect()->route('GCCDAFG');
            //return Back();
        }else{
            flash(" Pas de commission groupe disponible!!! ");
            return redirect()->route('GCCDAFG');
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
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
            ->where('confirmercdaf', null)
            ->get();

        if (isset($commission) && sizeof($commission) != 0) {
            
            // mettre à jour la table commission par null
            foreach ($commission as $comm) {
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmerdg" => null
                ]);
            }

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
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', date('m-Y'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', date('m-Y'))
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
            
            flash("Vous avez rejeté les commissions groupe.");
            TraceController::setTrace("Vous avez rejeté les commissions groupe.", session("utilisateur")->idUser);
            
            //return Back();
            return redirect()->route('GCCDAFG');

        }else{
            flash(" Pas de commission groupe disponible!!! ");
            //return Back();
            return redirect()->route('GCCDAFG');
        }
    }
    
    /**
     *  Get Validation Trésorerie
     * */

    public function getvalidationtresorerie(Request $request){
        
        // Exportation en Excel
        if (request('Excel') == "Excel") {
            
            $tabl = CommissionGroupeController::ExporterEnExcel(request('reglement'));
            $request->request->add(['reglement' => 'value']);
            //$date = date('m-Y');
            $date = date('m-Y'); // Mode TEST
            
            // Exporter tous les commissions 
            $autre = new Collection($tabl);
            Session()->put('allcommission', $autre);
            TraceController::setTrace("Vous avez exporté les commissions du mois de ".utf8_encode(strtoupper(strftime('%B %Y')))." en Excel.",session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportCommissionAll, 'Commission_'.$date.'_Export_'.date('Y-m-d-h-i-s').'.xlsx');
        }

        // Exportattion en pdf
        if (request('PDF') == "PDF") {
            TraceController::setTrace("Vous avez exporté les commissions groupe ".utf8_encode(strtoupper(strftime('%B %Y')))." en PDF.",session("utilisateur")->idUser);
            $path = CommissionGroupeController::ExporterEnPDF(request('reglement'));

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
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
        if(count($verif) != 0)
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101]);
        else
            $list = DB::table('commissions')->select('commissions.Apporteur as Commercial')
            ->where("statutcalculer", "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where('confirmercdaf', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
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
        
        // Le premier signataire du mois est bien sur sp
        $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get(); // TEST
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
                ->where('TypeCommission', 'g')
                ->where("commissions.Statut", date('m-Y'))
                ->where('confirmertresorerie', null)
                ->get();
            if (isset($commission) && sizeof($commission) != 0) {

                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('Agent', 'like', request('check').'%')
                    ->paginate(10000);
                    return view('commissiongroupe.listcomtresorerie', compact('listPayement','list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
                }else{
                    $list = $list->paginate(10000);
                    return view("commissiongroupe.listcomtresorerie", compact('listPayement','list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
                }

            }else{
                flash(" Pas de commission groupe disponible!!! ");
                return Back();
            }
        }
        
        if ((request('reglement') != null && request('reglement') != "all") && request('recherche') == "rech") {
                    $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])
                    ->where("compteagents.libCompte", 'like', '%'.request('reglement').'%')->paginate(10000); 
                
                return view('commissiongroupe.listcomtresorerie', compact('listPayement', 'list','sigle', 'search', 'signaturesp', 'libelleRole', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
        }

        $list = $list->paginate(10000);

        return view('commissiongroupe.listcomtresorerie', compact('listPayement', 'list', 'search', 'libelleRole', 'sigle', 'signaturesp', 'signaturecsp', 'signaturedt', 'signaturedg', 'signaturecdaf', 'reglement'));
    }
    
    public function setrejettresorerie(){
        
        // Réinitialiser les commissions concernés
         $commission = DB::table('commissions')
            ->where('statutcalculer', "oui")
            ->where('confirmercalcule', "oui")
            ->where('confirmercsp', "oui")
            ->where('confirmerdt', "oui")
            ->where('confirmerdg', "oui")
            ->where('confirmercdaf', "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'g')
            ->where("commissions.Statut", date('m-Y'))
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
            if(isset(DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)->where('DateCalculer', date('m-Y'))->first()->Nom)){
                DB::table('signataires')->where('RoleSignataire', session('utilisateur')->Role)
                    ->where('DateCalculer', date('m-Y'))
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
            
            flash("Vous avez rejeté les commissions groupe.");
            TraceController::setTrace("Vous avez rejeté les commissions groupe.", session("utilisateur")->idUser);
            
            return Back();
            //return redirect()->route('GCCDAF');

        }else{
            flash(" Pas de commission groupe disponible!!! ");
            return Back();
        }
    }
    
    public function ExporterEnExcel($reglement)
    {
        if ($reglement == "all") {

            $list = DB::table('compteagents')->select('Agent as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerGroupe as nette', 'compteagents.compteGroupe as compte')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])->orderBy("compteagents.libCompte", "desc")->get();
            
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
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerGroupe as nette', 'compteagents.compteGroupe as compte')->where("compteagents.libCompte", $reglement)->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])->get();

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
        
            
            if ($reglement != "all") {
                $list = DB::table('compteagents')->select('Agent as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerGroupe as nette',
            'compteagents.compteGroupe as compte')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])->where("compteagents.libCompte", $reglement)->get();
            
            }else{
                $list = DB::table('compteagents')->select('Agent as Commercial', 'compteagents.libCompte as libelleReglement', 
            'compteagents.numCompte as NumReglement', 'compteagents.compteNetapayerGroupe as nette', 
            'compteagents.compteGroupe as compte')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])->orderBy("compteagents.libCompte", "desc")->get();
            
            }
            
            //dd($list);
            

            // Générer code série QR
            $serieQR = CommissionGroupeController::genererserie();

            // Créer l'image QRCODE montant, periode, nombre apporteur

            // Recherche des données de l'image
            $i = 0; $mont = 0;
            foreach ($list as $value) {
                $mont += $value->compte;
                $i++;
            }
            
            $nombreApporteur = $i;

            $texte = "Montant de l'Etat : ".$mont." CFA. Période : ".date('d-m-Y').". Nombre Apporteur : ".$nombreApporteur.".";

            $pathQr = CommissionGroupeController::scanqrcode($texte, $serieQR);

            // Enregistrer la trace dans la table récap_commission

            // Controller si série existe déjà ou pas

            //if (isset(DB::table('recapcommissions')->where('periode', date('m-Y'))->first()->code)){
            if (!isset(DB::table('recapcommissions')->where('periode', date('d-m-Y'))->first()->serie)){ 
                $add = new  Recapcommission();
                $add->serie = $serieQR;
                $add->codeQR = $pathQr;
                $add->periode = date('d-m-Y'); 
                $add->montantEtat = $mont;
                $add->nombreAgent = $nombreApporteur;
                if ($reglement == "all") {
                   $add->all = 1;
                }
                $add->save();
            } 

            setlocale(LC_TIME,  'french');

            $datelettre = utf8_encode(strtoupper(strftime('%d %B %Y')));

            $signature = DB::table("signataires")->where('DateCalculer', date('m-Y'))->get(); // TEST
            $signaturecdaf = $signature[4]->pathSignataire;
            $signaturedg = $signature[3]->pathSignataire;
            $signaturedt = $signature[2]->pathSignataire;
            $signaturecsp = $signature[1]->pathSignataire;
            $signaturesp = $signature[0]->pathSignataire;

            // Création du PDF

            //$titre = "REPARTITION DES COMMISSIONS COMPLEMENT DU MOIS DE ".$datelettre;
            $titre = "REPARTITION DES COMMISSIONS GROUPE DU ".$datelettre;
            $path = "document/commission/".$serieQR.".pdf";

            //create pdf document
            $pdf = new PDFFF();
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

            $code = "NVA".$string.date("dmY"); 
            
            // vérification si le pdf à été déjà généré pour la période
            if (!isset(DB::table('recapcommissions')->where('periode', date('d-m-Y'))->first()->serie)){
                if (!isset(DB::table('recapcommissions')->where('serie', $code)->first()->serie)){
                    return $code;
                }
                else
                    CommissionController::genererserie();
            }else
            return DB::table('recapcommissions')->where('periode', date('d-m-Y'))->first()->serie;
    }
    
    public function getreglement(){
        $montallg = "";
        $qrg = "";
        $allreglementg = "";

        $temp = Session::get('montallg');
        //dd(Session::get('allreglement'));
        if (isset($temp) && $temp == 0) {
            Session::put('montallg', 0);
            Session::put('qrg', "" );
            Session::put('allreglementg', "" );
        }

        if (Session::get('montallg') != "") {
            $montallg = Session::get('montallg');
            $qrg = Session::get('qrg');
            $allreglementg = Session::get('allreglementg');
        }
       
        if (request("vue") == "vue" || request('recherchevue') == 1) {
            $qrg = trim(htmlspecialchars(request("qr")));
            if(isset(DB::table('recapcommissions')->where('all', 1)->where('serie', trim(htmlspecialchars(request("qr"))))->first()->montantEtat))
            {
                $montallg = DB::table('recapcommissions')->where('all', 1)->where('serie', trim(htmlspecialchars(request("qr"))))->first()->montantEtat;
                $allreglementg = DB::table('reglements')->where('RecapCommission', trim(htmlspecialchars(request("qr"))))->get();
                Session::put('montallg', $montallg );
                Session::put('qrg', $qrg );
                Session::put('allreglementg', $allreglementg );
                flash("La somme du code QR est affiché.");
            }else{
                flash("Le code QR n'existe pas")->error();
                return Back();
            }
            
        }
        
        $listPayement = DB::table('structures')->get();

        return view("commissiongroupe.reglement", compact("listPayement", "montallg", "qrg", "allreglementg"));
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
        Session::put('montallg', $montall );
        Session::put('qrg', $qr );
        Session::put('allreglementg', $allreglement );
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

        // Vérification des montants reverser
        if(!isset( DB::table('recapcommissions')->where('serie', session('qrg'))->first()->montantEtat)){
            flash("Le code QR n'existe pas ou n'est pas renseigné.")->error();
            return Back();
        }
        $allreglement = DB::table('reglements')->where('RecapCommission', session('qrg'))->get();
        $montglobal = DB::table('recapcommissions')->where('serie', session('qrg'))->first()->montantEtat;
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

            // Liste des commissions concernés
            $commission = DB::table('commissions')
                ->where('statutcalculer', "oui")
                ->where('confirmercalcule', "oui")
                ->where('confirmercsp', "oui")
                ->where('confirmerdt', "oui")
                ->where('confirmerdg', "oui")
                ->where('confirmercdaf', "oui")
                ->where('confirmertresorerie', null)
                ->where('TypeCommission', 'g')
                ->where("ctrl", 1)
                ->where("commissions.Statut", date('m-Y'))
                ->get();

            // Parcourir les commissions
            $a = 0;

            foreach ($commission as $comm) {
                // mettre à jour la table commission par oui
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "confirmertresorerie" => "oui"
                ]);
                $a++;
            }
                    
            $list = DB::table('compteagents')->select('compteagents.Agent as Commercial')->where("statueValideG", 1)->whereNotIn('Agent', [1,2,3, 101])->get();
            
            // Etablir Fiches de paie pour les apporteurs
            foreach ($list as $apporteur) {

                $infoapporteur = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first();
                
                if(!isset(DB::table('documents')->where('Agent', $infoapporteur->codeCom)->where('periode', date('m-Y'))->first()->id)){
                
                    if ($infoapporteur->Niveau == "INS") {
                        // Ce que lui même à reçu sur ses affaires
                        $detaillui = DB::table('commissions')
                        ->where("ctrl", 1)
                        ->where('TypeCommission', 'g')
                        ->where("commissions.Statut", date('m-Y'))
                        ->where("commissions.Apporteur", $infoapporteur->codeCom)->get();
                        
                        // Ce qu'il a gagner en tant chef d'inspection
                        $codeINSP = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                        $detailins = DB::table('commissions')
                        //->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                        ->join('commerciauxes', 'commissions.Apporteur', '=', 'commerciauxes.codecom')
                        ->where("ctrl", 1)
                        ->where('TypeCommission', 'g')
                        ->where("commissions.Statut", date('m-Y'))
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
                            ->where('TypeCommission', 'g')
                            ->where("commissions.Statut", date('m-Y'))
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                $datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
    
                                // Création du PDF
                                $titre ="";
                                if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                    $titre = "SYNTHESE COMMISSION";
                                else
                                    $titre = "FICHE DE PAIE CONSEILLER COMMERCIAL ";
                                    
                                $lib = "GROUPE_FICHEPAIE_".$apporteur->Commercial."_".utf8_encode(strtoupper(strftime('%B %Y')));
                                $path = "document/commission/".$lib.".pdf";
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFFF();
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
                                $pdf->Text(70, 42, "APPORTEUR :");
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
                                $pdf->Text(70, 64,iconv('utf-8','cp1252','Tel :'));
                                $pdf->Text(90, 64,iconv('utf-8','cp1252', $infoapporteur->telCom));
    
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
                                $pdf->Text(80, 78, iconv('utf-8','cp1252', number_format(($frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 80, 100, 80);
                                $pdf->Text(10, 84, iconv('utf-8','cp1252', "Autres Commissions : "));
                                $pdf->Text(80, 84, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 86, 100, 86);
                                $pdf->Text(10, 90, iconv('utf-8','cp1252', "Fixe : "));
                                $pdf->Text(80, 90, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 92, 100, 92);
                                $pdf->Text(10, 96, iconv('utf-8','cp1252', "Bonus : "));
                                $pdf->Text(80, 96, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 98, 100, 98);
    
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 102, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 102, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe ), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 104, 100, 104);
                                
                                if( !in_array($infoapporteur->Niveau, trans('var.group') )){
                                
                                    $pdf->SetFont("Arial", "", 7);
                                    $pdf->setFillColor(230,230,230);
                                    $pdf->Text(10, 108, iconv('utf-8','cp1252', "Dotation Téléphonie (nature) : "));
                                    $pdf->Text(80, 108, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 110, 100, 110);
                                    $pdf->Text(10, 114, iconv('utf-8','cp1252', "Dotation Carburant (nature) : "));
                                    $pdf->Text(80, 114, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 116, 100, 116);
                                    
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(10, 120, iconv('utf-8','cp1252', "Total en nature : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Text(80, 120, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 122, 100, 122);
                                
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total brute : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe ), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 128, 100, 128);
                                }else{
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total brute : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 128, 100, 128);
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
                                $pdf->Text(110, 78, iconv('utf-8','cp1252', "Taux Impôt : "));
                                $pdf->Text(180, 78, iconv('utf-8','cp1252', (Fonction::RecupererTaux($infoapporteur->codeCom))." %"));
                                $pdf->Line(110, 80, 197, 80);
                                $pdf->Text(110, 84, iconv('utf-8','cp1252', "Impôt : "));
                                $pdf->Text(180, 84, iconv('utf-8','cp1252', number_format(($frais->aibCalculerGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 86, 197, 86);
                                $pdf->Text(110, 90, iconv('utf-8','cp1252', "Avance payée ce mois : "));
                                $pdf->Text(180, 90, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 92, 197, 92);
                                $pdf->Text(110, 96, iconv('utf-8','cp1252', "Prélèvement : "));
                                $pdf->Text(180, 96, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 98, 197, 98);
                                if($frais->compteBloquer < 0){
                                    $pdf->Text(110, 100, iconv('utf-8','cp1252', "Solde à débiter le mois prochain : "));
                                    $pdf->Text(180, 100, iconv('utf-8','cp1252', number_format((abs(0)), 0, '.', ' ')." CFA"));
                                }
                                $pdf->Line(110, 104, 197, 104);
                                $pdf->Line(110, 110, 197, 110);
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 114, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 114, iconv('utf-8','cp1252', number_format(( $frais->aibCalculerGroupe ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 116, 197, 116);
    
                                $pdf->SetFont("Arial", "B", 8);
    
                                // Total sur les actives
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 138, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 138, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 140, 197, 140);
                                
    
                                // Total sur les passives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 147, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 147, iconv('utf-8','cp1252', number_format((  $frais->aibCalculerGroupe ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 149, 197, 149);
    
                                // Total
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 156, iconv('utf-8','cp1252', "Total net  : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 156, iconv('utf-8','cp1252', number_format(($frais->compteGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 158, 197, 158);
    
                                if( in_array($infoapporteur->Niveau, trans('var.group') )){
                                    // Signature Commercial
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(20, 168, iconv('utf-8','cp1252', "Signature DT : "));
                                    $pdf->SetTextColor(0,0,0);
                                    
                                    $datadt = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dt")->where("roles.statut", 0)->first();
                                    
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(20, 188, iconv('utf-8','cp1252', $datadt->nom.' '.$datadt->prenom));
        
                                    // Signature Société
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(150, 168, iconv('utf-8','cp1252', "Signature DG : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $datadg = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dg")->where("roles.statut", 0)->first();
                                    
                                    //$pdf->Image($soc->signature, 150, 170, -150);
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(150, 188, iconv('utf-8','cp1252', $datadg->nom.' '.$datadg->prenom));
                                }
                                else{
                                    // Signature Commercial
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(20, 168, iconv('utf-8','cp1252', "Signature du Conseiller Commercial : "));
                                    $pdf->SetTextColor(0,0,0);
        
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(35, 188, iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
        
                                    // Signature Société
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(150, 168, iconv('utf-8','cp1252', "Signature de la Société : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Image($soc->signature, 150, 170, -150);
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(160, 188, iconv('utf-8','cp1252', $soc->libelleSociete));
                                }
    
                                $pdf->Output('F', $path);
                                $fiche_O = $path;
                                $lib_fiche_O = $lib;
    
                                
    
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
                            ->where('TypeCommission', 'g')
                            ->where("ctrl", 1)
                            ->where("commissions.Statut", date('m-Y'))
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                //$datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
                                $datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
    
                                // Création du PDF
                                $titre ="";
                                if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                    $titre = "SYNTHESE COMMISSION";
                                else
                                    $titre = "FICHE DE PAIE CONSEILLER COMMERCIAL ";
                                
                                $lib = "GROUPE_FICHEPAIE_DUPLICATA_".$apporteur->Commercial."_".utf8_encode(strtoupper(strftime('%B %Y')));
                                $pathd = 'document/commission/'.$lib.'.pdf';
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFFF();
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
                                $pdf->Text(70, 64,iconv('utf-8','cp1252','Tel :'));
                                $pdf->Text(90, 64,iconv('utf-8','cp1252', $infoapporteur->telCom));
    
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
                                $pdf->Text(80, 78, iconv('utf-8','cp1252', number_format(($frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 80, 100, 80);
                                $pdf->Text(10, 84, iconv('utf-8','cp1252', "Autres Commissions : "));
                                $pdf->Text(80, 84, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 86, 100, 86);
                                $pdf->Text(10, 90, iconv('utf-8','cp1252', "Fixe : "));
                                $pdf->Text(80, 90, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 92, 100, 92);
                                $pdf->Text(10, 96, iconv('utf-8','cp1252', "Bonus : "));
                                $pdf->Text(80, 96, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 98, 100, 98);
                                
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(10, 102, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(80, 102, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe ), 0, '.', ' ')." CFA"));
                                $pdf->Line(10, 104, 100, 104);
                                
                               if( !in_array($infoapporteur->Niveau, trans('var.group') )){
                                
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
                                    $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe ), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 128, 100, 128);
                                }else{
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(10, 126, iconv('utf-8','cp1252', "Total brute : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Text(80, 126, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe), 0, '.', ' ')." CFA"));
                                    $pdf->Line(10, 128, 100, 128);
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
                                $pdf->Text(110, 78, iconv('utf-8','cp1252', "Taux Impôt : "));
                                $pdf->Text(180, 78, iconv('utf-8','cp1252', (Fonction::RecupererTaux($infoapporteur->codeCom))." %"));
                                $pdf->Line(110, 80, 197, 80);
                                $pdf->Text(110, 84, iconv('utf-8','cp1252', "Impôt : "));
                                $pdf->Text(180, 84, iconv('utf-8','cp1252', number_format(($frais->aibCalculerGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 86, 197, 86);
                                $pdf->Text(110, 90, iconv('utf-8','cp1252', "Avance payée ce mois : "));
                                $pdf->Text(180, 90, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 92, 197, 92);
                                $pdf->Text(110, 96, iconv('utf-8','cp1252', "Prélèvement : "));
                                $pdf->Text(180, 96, iconv('utf-8','cp1252', number_format((0), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 98, 197, 98);
                                
                                if($frais->compteBloquer < 0){
                                    $pdf->Text(110, 100, iconv('utf-8','cp1252', "Solde à débiter le mois prochain : "));
                                    $pdf->Text(180, 100, iconv('utf-8','cp1252', number_format((abs($frais->compteBloquer)), 0, '.', ' ')." CFA"));
                                }
                                $pdf->Line(110, 104, 197, 104);
                                $pdf->Line(110, 110, 197, 110);
                                $pdf->SetFont("Arial", "B", 8);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 114, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 114, iconv('utf-8','cp1252', number_format(( $frais->aibCalculerGroupe ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 116, 197, 116);
    
                                $pdf->SetFont("Arial", "B", 8);
    
                                // Total sur les actives
                                $pdf->Ln(10);
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 138, iconv('utf-8','cp1252', "Total gains : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 138, iconv('utf-8','cp1252', number_format(( $frais->compteMoisGroupe + $frais->compteEncadrementMoisCalculerGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 140, 197, 140);
                                
    
                                // Total sur les passives
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 147, iconv('utf-8','cp1252', "Total retenues : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 147, iconv('utf-8','cp1252', number_format((  $frais->aibCalculerGroupe ), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 149, 197, 149);
    
                                // Total
                                $pdf->SetTextColor(0,22,96);
                                $pdf->Text(110, 156, iconv('utf-8','cp1252', "Total net  : "));
                                $pdf->SetTextColor(0,0,0);
                                $pdf->Text(180, 156, iconv('utf-8','cp1252', number_format(($frais->compteGroupe), 0, '.', ' ')." CFA"));
                                $pdf->Line(110, 158, 197, 158);
    
    
                                if( in_array($infoapporteur->Niveau, trans('var.group') )){
                                    // Signature Commercial
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(20, 168, iconv('utf-8','cp1252', "Signature DT : "));
                                    $pdf->SetTextColor(0,0,0);
                                    
                                    $datadt = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dt")->where("roles.statut", 0)->first();
                                    
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(20, 188, iconv('utf-8','cp1252', $datadt->nom.' '.$datadt->prenom));
        
                                    // Signature Société
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(150, 168, iconv('utf-8','cp1252', "Signature DG : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $datadg = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dg")->where("roles.statut", 0)->first();
                                    
                                    //$pdf->Image($soc->signature, 150, 170, -150);
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(150, 188, iconv('utf-8','cp1252', $datadg->nom.' '.$datadg->prenom));
                                }
                                else{
                                    // Signature Commercial
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(20, 168, iconv('utf-8','cp1252', "Signature du Conseiller Commercial : "));
                                    $pdf->SetTextColor(0,0,0);
        
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(35, 188, iconv('utf-8','cp1252', $infoapporteur->nomCom.' '.$infoapporteur->prenomCom));
        
                                    // Signature Société
                                    $pdf->SetFont("Arial", "B", 8);
                                    $pdf->SetTextColor(0,22,96);
                                    $pdf->Text(150, 168, iconv('utf-8','cp1252', "Signature de la Société : "));
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Image($soc->signature, 150, 170, -150);
                                    $pdf->SetFont("Arial", "", 6);
                                    $pdf->Text(160, 188, iconv('utf-8','cp1252', $soc->libelleSociete));
                                }
    
                                $pdf->Output('F', $pathd);
                                $fiche_D = $pathd;
                                $lib_fiche_D = $lib;
    
    
                    //////////////////////FIN fiche de paie (DUPLICATA) ////////////////////////////////
    
    
                    //////////////////////Catégorie CONS////////////////////////////////
    
                    if ($infoapporteur->Niveau != "INS" && $infoapporteur->Niveau != "CEQP" && $infoapporteur->Niveau != "RG") {
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
                            ->where('TypeCommission', 'g')
                            ->where("ctrl", 1)
                            ->where("commissions.Statut", date('m-Y'))
                            ->where("contrats.Agent", $infoapporteur->codeCom)->get();
    
                            ///// detail Group
                            $detail_group = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.Taux as comm', 'commissions.Apporteur as Apporteur', 'commissions.Toc as Toc'  )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'g')
                            ->where("commissions.Statut", date('m-Y'))
                            ->where("commissions.Toc", 'like', '%'.$infoapporteur->codeCom.'%')->get();
                                
                            ///// detail Group
                            
                            //dd($detail);
                                 setlocale(LC_TIME,  'french');
    
                                $datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "GROUPE_DETAIL_FICHE_PAIE_".$apporteur->Commercial."_".utf8_encode(strtoupper(strftime('%B %Y')));
                                $path = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFFF();
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
                                $pdf->Text(10, 32, "INFORMATIONS APPORTEUR");
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
                                if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                $pdf->Cell(30,6,'Convention', 1, 0, 'L', 1);
                                else
                                $pdf->Cell(30,6,'Client', 1, 0, 'L', 1);
                                
                                $pdf->Cell(15,6,'Quittance', 1, 0, 'C', 1);
                                $pdf->Cell(12,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
                                $pdf->Cell(15,6,iconv('utf-8','cp1252','Catégorie'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Base de Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(25,6,iconv('utf-8','cp1252','Commission'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','AIB'), 1, 0, 'C', 1);
                                $pdf->Cell(20,6,iconv('utf-8','cp1252','NET A PAYER'), 1, 0, 'C', 1);
                                
                                
                                $sombase_ind = 0;
                                $somcom_ind = 0;
                                $sombase_group = 0;
                                $somcom_group = 0;
                                
                                $sombase = 0;
                                $somcom = 0;
                                
    
                                $pdf->SetFont("Arial", "", 5);
                                $pdf->setFillColor(255,255,255);
    
                                //////////////////////////////////////////////////////// Les données
                                foreach ($detail as $value){ 
                                    $sombase_ind += $value->base;
                                    $somcom_ind += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1); 
                                    if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupConv($value->police), 1, 0, 'L', 1);
                                    else
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
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase_ind, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom_ind, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_ind * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_ind - ($somcom_ind * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                
                                
                                //////// Group
                                if(count($detail_group) != 0){
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
        
                                    foreach ($detail_group as $value){ 
                                        // Vérifie s'il est bien dans Toc avant d'afficher 
                                        $commgroup = 0;
                                        $appexterne_decode = json_decode($value->Toc);
                                        if (in_array($infoapporteur->codeCom, $appexterne_decode)){
                                            $comexterne = json_decode($value->comm);
                                            $commgroup = $comexterne[array_search($infoapporteur->codeCom, $appexterne_decode)];
                                        }
                                        
                                        $sombase_group += $value->base;
                                        $somcom_group += $commgroup;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $infoapporteur->codeCom, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, " ", 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, InterfaceServiceProvider::niveauGroup($value->Apporteur), 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($commgroup, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup - ($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
        
                                    // Sous catégorie Group
                                    $pdf->Ln(6);
                                    $pdf->SetFont("Arial", "", 7);
                                    $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                    $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie  :"), 1, 0, 'R');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase_group, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom_group, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group - ($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                }
                                /////// Fin Group
                                
                                
                                $somcom = $somcom_ind + $somcom_group;
                                $sombase = $sombase_group + $sombase_ind;
                                
                                ///////////////////////////////////////////////////////////
                                
                                 $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
    
                                if( in_array($infoapporteur->Niveau, trans('var.group') )){
                                    
                                    $datadt = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dt")->where("roles.statut", 0)->first();
                                    
                                    $datadg = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dg")->where("roles.statut", 0)->first();
                                    
                                    $pdf->Ln(20);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6,"Signature DT :", 0, 0, 'L');
                                    $pdf->Cell(60,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature DG : "), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C');
        
                                    $pdf->Ln(10);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6," ", 0, 0, 'L');
                                    $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                    //$pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                    $pdf->Ln(10);
        
                                    $pdf->SetFont("Arial", "", 8);
                                    $pdf->Cell(50,6, $datadt->nom.' '.$datadt->prenom, 0, 0, 'C');
                                    $pdf->Cell(90,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252', $datadg->nom.' '.$datadg->prenom), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                }
    
                                else{
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
                                }
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
                                           'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.Apporteur as Apporteur',  'commissions.MontantConseiller as comm', 'commissions.Toc as Toc' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where('TypeCommission', 'g')
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ////->where("commissions.moiscalculer", date('m-Y'))
                                //->where("commissions.moiscalculer", date('m-Y'))
                                ->where("contrats.Agent", $infoapporteur->codeCom)->get();
                                
                                ///// detail Group
                                $detail_group = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                           'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.Taux as comm', 'commissions.Apporteur as Apporteur' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where('TypeCommission', 'g')
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where("commissions.Toc", 'like', '%'.$infoapporteur->codeCom.'%')->get();
                                
                                $sombase_ind = 0;
                                $somcom_ind = 0;
                                $sombase_group = 0;
                                $somcom_group = 0;
                                    
                                ///// detail Group
                                
                                
                                //dd($detail);
                                     setlocale(LC_TIME,  'french');
    
                                //$datelettre = utf8_encode(strtoupper(strftime('%B %Y'))); // MODE TEST
                                $datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "GROUPE_DETAIL_FICHE_PAIE_DUPLICATA_".$apporteur->Commercial."_".utf8_encode(strtoupper(strftime('%B %Y')));
                                $pathd = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFFF();
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
                                $pdf->Text(10, 32, "INFORMATIONS APPORTEUR");
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
                                if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                $pdf->Cell(30,6,'Convention', 1, 0, 'L', 1);
                                else
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
                                    $sombase_ind += $value->base;
                                    $somcom_ind += $value->comm;
                                    $pdf->Ln(6);
                                    $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1); 
                                    if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupConv($value->police), 1, 0, 'L', 1);
                                    else
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
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase_ind, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom_ind, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_ind * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_ind - ($somcom_ind * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                
                                
                                //////// Group
                                if(count($detail_group) != 0){
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
        
                                    foreach ($detail_group as $value){ 
                                        // Vérifie s'il est bien dans Toc avant d'afficher 
                                        $commgroup = 0;
                                        $appexterne_decode = json_decode($value->Toc);
                                        if (in_array($infoapporteur->codeCom, $appexterne_decode)){
                                            $comexterne = json_decode($value->comm);
                                            $commgroup = $comexterne[array_search($infoapporteur->codeCom, $appexterne_decode)];
                                        }
                                        
                                        $sombase_group += $value->base;
                                        $somcom_group += $commgroup;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $infoapporteur->codeCom, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, " ", 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, InterfaceServiceProvider::niveauGroup($value->Apporteur), 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($commgroup, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup - ($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
        
                                    // Sous catégorie Group
                                    $pdf->Ln(6);
                                    $pdf->SetFont("Arial", "", 7);
                                    $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                    $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie  :"), 1, 0, 'R');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase_group, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom_group, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group - ($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                }
                                
                                $somcom = $somcom_ind + $somcom_group;
                                $sombase = $sombase_group + $sombase_ind;
                                
                                
                                /////// Fin Group
                                
                                
                                
                                ///////////////////////////////////////////////////////////
                                
    
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                if( in_array($infoapporteur->Niveau, trans('var.group') )){
                                    
                                    $datadt = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dt")->where("roles.statut", 0)->first();
                                    
                                    $datadg = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dg")->where("roles.statut", 0)->first();
                                    
                                    $pdf->Ln(20);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6,"Signature DT :", 0, 0, 'L');
                                    $pdf->Cell(60,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature DG : "), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C');
        
                                    $pdf->Ln(10);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6," ", 0, 0, 'L');
                                    $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                    //$pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                    $pdf->Ln(10);
        
                                    $pdf->SetFont("Arial", "", 8);
                                    $pdf->Cell(70,6, $datadt->nom.' '.$datadt->prenom, 0, 0, 'C');
                                    $pdf->Cell(90,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252', $datadg->nom.' '.$datadg->prenom), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                }
    
                                else{
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
                                }
                                // Sauvegarde du pdf
                                $pdf->Output('F', $pathd);
                                $detail_D = $pathd;
                                $lib_detail_D = $lib;
    
    
                                ////////////////////FIN (DUPLICATA) ///////////////////
    
                    }
    
                    //////////////////////FIN Catégorie CONS////////////////////////////////
    
                    //////////////////////Catégorie CEQP ou INS ////////////////////////////////
    
                        if ($infoapporteur->Niveau == "CEQP" || $infoapporteur->Niveau == "INS" || $infoapporteur->Niveau == "RG") {
                            $niv = "";
                            $soc = DB::table('societes')->where("id", $infoapporteur->Societe)->first();
                            // Ce que lui même à reçu sur  ses affaires
                            $detaillui = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                       'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.MontantConseiller as comm', 'commissions.Toc as Toc' )
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('TypeCommission', 'g')
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 1)
                            ->where("commissions.Statut", date('m-Y'))
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
                                ->where('TypeCommission', 'g')
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commerciauxes.codeEquipe', $codeEquipe)->get(); 
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
                                ->where('TypeCommission', 'g')
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commerciauxes.codeInspection', $codeIns)->get();
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
                                ->where('TypeCommission', 'g')
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commerciauxes.codeRegion', $codeRG)->get();
                            }
                            
                            ///// detail Group
                                $detail_group = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                           'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.Apporteur as Apporteur', 'commissions.Taux as comm', 'commissions.Toc as Toc' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('confirmertresorerie', "oui")
                                ->where('TypeCommission', 'g')
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where("commissions.Toc", 'like', '%'.$infoapporteur->codeCom.'%')->get();
                                
                                $sombase_group = 0;
                                $somcom_group = 0;
                                    
                                ///// detail Group
    
                                setlocale(LC_TIME,  'french');
                                //$datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
                                $datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "GROUPE_DETAIL_FICHE_PAIE_".$apporteur->Commercial."_".utf8_encode(strtoupper(strftime('%B %Y')));
                                $path = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFFF();
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
                                $pdf->Text(10, 32, "INFORMATIONS APPORTEUR");
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
                                if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                $pdf->Cell(30,6,'Convention', 1, 0, 'L', 1);
                                else
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
                                    if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                    $pdf->Cell(30,6, InterfaceServiceProvider::RecupConv($value->police), 1, 0, 'L', 1);    
                                    else
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
    
                                // Sous catégorie 2
                                $pdf->Ln(6);
                                $pdf->SetFont("Arial", "", 7);
                                $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie :"), 1, 0, 'R');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase2, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom2, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom2 - ($somcom2 * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
    
                                
                                //////// Group
                                if(count($detail_group) != 0){
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
        
                                    foreach ($detail_group as $value){ 
                                        // Vérifie s'il est bien dans Toc avant d'afficher 
                                        $commgroup = 0;
                                        $appexterne_decode = json_decode($value->Toc);
                                        if (in_array($infoapporteur->codeCom, $appexterne_decode)){
                                            $comexterne = json_decode($value->comm);
                                            $commgroup = $comexterne[array_search($infoapporteur->codeCom, $appexterne_decode)];
                                        }
                                        
                                        $sombase_group += $value->base;
                                        $somcom_group += $commgroup;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $infoapporteur->codeCom, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, " ", 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, InterfaceServiceProvider::niveauGroup($value->Apporteur), 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($commgroup, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup - ($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
        
                                    // Sous catégorie Group
                                    $pdf->Ln(6);
                                    $pdf->SetFont("Arial", "", 7);
                                    $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                    $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie  :"), 1, 0, 'R');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase_group, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom_group, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group - ($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                }
                                
                                
                                ////////////////////////////////////////////////////////////////////////////////////
    
                                $somcom = $somcom1 + $somcom2 + $somcom_group;
                                $sombase = $sombase1 + $sombase2 + $sombase_group;
                                
                                
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                if( in_array($infoapporteur->Niveau, trans('var.group') )){
                                    
                                    $datadt = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dt")->where("roles.statut", 0)->first();
                                    
                                    $datadg = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dg")->where("roles.statut", 0)->first();
                                    
                                    $pdf->Ln(20);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6,"Signature DT :", 0, 0, 'L');
                                    $pdf->Cell(60,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature DG : "), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C');
        
                                    $pdf->Ln(10);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6," ", 0, 0, 'L');
                                    $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                    $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                    $pdf->Ln(10);
        
                                    $pdf->SetFont("Arial", "", 8);
                                    $pdf->Cell(70,6, $datadt->nom.' '.$datadt->prenom, 0, 0, 'C');
                                    $pdf->Cell(90,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252', $datadg->nom.' '.$datadg->prenom), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                }
    
                                else{
                                
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
                                }
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
                            ->where('TypeCommission', 'g')
                            ->where("ctrl", 1)
                            ->where("commissions.Statut", date('m-Y'))
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
                                ->where('TypeCommission', 'g')
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commerciauxes.codeEquipe', $codeEquipe)->get(); 
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
                                ->where('TypeCommission', 'g')
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commerciauxes.codeInspection', $codeIns)->get();
                            }
                            if ($infoapporteur->Niveau == "RG") {
                                $niv = "RG";
                                // Ce qu'il a gagner en tant chef d'équipe
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
                                ->where('TypeCommission', 'g')
                                ->where("commissions.Statut", date('m-Y'))
                                ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                                ->where('commerciauxes.Niveau', 'CONS')
                                ->where('commerciauxes.codeRegion', $codeRG)->get();
                            }
                            
                            ///// detail Group
                                $detail_group = DB::table('commissions')
                                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->select('contrats.police as police', 'contrats.Produit as produit', 'commissions.NumQuittance as Quittance', 
                                           'commissions.DateProduction as periode', 'commissions.BaseCommission as base', 'commissions.Apporteur as Apporteur', 'commissions.Taux as comm', 'commissions.Toc as Toc' )
                                ->where("statutcalculer", "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('TypeCommission', 'g')
                                ->where('confirmertresorerie', "oui")
                                ->where("ctrl", 1)
                                ->where("commissions.Statut", date('m-Y'))
                                ->where("commissions.Toc", 'like', '%'.$infoapporteur->codeCom.'%')->get();
                                
                                $sombase_group = 0;
                                $somcom_group = 0;
    
                                setlocale(LC_TIME,  'french');
                                $datelettre = utf8_encode(strtoupper(strftime('%B %Y')));
    
                                // Création du PDF
    
                                $titre = "ETAT DETAILLE DES COMMISSIONS ";
                                $lib = "GROUPE_DETAIL_FICHE_PAIE_DUPLICATA_".$apporteur->Commercial."_".utf8_encode(strtoupper(strftime('%B %Y')));
                                $pathd = "document/commission/".$lib.".pdf";
    
                                //create pdf document
                                //$pdf = app('Fpdf');
                                $pdf = new PDFFF();
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
                                $pdf->Text(10, 32, "INFORMATIONS APPORTEUR");
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
                                if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                    $pdf->Cell(30,6,'Convention', 1, 0, 'L', 1);
                                else
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
                                    
                                    if( in_array($infoapporteur->Niveau, trans('var.group') ))
                                        $pdf->Cell(30,6, InterfaceServiceProvider::RecupConv($value->police), 1, 0, 'L', 1);
                                    else
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
    
                                //////// Group
                                if(count($detail_group) != 0){
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
        
                                    foreach ($detail_group as $value){ 
                                        // Vérifie s'il est bien dans Toc avant d'afficher 
                                        $commgroup = 0;
                                        $appexterne_decode = json_decode($value->Toc);
                                        if (in_array($infoapporteur->codeCom, $appexterne_decode)){
                                            $comexterne = json_decode($value->comm);
                                            $commgroup = $comexterne[array_search($infoapporteur->codeCom, $appexterne_decode)];
                                        }
                                        
                                        $sombase_group += $value->base;
                                        $somcom_group += $commgroup;
                                        $pdf->Ln(6);
                                        $pdf->Cell(15,6, $value->police, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, $infoapporteur->codeCom, 1, 0, 'C', 1);
                                        $pdf->Cell(30,6, " ", 1, 0, 'L', 1);
                                        $pdf->Cell(15,6, $value->Quittance, 1, 0, 'C', 1);
                                        $pdf->Cell(12,6, $value->periode, 1, 0, 'C', 1);
                                        $pdf->Cell(15,6, InterfaceServiceProvider::niveauGroup($value->Apporteur), 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($value->base, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(25,6, number_format($commgroup, 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                        $pdf->Cell(20,6, number_format(($commgroup - ($commgroup * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA", 1, 0, 'C', 1);
                                    }
        
                                    // Sous catégorie Group
                                    $pdf->Ln(6);
                                    $pdf->SetFont("Arial", "", 7);
                                    $pdf->Cell(60,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                                    $pdf->Cell(42,6,iconv('utf-8','cp1252', "Sous Total Catégorie  :"), 1, 0, 'R');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($sombase_group, 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format($somcom_group, 0, '.', ' ')." CFA" ), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                    $pdf->Cell(20,6,iconv('utf-8','cp1252', number_format(($somcom_group - ($somcom_group * Fonction::RecupererTaux($infoapporteur->codeCom) / 100)), 0, '.', ' ')." CFA"), 1, 0, 'C');
                                }
                                
                                
                                ////////////////////////////////////////////////////////////////////////////////////
    
                                $somcom = $somcom1 + $somcom2 + $somcom_group;
                                $sombase = $sombase1 + $sombase2 + $sombase_group;
                                
                                
                                $pdf->Ln(16);
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(107,6,"", 0, 0, 'C');
                                $pdf->Cell(35,6,iconv('utf-8','cp1252',"Montant des commissions "), 'B', 0, 'L');
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(40,6,number_format(($somcom), 0, '.', ' ')." CFA ", 'B', 0, 'R');
    
                                if( in_array($infoapporteur->Niveau, trans('var.group') )){
                                    
                                    $datadt = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dt")->where("roles.statut", 0)->first();
                                    
                                    $datadg = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->where("roles.code", "dg")->where("roles.statut", 0)->first();
                                    
                                    $pdf->Ln(20);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6,"Signature DT :", 0, 0, 'L');
                                    $pdf->Cell(60,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252',"Signature DG : "), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C');
        
                                    $pdf->Ln(10);
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(70,6," ", 0, 0, 'L');
                                    $pdf->Cell(67,6,iconv('utf-8','cp1252',""), 0, 0, 'L');
                                    $pdf->Image($soc->signature, $pdf->GetX(), $pdf->GetY(), 30);
                                    $pdf->Ln(10);
        
                                    $pdf->SetFont("Arial", "", 8);
                                    $pdf->Cell(70,6, $datadt->nom.' '.$datadt->prenom, 0, 0, 'C');
                                    $pdf->Cell(90,6,"", 0, 0, 'L');
                                    $pdf->Cell(70,6,iconv('utf-8','cp1252', $datadg->nom.' '.$datadg->prenom), 0, 0, 'L');
                                    $pdf->Cell(60,6," ", 0, 0, 'C'); 
                                }
    
                                else{
                                
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
                                }
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
                    $addDocD->periode = date('m-Y'); //utf8_encode(strtoupper(strftime('%B %Y')));
                    $addDocD->Agent = $apporteur->Commercial;
                    $addDocD->save();
                    
                }
            } 

           
            //  Vidée tous les comptes apporteurs du mois/
            
            foreach ($list as $apporteur) {
                if(!isset(DB::table('tracecomptes')->where('Commercial', $apporteur->Commercial)->where('moiscalculer', date('m-Y'))->first()->id)){
                    // mettre à jour la table compte
                    $occurence = json_encode(DB::table('compteagents')->where('Agent', $apporteur->Commercial)->first());
                    $addt = new Tracecompte();
                    $addt->contenu = "Compte réinitialisé : ".$occurence;
                    //$addt->moiscalculer = date('m-Y');
                    $addt->moiscalculer = date('m-Y');
                    $addt->Commercial = $apporteur->Commercial;
                    $addt->save();
                    DB::table('compteagents')->where('Agent', $apporteur->Commercial)->update([
                        'compteMoisGroupe' => 0,
                        'aibCalculerGroupe' => 0,
                        'compteNetapayerGroupe' => 0,
                        'compteEncadrementMoisCalculerGroupe' => 0,
                        'compteGroupe' => 0,
                        'statueValideG' => 0,
                        'statutEngG' => 0
                    ]);
                }
            }
            
            
            // Enregistrer dans la table Signataire
                $addSignataire = new Signataire();
                $addSignataire->idSignataire = session('utilisateur')->idUser;
                $addSignataire->CodeCommission = "allgroupe";
                $addSignataire->Nom = session('utilisateur')->nom.' '.session('utilisateur')->prenom;
                $addSignataire->pathSignataire = session('utilisateur')->pathSignature;
                $addSignataire->DateCalculer = date('m-Y');
                $addSignataire->RoleSignataire = session('utilisateur')->Role;
                $addSignataire->save();
                
            // Validé tous les commissions de ce mois
            $commission_v = DB::table('commissions')
                ->where("ctrl", 1)
                ->where("commissions.Statut", date('m-Y'))
                ->where('TypeCommission', 'g')
                ->get();

            foreach ($commission_v as $comm) {
                DB::table('commissions')->where('NumCommission', $comm->NumCommission)->update([
                    "ctrl" => 2
                ]);
            }
            
            
            $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mail);
            }
            $allSup = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "cfvi")->get();
            foreach ($allSup as $value) {
                array_push($mails, $value->mail);
            }
            array_push($mails, "emmanueldjidagbagba@gmail.com");
            
            //SendMail::sendnotificationfin("rogerkpovi@gmail.com", $mails, "Commissions. NSIA VIE ASSURANCES", []);
            
            flash("Vous avez confirmé les commissions groupe.");
            TraceController::setTrace(
                "Vous avez confirmé les commissions groupe de ce mois.",
                session("utilisateur")->idUser);
            return "Vous avez confirmé les commissions groupe.";//Back(); 
        }
    }
    
    public function getcommissiondetail(){
        
        $apporteur = DB::table('commerciauxes')->where("commerciauxes.codeCom", trim(request('id')))->first();
        $compte = DB::table('compteagents')->where('Agent', trim(request('id')))->first();
        
        //$apporteur = DB::table('tracecomptes')->where('id', trim(request('id')))->first();
	    //$comp = InterfaceServiceProvider::RecupCompteAncien($apporteur->Commercial, $apporteur->moiscalculer); 
	    $net_temp = ($compte->compteNetapayerGroupe + $compte->aibCalculerGroupe);
        $taux = 0;
        if($net_temp != 0)
	       $taux = round(($compte->aibCalculerGroupe / $net_temp) * 100);
	  
	    $list = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('ctrl', 1)
                    ->where('TypeCommission', 'g')
                    ->where('Apporteur', $apporteur->codeCom)
                    ->where('commissions.Statut', $compte->MoisCalculer);
        $niveau = "CONS";
        $detailCom ="";
        
        $detail_group = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'g')
                            ->where("commissions.Statut", date('m-Y'))
                            ->where("commissions.Toc", 'like', '%'.trim(request('id')).'%')->get();
        
        $nivcommer = $apporteur->Niveau;
            
        if ($nivcommer == "CEQP") {
            $niveau = "CEQP";
            // Ce qu'il a gagner en tant chef d'équipe
            $codeEquipe = DB::table('commerciauxes')->where('codeCom', $apporteur->codeCom)->first()->codeEquipe;
            $detailCom = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'g')
                            ->where("commissions.Statut", $compte->MoisCalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeEquipe', $codeEquipe)->get(); 
        }
        if ($nivcommer == "INS") {
            $niveau = "INS";
            
                            $codeIns = DB::table('commerciauxes')->where('codeCom', $apporteur->codeCom)->first()->codeInspection;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'g')
                            ->where("commissions.Statut", $compte->MoisCalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeInspection', $codeIns)->get();
                        }
        if ($nivcommer == "RG") {
                            $niveau = "RG";
                            $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->codeCom)->first()->codeRegion;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'g')
                            ->where("commissions.Statut", $compte->MoisCalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeRegion', $codeRG)->get();
                        }
                        
       $nomapp = $apporteur->nomCom.' '.$apporteur->prenomCom;
	   setlocale(LC_ALL, 'fr_FR', 'fra_FRA');
	   $mois = strftime('%B %Y', strtotime(implode('-',array_reverse  (explode('-',$compte->MoisCalculer)))));
	   
       $list = $list->paginate(4000);
	   return view("commissiongroupe.listdetailcom", compact('apporteur', 'compte', 'detail_group', 'list', 'taux', 'nomapp', 'mois', 'niveau', 'detailCom'));
    }
}