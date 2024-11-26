<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Import\ImportExcel;
use App\Http\Model\Commission;
use App\Http\FonctionControllers\Fonction;
use App\Http\Model\Hierarchie;
use App\Http\Model\Compteagent;
use App\Http\Model\Contrat;
use App\Http\Model\Client;
use App\Http\Model\Commerciaux;
use DB;
use App\Http\Model\TauxNiveau;
use App\Http\Model\Trace;
use Illuminate\Support\Collection;
use App\Exports\ExportTraitement;
use App\Exports\Export;


class ImporterCommissionController extends Controller
{
	
    public function __construct()
    {
		parent::__construct();
    } 
    
    public function importerauto()
    {
		dd(view()->shared('periode'));
        dd(DB::connection('mysql2')->table('users')->get());
    }
    
    public static function CheckCodeInspecteur($inspecteur)
    {
        $verif = DB::table('hierarchies')->where('managerH', $inspecteur)->whereIn("structureH", trans('var.ins'))->first();
        if(isset($verif->codeH)) return $verif->codeH; else return 0;
    }

    public function importercommission(Request $request)
    {
        // Importer commission
        if ($request->hasFile('comm')) {
            $referenceNote = "Importer-".date('ymdhis');
            $namefile = $referenceNote.".xlsx";
            $upload = "document/upload/";
            $request->file('comm')->move($upload, $namefile);
               
            $path = $upload.$namefile;

            $tab = Excel::toArray( new ImportExcel, $path);
            $commission = $tab[0];
            //dd($commission);
            for ($i=1; $i < count($commission); $i++) { 
                $comm = $commission[$i];
				$num_commission = $comm[0];
                $num_agent = $comm[1];
                $structure = $comm[2]; // code d'inspection
                $niveau = $comm[3]; // Niveau par défaut ici sera CONS
                $police = $comm[4]; // numéro contrat
                $num_produit = $comm[5]; // numéro du produit
                $num_quittance = $comm[6]; // quittance
                $date_creation = ((strlen($comm[7])!= 8)?
                    Fonction::ChangeFormatDate(date('d/m/Y', ($comm[7] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate($comm[7]));
                $montant_commission = $comm[8];
                $date_production = $comm[9];
                $garantie = $comm[10];
                $base_commission = $comm[11];
                $nom_total = $comm[12]; // nom des inspections
                $nom_agent = $comm[13];
                $releve = $comm[14];
                $index = $comm[15];
                $DateDebutQuittance = ((strlen($comm[16])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($comm[16] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($comm[16]));
                $DateFinQuittance = ((strlen($comm[17])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($comm[17] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($comm[17]));
                $etat = $comm[18];
                $numcetat = $comm[19];
                $codeetat = $comm[20];
                
                if (trim($niveau) != "INSP" && trim($niveau) != "CEQP" && ($index == "S" || $index == "A")) {

                    // Créer une inspection en utilisant le code de structure
                    Fonction::saveStructure($structure, $nom_total);
                    
                    
                    if(($numcetat == 9 || $etat == "Annulée Payée") && $montant_commission < 0)
                            if($base_commission >= 0)
                                $base_commission = (-1 * $base_commission);

                    $numCom = Commission::where("NumCommission", $num_commission)->first();
                    

                    if(isset($numCom->NumCommission)){
                        
                        if(substr($numCom->created_at, 0, 10) == date('Y-m-d')){
                            
                            //dd(strpos($numCom->Garantie, trim($garantie)) === false);
                            
                            if(strpos($numCom->Garantie, trim($garantie)) === false){
                                $newBaseCom = $numCom->BaseCommission + $base_commission;
                                $newmontSunshine = $numCom->MontantSunShine + $montant_commission;
        
                                Commission::where("NumCommission", $num_commission)->update([
                                    "BaseCommission" => $newBaseCom,
                                    "MontantSunShine" => $newmontSunshine,
                                    "Garantie" => $numCom->Garantie.", ".$garantie
                                ]);
                            }
                        }
                    }else{
                        if(!isset(Commission::where("NumQuittance", $num_quittance)->first()->NumCommission)){
                            $add = new Commission();
                            $add->NumCommission = $num_commission;
                            $add->NumPolice = $police;
                            $add->DateCreation = $date_creation;
                            $add->Garantie = $garantie;
                            $add->BaseCommission = $base_commission;
                            $add->NumReleve = $releve;
                            $add->NumQuittance = $num_quittance;
                            $add->DateDebutQuittance = $DateDebutQuittance;
                            $add->DateFinQuittance = $DateFinQuittance;
                            $add->IndexQuittance = $index;
                            $add->DateProduction = $date_production;
                            $add->Apporteur = $num_agent;
                            $add->ncom = 0;
                            $add->Etat = $etat;
                            $add->ctrl = 0;
                            $add->Statut = view()->shared('periode');
                            $add->numeroetat = $numcetat;
                            $add->codeetat = $codeetat;
                            $add->TypeCommission = 'i';
                            $add->MontantSunShine = $montant_commission;
                            $add->moiscalculer = substr($date_creation, 3, 9);
                            $add->save();
                        }
                    }
                }
                // Les variables tels que agent, produit, nom agent ne pas utiliser ici et provienne de police. Police qui n'est d'autre que la clé de la table contrat.
            }
            SendMail::sendnotification("emmanueldjidagbagba@gmail.com", [], "Importation des commissions individuelle avec succès.", [], "i");
			SendMail::sendnotification("roger.kpovihouede@nsiaassurances.com", [], "Importation des commissions individuelle avec succès.", [], "i");
            flash("Fichier Commission importé avec succès.");
        }else{
            flash("Pas de fichier Commission.")->error();
        }

        // Importer commission hors
        if ($request->hasFile('commhors')) {
            $referenceNote = "ImporterCommissionHorsSunShine-".date('ymdhis');
            $namefile = $referenceNote.".xlsx";
            $upload = "document/upload/";
            $request->file('commhors')->move($upload, $namefile);
			$namefileimporte = $request->file('commhors')->getClientOriginalName();
               
            $path = $upload.$namefile;

            $tab = Excel::toArray( new ImportExcel, $path);
            $commission = $tab[0];
            
            for ($i=1; $i < count($commission); $i++) { 
                $comm = $commission[$i];
                $num_commission = Fonction::genererNumCommission();
                $num_agent = $comm[0];
                $structure = $comm[1]; // code d'inspection
                $niveau = $comm[2]; // Niveau par défaut ici sera CONS
                $police = $comm[3]; // numéro contrat
                $num_produit = $comm[4]; // numéro du produit
                $num_quittance = $comm[5]; // quittance
                $date_production = $comm[6];
                $base_commission = $comm[7];
                $index = $comm[8];
                $DateDebutQuittance = ((strlen($comm[9])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($comm[9] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($comm[9]));
                $DateFinQuittance = ((strlen($comm[10])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($comm[10] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($comm[10]));

                if ($index == "S") {

                    // Créer une inspection en utilisant le code de structure
                    if($structure != "" && $structure != null)
                    Fonction::saveStructure($structure, "");

                    if(isset(Commission::where("NumCommission", $num_commission)->first()->NumCommission)){
                        $newBaseCom = Commission::where("NumCommission", $num_commission)->first()->BaseCommission + $base_commission;
                        $newmontSunshine = Commission::where("NumCommission", $num_commission)->first()->MontantSunShine + $montant_commission;

                        Commission::where("NumCommission", $num_commission)->update([
                            "BaseCommission" => $newBaseCom,
                            "MontantSunShine" => $newmontSunshine
                        ]);
                    }else{
                        if(!isset(Commission::where("NumQuittance", $num_quittance)->first()->NumCommission)){
                            $add = new Commission();
                            $add->Apporteur = $num_agent;
                            $add->NumCommission = $num_commission;
                            $add->NumPolice = $police;
                            $add->DateCreation = date('d-m-Y');
                            $add->BaseCommission = $base_commission;
                            $add->NumQuittance = $num_quittance;
                            $add->DateDebutQuittance = $DateDebutQuittance;
                            $add->DateFinQuittance = $DateFinQuittance;
                            $add->IndexQuittance = $index;
                            $add->DateProduction = $date_production;
                            $add->ncom = 0;
                            $add->ctrl = 0;
                            $add->TypeCommission = 'i';
                            $add->Statut = view()->shared('periode');
                            $add->moiscalculer = view()->shared('periode');
                            $add->save();
                        }
                    }
                }
                // Les variables tels que agent, produit, nom agent ne pas utiliser ici et provienne de police. Police qui n'est d'autre que la clé de la table contrat.
            }
            SendMail::sendnotification("emmanueldjidagbagba@gmail.com", [], "Importation des commissions hors sunshine avec succès.".$namefileimporte, [], "i");
			SendMail::sendnotification("roger.kpovihouede@nsiaassurances.com", [], "Importation des commissions hors sunshine avec succès.", [], "i");
            flash("Fichier Commission hors Sunshine importé avec succès.");
        }else{
            flash("Pas de fichier pour hors Commission.")->error();
        }

        // Importer contrat

        if ($request->hasFile('contrat')) {
            $referenceNote = "Importer-Contrat-".date('ymdhis');
            $namefile = $referenceNote.".xlsx";
            $upload = "document/upload/";
            $request->file('contrat')->move($upload, $namefile);
               
            $pathc = $upload.$namefile;

            $tabc = Excel::toArray( new ImportExcel, $pathc);
            $contrat = $tabc[0];
            $i=0;
            
            //dd(count($contrat));
            $message = "";
            for ($i=1; $i < count($contrat); $i++) { 
                $contr = $contrat[$i];
                $contr_police = $contr[0];
                $contr_produit = $contr[1];
                $contr_statut = $contr[3]; // 
                $contr_nomassur = $contr[5]; 
                $contr_prenomassur = $contr[6];
                $contr_num_assur = $contr[7]; 
                $contr_payeur = $contr[8]; 
                
                $contr_dateffetdbut = ((strlen($contr[9])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[9] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($contr[9]));
                $contr_dateffetfin = ((strlen($contr[10])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[10] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($contr[10])); 
                /*
                $contr_dateffetdbut = ((strlen($contr[9])!= 10)?
                    Fonction::ChangeFormatDateFormat(date('d/m/Y', ($contr[9] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDateFormat($contr[9]));
                $contr_dateffetfin = ((strlen($contr[10])!= 10)?
                    Fonction::ChangeFormatDateFormat(date('d/m/Y', ($contr[10] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDateFormat($contr[10])); */
                    
                $contr_agent = $contr[11];
                $contr_nomagent = $contr[12];
                $contr_codeinspection = $contr[14];
                $contr_typeinspection = $contr[15];
                $contr_fractionnement = $contr[16];
				$contr_nais = $contr[17];
                $contr_mail = $contr[18];
                $contr_tel = $contr[19];
                $contr_conv= $contr[20];
                $contr_dateecheance = $contr_dateffetfin;
                
                //dd($contr_dateffetdbut);

                // récuperer le libelle à partir du sigle fractionnement
                $codeFrac = Fonction::LibelleFractionnement($contr_fractionnement);
				
				if($contr_conv == "1000847")
                    $codeFrac = "UNIQUE";

                Fonction::saveClient($contr_nomassur, $contr_prenomassur, $contr_num_assur, $contr_payeur, $contr_tel, $contr_mail, $contr_nais );

                // Vérification de l'unicité de la police
                if(Fonction::VérificationPolice($contr_police)){

                    // Créer une inspection en utilisant le code de structure
                    //Fonction::updatetypeStructure($contr_codeinspection, $contr_typeinspection);

                    // Nourri la table Produit 
                   // Fonction::saveProduit($contr_produit, $contr[2]);

                    // Enregistrer le client
                    Fonction::saveClient($contr_nomassur, $contr_prenomassur, $contr_num_assur, $contr_payeur, $contr_tel, $contr_mail, $contr_nais );

                    // Vérification pour éliminer les valeurs null remarquer dans le fichier
                    if($contr_agent != "NULL" && $contr_produit != "NULL" && $contr_num_assur != "NULL" && $contr_agent != "" && $contr_produit != "" && $contr_num_assur != "")
                    {
                        $addContrat = new Contrat();
                        $addContrat->police = $contr_police;
                        $addContrat->Produit = $contr_produit;
                        $addContrat->Client = $contr_num_assur;
                        $addContrat->Agent = $contr_agent;
                        $addContrat->statutSunshine = $contr_statut;
                        $addContrat->DateDebutEffet = $contr_dateffetdbut;
                        $addContrat->DateFinEffet = $contr_dateffetfin;
                        $addContrat->DateEcheance = $contr_dateecheance;
                        $addContrat->fractionnement = $codeFrac;
						$addContrat->conv = $contr_conv;
                        $addContrat->user_action = 1;
                        $addContrat->save();
                    }
                // Les variables tels que agent, produit, nom agent ne pas utiliser ici et provienne de police. 
                //Police qui n'est d'autre que la clé de la table contrat.
                }else
                {
                    // Vérification des informations du contrat existant
                    //$message = "";
                    $ecap = Contrat::where('police', $contr_police)
                    ->where('Produit', $contr_produit)
                    ->where('Client', "!=", $contr_num_assur)
                    ->where('Agent', "!=", $contr_agent)
                    ->where('DateDebutEffet', "!=", $contr_dateffetdbut)
                    ->where('DateFinEffet', "!=", $contr_dateffetfin)
                    ->where('DateEcheance', "!=", $contr_dateecheance)
                    ->first();
                    if(isset($ecap->police)){
                        $message .= "Ce contrat existe déjà, mais les informations sont différentes par rapport au nouveau. Veuillez vérifier et mettre à jour les informations depuis l'interface contrat.";
                        $anc = Contrat::where('police', $contr_police)->first();
                        $message .= "Ancien [ Police : ".$anc->police." Code Produit : ".$anc->Produit." Code Client : ".$anc->Client." Code Agent : ".$anc->Agent." 
                        Date début Efet : ".$anc->DateDebutEffet." Date fin Effet : ".$anc->DateFinEffet." Date Echeance : ".$anc->DateEcheance." ] ";
                        
                        $message .= ". Nouveau [ Police : ".$contr_police." Code Produit : ".$contr_produit." Code Client : ".$contr_num_assur." 
                        Code Agent : ".$contr_agent." Date début Efet : ".$contr_dateffetdbut." Date fin Effet : ".$contr_dateffetfin." Date Echeance : ".$contr_dateecheance." ] ";
                    } 
                    
                    
                    // Mettre à jour Contrat 
                    Contrat::where('police', $contr_police)->update([
						"conv" => $contr_conv,
                        "fractionnement" => $codeFrac,
                        "statutSunshine" => $contr_statut
                        //"DateDebutEffet" => $contr_dateffetdbut,
                        //"DateFinEffet" => $contr_dateffetfin ,
                        //"DateEcheance" => $contr_dateecheance
                    ]);
                }
            }
            
            // Envoi mail de notification
            $allAdmin = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mailZ')->where("roles.code", "admin")->get();
            $mails = array();
            foreach ($allAdmin as $value) {
                array_push($mails, $value->mailZ);
            }   
            //flash($message);
            if($message != ""){
                SendMail::sendnotificationErreurContrat($mails, "Erreur lors de l'imporation des contrats de ce mois !", $message);
            }
            SendMail::sendnotification("emmanueldjidagbagba@gmail.com", [], "Importation des contrats avec succès.", [], "i");
			SendMail::sendnotification("roger.kpovihouede@nsiaassurances.com", [], "Importation des contrats avec succès.", [], "i");
            flash("Fichier contrat importé avec succès.");
        }else{
            flash("Pas de fichier Contrat.")->error();
        }
        
        // Importer commerciaux
         if ($request->hasFile('commerciaux')) {
            $ext  = $request->file('commerciaux')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            //$tabl = ;

            if(in_array($ext,['xlsx','xls'])){
                $reference = "TRAITEMENT-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('commerciaux')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                for ($i=1; $i < count($commerciaux); $i++) {
                    $app = $commerciaux[$i];
                    $payeur = $app[0];
                    $contact = $app[1];
                    $mails = $app[2];
                    
                    Client::where("idClient", $payeur)->update(["contact"=> $contact, "mails"=> $mails]);
                }
                
                /*
                Session()->put('maj', "");
                $j = 0;
                $tabl[$j]["police"] = "";
                    $tabl[$j]["libelle"] = "";
                    $tabl[$j]["montant"] = "";
                    $tabl[$j]["rejet"] = "";
                    $tabl[$j]["banque"] = "";
                    $tabl[$j]["apporteur"] = "";
                    $tabl[$j]["nom"] = "";
                    $tabl[$j]["equipe"] = "";
                    $tabl[$j]["nomequipe"] = "";
                    $tabl[$j]["ins"] = "";
                    $tabl[$j]["nomins"] = "";
                    $tabl[$j]["rg"] = "";
                    $tabl[$j]["nomrg"] = "";
                    $tabl[$j]["cd"] = "";
                    $tabl[$j]["nomcd"] = "";
                    $j++;
                for ($i=1; $i < count($commerciaux); $i++) { 
                 
                    $app = $commerciaux[$i];
                    $police = $app[0];
                    $libelle = $app[1];
                    $montant = $app[2];
                    $rejet  = $app[3];
                    $banque = $app[4];
                    
                    $contrat = Contrat::where('police', $police)->first();
                    
                    //dd($contrat);
                    
                    if(isset($contrat->Agent)){
                        
                        $com = CommerciauxController::getinfoscommerciaux($contrat->Agent);
                        
                        $tabl[$j]["police"] = $police;
                        $tabl[$j]["libelle"] = $libelle;
                        $tabl[$j]["montant"] = $montant;
                        $tabl[$j]["rejet"] = $rejet;
                        $tabl[$j]["banque"] = $banque;
                        $tabl[$j]["apporteur"] = $contrat->Agent;
                        $tabl[$j]["nom"] = $com['Nom'].' '.$com['Prenom'];
                        $tabl[$j]["equipe"] = $com['Equipe'];
                        $tabl[$j]["nomequipe"] = $com['NomEquipe'].' '.$com['PrenomEquipe'];
                        $tabl[$j]["ins"] = $com['Inspecteur'];
                        $tabl[$j]["nomins"] = $com['NomInspecteur'].' '.$com['PrenomInspecteur'];
                        $tabl[$j]["rg"] = $com['RG'];
                        $tabl[$j]["nomrg"] = $com['NomRG'].' '.$com['PrenomRG'];
                        $tabl[$j]["cd"] = $com['CD'];
                        $tabl[$j]["nomcd"] = $com['NomCD'].' '.$com['PrenomCD'];
                        $j++;
                    }else{
                        $tabl[$j]["police"] = $police;
                        $tabl[$j]["libelle"] = $libelle;
                        $tabl[$j]["montant"] = $montant;
                        $tabl[$j]["rejet"] = $rejet;
                        $tabl[$j]["banque"] = $banque;
                        $tabl[$j]["apporteur"] = "";
                        $tabl[$j]["nom"] = "";
                        $tabl[$j]["equipe"] = "";
                        $tabl[$j]["nomequipe"] = "";
                        $tabl[$j]["ins"] = "";
                        $tabl[$j]["nomins"] = "";
                        $tabl[$j]["rg"] = "";
                        $tabl[$j]["nomrg"] = "";
                        $tabl[$j]["cd"] = "";
                        $tabl[$j]["nomcd"] = "";
                        $j++;
                    }
                    
                }
                
                $autre = new Collection($tabl);
                Session()->put('maj', $autre);
                // Téléchargement du fichier excel
                return Excel::download(new Export, 'CorrineZ'.date('Y-m-d-h-i-s').'.xlsx'); */
                /*
                for ($i=1; $i < count($commerciaux); $i++) { 
                 
                    $app = $commerciaux[$i];
                    $agnt = $app[0];
                    
                    Compteagent::where('Agent', $agnt)->update(["compteEncadrementMoisCalculer" => $app[1], "statutEng" => 1]);
                    
                }*/
                /*
                $tabl = array();
                // préparation du fichier excel
                $tabl[0]["agent"] = "";
                $tabl[0]["nom"] = "";
                $tabl[0]["num"] = "";
                $tabl[0]["email"] = "";
                $tabl[0]["pass"] = "";
                $tabl[0]["isauthorized"] = "";
                $tabl[0]["createdAt"] = "";
                $tabl[0]["updatedAt"] = "";
                $tabl[0]["hcd"] = "";
                */
                /*
                // num, nombre
                for ($i=1; $i < count($commerciaux); $i++) { 
                    $app = $commerciaux[$i];
                    $agnt = $app[0];
                    $tabl[$i]["agent"] = $app[0];
                    
                    $agt = Commerciaux::where('codeCom', $agnt)->first();
                    if(isset($agt->codeCom)){
                        $tabl[$i]["nom"] = $agt->nomCom.' '.$agt->prenomCom;
                        $tabl[$i]["num"] = $agt->telCom;
                        $tabl[$i]["email"] = $agt->mail;
                    }else{
                        $tabl[$i]["nom"] = "";
                        $tabl[$i]["num"] = "";
                        $tabl[$i]["email"] = "";
                    }
                    $tabl[$i]["pass"] = $app[1];
                    $tabl[$i]["isauthorized"] = $app[2];
                    $tabl[$i]["createdAt"] = $app[3];
                    $tabl[$i]["updatedAt"] = $app[4];
                    $tabl[$i]["hcd"] = $app[5];
                } */
                /*$autre = new Collection($tabl);
                Session()->put('alltre', $autre);
                // Téléchargement du fichier excel
                return Excel::download(new ExportTraitement, 'Traitement'.date('Y-m-d-h-i-s').'.xlsx');
                */
                /*
                $allcontrat = DB::table('contrats')->where("statutSunshine", "Actif")->get();
                
                $a = 0;
                for ($i=1; $i < count($commerciaux); $i++) { 
                    $app = $commerciaux[$i];

                    $code = $app[0];
                    $codeequipe = $app[2];
                    $codeinsp = $app[3];
                    
                    if(!isset($exit->codeCom)){
                        DB::table('contrats')->where("Agent", $code)->update(["ceqp"=>$codeequipe, "ins"=>$codeinsp]);
                        $a++;
                    }
                }
                */
                //dd($a);
                /*
                $all = DB::table('commerciauxes')->get();
                
                foreach($all as $com)
                {
                    if($com->Niveau == "CONS"){
                        if($com->codeEquipe != "" ||  $com->codeEquipe != "0000" || $com->codeEquipe != 0){
                            
                            $hieequipe = DB::table('hierarchies')->where('codeH', $com->codeEquipe)->where("structureH", "CEQP")->first();
                            if(!isset($hieequipe->managerH) || $hieequipe->managerH == null || $hieequipe->managerH == 0){
                                Commerciaux::where('codeCom', $com->codeCom)->update([
                                        "codeEquipe" => "",
                                        "codeInspection" => "",
                                        "codeRegion" => ""
                                    ]);
                            }else{
                                $hieequip = DB::table('hierarchies')->where('managerH', $hieequipe->managerH)->where("structureH", "CEQP")->first();
                                $Equipe_v = $hieequip->codeH;
                                $Inspection_v = $hieequip->superieurH;
                                 
                                            Commerciaux::where('codeCom', $com->codeCom)->update([
                                                "codeEquipe" => $Equipe_v,
                                                "codeInspection" => $Inspection_v,
                                                
                                            ]);
                            }
                        }else{
                            Commerciaux::where('codeCom', $com->codeCom)->update([
                                        "codeEquipe" => "",
                                        "codeInspection" => "",
                                        "codeRegion" => ""
                                    ]);
                        }
                    }
                } */
                //dd($all);
                /*
                for ($i=2; $i < count($commerciaux); $i++) { 
                    $app = $commerciaux[$i];

                    $code = $app[0];
                    $codeequipe = $app[1];
                    $codeinsp = $app[2];
                    
                    // Code Equipe 
                    $hieequipe = DB::table('hierarchies')->where('managerH', $codeequipe)->where("structureH", "CEQP")->first();
                    $Equipe_v = $hieequipe->codeH;
                    
                    $hieins = DB::table('hierarchies')->where('codeH', $hieequipe->superieurH)->whereIn("structureH", trans('var.ins'))->first();
                    
                    //dd($hieins->managerH);
                    $Inspection_v = $hieequipe->superieurH;
                    
                                $dataInfo = json_encode(Commerciaux::where('codeCom', $code)->first());
                                $dataCompte = json_encode(Compteagent::where('Agent', $code)->first());
                                $data = json_encode(array($dataInfo, $dataCompte));
                                TraceController::setTrace("Existing sales (commercial) data : ".$data, session("utilisateur")->idUser);
                                
                                Commerciaux::where('codeCom', $code)->update([
                                    "codeEquipe" => $Equipe_v,
                                    "codeInspection" => $Inspection_v,
                                ]);
                                
                }
                */
                
                    flash("Tous les commerciaux importés avec succès.")->success();
                    return Back();
                
            }else{
                flash("Le fichier n'est pas un fichier Excel.")->error();
            }
        }else{
            flash("Pas de fichier Commerciaux.")->error();
        }
        
        // Importer Taux 
        if ($request->hasFile('taux')) {
            $referenceNote = "Importer-Taux-".date('ymdhis');
            $namefile = $referenceNote.".xlsx";
            $upload = "document/upload/";
            $request->file('taux')->move($upload, $namefile);
               
            $patht = $upload.$namefile;

            $tabt = Excel::toArray( new ImportExcel, $patht);
            dd($tabt[0][7][1]);
            dd(count($tabt));
            $taux = $tabt[0];
            $i=0;
            //dd(count($taux));
            for ($i=2; $i < count($taux); $i++) { 
                $tauxline = $taux[$i];
                $taux_codeprod = $tauxline[0];
                $taux_libprod = $tauxline[1];
                $taux_daterenouvellement = $tauxline[2]; 
                $taux_convention = $tauxline[3];  
                $taux_codeapporteur = $tauxline[4];
                $taux_typeapporteur = $tauxline[5]; // considérer comme nom apporteur
                $taux_taux = $tauxline[6];
                $taux_niveau = $tauxline[7];
                $taux_basemin = $tauxline[8];
                $taux_basemax = $tauxline[9];
                $taux_access = $tauxline[10];
                
                $taux_vv = 0;
                $com_vv = 0;
                if(substr($taux_taux, -1) != "p")
                    $taux_vv = $taux_taux * 100;
                if(substr($taux_taux, -1) == "p"){
                    $taux_vv = -1;
                    $com_vv = substr($taux_taux, 0, -1);
                }
                
                if($taux_basemin == null)
                    $taux_basemin = 0;
                if($taux_basemax == null)
                    $taux_basemax = 0;
                    
                // save produit 
                Fonction::saveProduit($taux_codeprod, $taux_libprod);
                    
                // save apporteur bénéficiant sur même affaire
                /*if(isset(DB::table('taux_niveaus')->where('police', $taux_convention)->first()->police)){
                    $appexterne = DB::table('contrats')->where('police', $taux+_convention)->first()->NumExterne; // json encode
                    if($appexterne == null || $appexterne == ""){
                        $tabapp = array();
                        array_push($tabapp, $taux_codeapporteur);
                        $tabapp_encode = json_encode($tabapp); 
                        DB::table('contrats')->where('police', $taux_convention)->update(["NumExterne" => $tabapp_encode]);
                    }else{
                        $appexterne_decode = json_decode($appexterne);
                        if (!in_array($taux_codeapporteur, $appexterne_decode)){
                            array_push($appexterne_decode, $taux_codeapporteur);
                            $tabapp_encode = json_encode($appexterne_decode); 
                            DB::table('contrats')->where('police', $taux_convention)->update(["NumExterne" => $tabapp_encode]);
                        }
                    }
                    
                }*/
                
                // Si code apporteur existe passe sinon créer
                if(!isset(DB::table('commerciauxes')->where('codeCom', $taux_codeapporteur)->first()->codeCom)){
                    $email = DB::table('users')->join('roles', 'roles.idRole', '=', 'users.Role')->select('users.mail as mail')->where("roles.code", "sp")->where("roles.statut", 0)->first()->mail;
                    Fonction::saveApporteur($taux_codeapporteur, $taux_typeapporteur, $email, $taux_niveau);
                }

                // save taux
                $add = new TauxNiveau();
                $add->Produit = $taux_codeprod;
                $add->Niveau = $taux_niveau;
                $add->Periodicite =  1;
                $add->dureenapplication = -1;
                $add->dureecontratmin = 0;
                $add->dureecontratmax = -1;
                $add->tauxCommissionnement = $taux_vv;
                $add->pourcentage = 100;
                $add->Agent = $taux_codeapporteur;
                $add->conv = $taux_convention;
                $add->comfixe = $com_vv;
                $add->Schema = "ANCIEN";
                $add->basemin = $taux_basemin;
                $add->basemax = $taux_basemax;
                $add->acces = $taux_access;
                $add->user_action = session("utilisateur")->idUser;
                $add->save();

           }
            flash("Fichier taux importé avec succès.");
        }else{
            //dd('too');
            flash("Pas de fichier Taux.")->error();
        }
        
        // Importer groupe

        if ($request->hasFile('groupe')) {
            $referenceNote = "I_Groupe_".date('YmdYhis');
            $namefile = $referenceNote.".xlsx";
            $upload = "document/upload/";
            $request->file('groupe')->move($upload, $namefile);
               
            $pathc = $upload.$namefile;

            $tabc = Excel::toArray( new ImportExcel, $pathc);
            $groupe = $tabc[0];
            //dd($groupe);
            $i=0;
            
            for ($i=1; $i < count($groupe); $i++) { 
                $contr = $groupe[$i];
                
                $contr_quittancenum = $contr[0];
                $contr_police = $contr[1]; 
                $contr_codeapporteur = $contr[2];
                $num_payeur = $contr[3];
                $payeur = $contr[4];
                
                $contr_creat = "";
                if( is_numeric( $contr[5] )) 
                    $contr_creat = ((strlen($contr[5])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[5] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($contr[5]));
                else 
                    $contr_creat = trim($contr[5]);
                    
                $DateDebutQuittance = "";
                if( is_numeric( $contr[6] )) 
                    $DateDebutQuittance = ((strlen($contr[6])!= 10)?
                        Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[6] - 25569)*24*60*60)):
                        Fonction::ChangeFormatDate2($contr[6]));
                else
                    $DateDebutQuittance = trim($contr[6]);
                    
                $DateFinQuittance = "";
                if( is_numeric( $contr[7] )) 
                    $DateFinQuittance = ((strlen($contr[7])!= 10)?
                        Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[7] - 25569)*24*60*60)):
                        Fonction::ChangeFormatDate2($contr[7]));
                else 
                    $DateFinQuittance = trim($contr[7]);
                $contr_dateffetdbut = "";
                if( is_numeric( $contr[8] )) 
                
                    $contr_dateffetdbut = ((strlen($contr[8])!= 10)?
                        Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[8] - 25569)*24*60*60)):
                        Fonction::ChangeFormatDate2($contr[8]));
                else
                    $contr_dateffetdbut = trim($contr[8]);
                    
                $contr_dateffetfin = "";
                if( is_numeric( $contr[9] )) 
                    $contr_dateffetfin = ((strlen($contr[9])!= 10)?
                        Fonction::ChangeFormatDate2(date('d/m/Y', ($contr[9] - 25569)*24*60*60)):
                        Fonction::ChangeFormatDate2($contr[9]));
                else 
                    $contr_dateffetfin = trim($contr[9]);
                $contr_base = $contr[10];
                $contr_index = $contr[11];
                $contr_produit = $contr[12];
                $contr_conv = $contr[13];
                $contr_fractionnement = 'M';
                $contr_dateecheance = $contr_dateffetfin;
                
                $num_commission = Fonction::genererNumCommissionG();
                
                $date_production = date('m');
                
                /* Commission */
                if ($contr_index == "S") {

                    if(!isset(Commission::where("NumQuittance", $contr_quittancenum)->first()->NumCommission)){
                        $add = new Commission();
                        $add->Apporteur = $contr_codeapporteur;
                        $add->NumCommission = $num_commission;
                        $add->NumPolice = $contr_police;
                        $add->DateCreation = $contr_creat;
                        $add->BaseCommission = $contr_base;
                        $add->NumQuittance = $contr_quittancenum;
                        $add->DateDebutQuittance = $DateDebutQuittance;
                        $add->DateFinQuittance = $DateFinQuittance;
                        $add->IndexQuittance = $contr_index;
                        $add->TypeCommission = 'g';
                        $add->DateProduction = $date_production;
                        $add->ncom = 0;
                        $add->ctrl = 0;
                        $add->Statut = view()->shared('periode');
                        $add->moiscalculer = view()->shared('periode');
                        $add->save();
                    }
                }

                
                /* Contrat */
                // récuperer le libelle à partir du sigle fractionnement
                $codeFrac = Fonction::LibelleFractionnement($contr_fractionnement);

                // Vérification de l'unicité de la police
                if(Fonction::VérificationPolice($contr_police)){
                    
                    // Enregistrer le client
                    Fonction::saveClient($payeur, "", $num_payeur, $num_payeur);

                    // Vérification pour éliminer les valeurs null remarquer dans le fichier
                    if($contr_codeapporteur != "NULL" && $contr_produit != "NULL")
                    {
                        $addContrat = new Contrat();
                        $addContrat->police = $contr_police;
                        $addContrat->Produit = $contr_produit;
                        $addContrat->Client = $num_payeur;
                        $addContrat->Agent = $contr_codeapporteur;
                        $addContrat->statutSunshine = "Actif";
                        $addContrat->DateDebutEffet = $contr_dateffetdbut;
                        $addContrat->DateFinEffet = $contr_dateffetfin;
                        $addContrat->DateEcheance = $contr_dateecheance;
                        $addContrat->fractionnement = $codeFrac;
                        $addContrat->conv = $contr_conv;
                        $addContrat->user_action = 1;
                        $addContrat->save();
                    }
                // Les variables tels que agent, produit, nom agent ne pas utiliser ici et provienne de police. 
                //Police qui n'est d'autre que la clé de la table contrat.
                }else
                {
                    // Mettre à jour Contrat 
                    Contrat::where('police', $contr_police)->update([
                        "fractionnement" => $codeFrac,
                        "statutSunshine" => "Actif"
                    ]);
                }
                
                
            }
            flash("Fichier groupe importé avec succès. ".$i);
        }else{
            flash("Pas de fichier groupe.")->error();
        }
        return Back();
        return response()->json(['response' =>  ""]);
        
    }

    public function getcommission()
    {
        return view('importExcel.importcommission');
    }

}
