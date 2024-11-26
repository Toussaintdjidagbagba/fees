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
use App\Http\Model\Commerciaux;
use App\Http\Model\Hierarchie;
use App\Http\Model\Tracecompte;
use Crabbly\Fpdf\Fpdf;
use Carbon\Carbon;

class PDFFFF extends Fpdf
{
    //
    function Header() {
        
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',5);
        $this->Cell(0,10,iconv('utf-8','cp1252',"Société Anonyme avec Conseil d'administration au Capital social de F CFA 3.000.000.000. Entreprise régie par le Code CIMA Immeuble NSIA, 1066 Boulevard Saint Michel - 08 BP 0258 Tri Postal - "),0,0,'C');
        $this->ln(4);
        $this->Cell(0,10, iconv('utf-8', 'cp1252', "Tél (229) 21 36 55 00 / 21 36 54 00 Fax(229) 21 31 35 17 Email nsiavie.benin@groupensia.com - Site web : www.groupensia.com / www.nsiaviebenin.com"),0,0,'C');
    }
    
}

class CommissionControllerAutres extends Controller
{

    public function renvoieDoc(){
		
		$allnaf = Compteagent::where("naf", "!=", 0)->get();
		$i = 0;
		foreach($allnaf as $anf){
			if($anf->naf == 650 || ($anf->naf == 2500 && (($anf->comptenaf % $anf->naf) == 0)) ){
					$dateeffet = Carbon::createFromFormat('d-m-Y', $anf->effetnaf);
					$dateactu = Carbon::now();

					$diff_mois = $dateeffet->diffInMonths($dateactu);

					$nombre_prel = $anf->comptenaf / $anf->naf;

					$tabl[$i]["apporteur"] = $anf->Agent;
					$tabl[$i]["dateeffet"] = $anf->effetnaf;
					$tabl[$i]["naf"] = $anf->naf;
					$tabl[$i]["impayenaf"] = $anf->impayernaf;
					$tabl[$i]["tracenaf"] = $anf->tracenaf;
					$tabl[$i]["comptenaf"] = $anf->comptenaf;
				
					$etat = "";
					$impaye = 0;
					$rembourse = 0;
					if($nombre_prel == $diff_mois) {
						// A jour
						$etat = "Apporteur à jour.";
						$impaye = 0;
						$rembourse = 0;
					}
					if($nombre_prel < $diff_mois) {
						// Impayé
						$etat = "Apporteur en impayés.";
						$impaye = ($diff_mois - $nombre_prel);
						$rembourse = 0;
						if($impaye > 2){
							$compnaf = Compteagent::where("Agent", $anf->Agent)->first();
							$compnaf->impayernaf = 2;
							$compnaf->save();
						}else{
							$compnaf = Compteagent::where("Agent", $anf->Agent)->first();
							$compnaf->impayernaf = $impaye;
							$compnaf->save();
						}
						
					}
					if($nombre_prel > $diff_mois) {
						// A remboursé
						$etat = "Apporteur à remboursé.";
						$impaye = 0;
						$rembourse = ( $nombre_prel - $diff_mois );
					}
				
					$tabl[$i]["etat"] = $etat;
					$tabl[$i]["impaye"] = $impaye;
					$tabl[$i]["rembours"] = $rembourse;
					$i++;
				}
				if($anf->naf == 2500 && (($anf->comptenaf % $anf->naf) != 0)) {
					// La personne avait payer 650 et passer 2500
				}
			}
			
			$autre = new Collection($tabl);
			Session()->put('autrecommission', $autre);
			// Téléchargement du fichier excel
			return Excel::download(new ExportExcel, 'ETAT_NAF'.date('Y-m-d-h-i-s').'.xlsx');
		
		//dd($allnaf);
			/*
			$compte = Commission::select('NumCommission')->where('Statut','09-2024')->distinct()->get();
	    
	    foreach($compte as $tc){
	        $tcc = Commission::select('id')->where('Statut','11-2023')->where('NumCommission', $tc->NumCommission)->orderby("id", "desc")->get();
	        if($tcc->count() == 2){
	            $id = Commission::select('id')->where('Statut','11-2023')->where('NumCommission', $tc->NumCommission)->orderby("id", "desc")->first()->id;
				echo $tc->NumCommission."<br>";
	            //Commission::where('id', $id)->delete();
	        }
	    } 
	    dd("OUI"); */
        
		/*
        $app = InterfaceServiceProvider::getcom(5580);
        
        dd(json_decode($app)[0]->Nom); */
        
        /**
         *
         *   Annuler les doublons dans l'historique compte
         */
        /*
        $compte = Tracecompte::select('Commercial')->where('moiscalculer','11-2023')->distinct()->get();
	    
	    foreach($compte as $tc){
	        $tcc = Tracecompte::select('id')->where('moiscalculer','11-2023')->where('Commercial', $tc->Commercial)->orderby("id", "desc")->get();
	        if($tcc->count() == 2){
	            $id = Tracecompte::select('id')->where('moiscalculer','11-2023')->where('Commercial', $tc->Commercial)->orderby("id", "desc")->first()->id;
	            Tracecompte::where('id', $id)->delete();
	        }
	    } 
	    dd("OUI"); */
	    /**
	     * 
	     * Annuler les doublons dans l'historique document 
	     * 
	     */
	    /*
	    $documents = Document::select('Agent')->where('periode','11-2023')->distinct()->get();
	    
	    foreach($documents as $tc){
	        $tcc = Document::select('id')->where('periode','11-2023')->where('Agent', $tc->Agent)->orderby("id", "desc")->get();
	        //dd($tcc->count());
	        if($tcc->count() == 2){
	            $id = Document::select('id')->where('periode','11-2023')->where('Agent', $tc->Agent)->orderby("id", "desc")->first()->id;
	            Document::where('id', $id)->delete();
	        }
	    }
	    dd("NON"); */
        //dd($documents);
        /*
        $commission = DB::table('commissions')
                ->where('statutcalculer', 'oui')
                ->where("ctrl", 2)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", date('m-Y'))
                ->get();
        
            $temp_commerciaux = array();
            $error = 0;
            $temp_chef = array();
            
            if (isset($commission) && sizeof($commission) != 0) {
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
                            if($comm->statutEnc == 0){
                                if($data_cons->codeEquipe != "" && $data_cons->codeEquipe != null){
                                    // Code commercial du Chef Equipe
                                    if(isset(DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH)){
                                        $chefequipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $data_cons->codeEquipe)->first()->managerH;
                                        // ontrole d'ancien equipe 
                                        $ancienequipe = DB::table('contrats')->where('police', $comm->NumPolice)->first()->ceqp;
                                        if ($ancienequipe != 0 && $ancienequipe != null){
                                            $contratmag = DB::table('contrats')->where('police', $comm->NumPolice)->first();
                                            if(strtotime($contratmag->DateDebutEffet) < strtotime("01-01-2023"))
                                            $chefequipe = $ancienequipe;
                                        }
                                        if ($chefequipe != 0 && $chefequipe != null){
                                            // Attribuer les soldes au sup
                                            //Fonction::SetMontantSup(($comm->MontantCEQ), $chefequipe);
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["Toc" => $chefequipe]);
                                            if (!in_array($chefequipe, $temp_chef))
                                            array_push($temp_chef, $chefequipe);
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
                                            $contratmag = DB::table('contrats')->where('police', $comm->NumPolice)->first();
                                            if(strtotime($contratmag->DateDebutEffet) < strtotime("01-01-2023"))
                                            $chefins = $ancienins;
                                        }
                                        if ($chefins != 0 && $chefins != null){
                                            // Attribuer les soldes au sup
                                            //Fonction::SetMontantSup(($comm->MontantInspecteur), $chefins);
                                            DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["premiervalidation" => $chefins]);
                                            if (!in_array($chefins, $temp_chef))
                                            array_push($temp_chef, $chefins);
                                        }
                                    }
                                }
                                
                                
                                
                                // Confirmer que telle commission a été attribuer au manager et ne fera plus objet de réatribution
                                DB::table('commissions')->where("NumCommission", $comm->NumCommission)->update(["statutEnc" => 1]);
                            }
                        }
                    }
    
                }
                
            }
        
        dd($commission);
        */
        /*
        $com = Commerciaux::where("codeRegion", 9999)->where("codeEquipe", "!=", "")->get();
        
        foreach ($com as $tra) {
            if($tra->codeInspection != "")
            {$sup = Hierarchie::where('codeH', $tra->codeInspection)->first()->superieurH;
            if($sup != 9999){
                $sups = Hierarchie::where('codeH', $sup)->first()->superieurH;
                
                Commerciaux::where('codeCom', $tra->codeCom)->update([
                            'codeCD' => $sups,
                            'codeRegion' => $sup,
                        ]);
            }
            }
        }
        
        dd($com); */
        /*
        foreach ($tabcom as $tc) {
            DB::table('contrats')->where('Agent', $tc["agent"])->update(["ceqp" => $tc["ceqp"], "ins" => $tc["ins"]]);
            //dd($tc["agent"]);
        } */
    }
       

    public static function MiseaJour()
    {
        Commission::where('Statut', "04-2023")->where("ctrl", 1)->update(["statutcalculer" => "oui"]);
        dd("oui");
        /*$com = Commerciaux::where("codeRegion", 9999)->where("codeEquipe", "!=", "")->get();
        
        foreach ($com as $tra) {
            if($tra->codeInspection != "")
            {
                $sup = Hierarchie::where('codeH', $tra->codeInspection)->first()->superieurH;
                if($sup != 9999){
                    $sups = Hierarchie::where('codeH', $sup)->first()->superieurH;
                    
                    Commerciaux::where('codeCom', $tra->codeCom)->update([
                                'codeCD' => $sups,
                                'codeRegion' => $sup,
                            ]);
                }
            }
        }
        
        dd($com); */
        /*$tras = DB::table('hierarchies')->whereIn("managerH", [6137, 5760, 5251, 5580, 5579, 6152, 3001, 8078, 7114, 7102, 8038, 7121])->where("structureH", "CEQP")->get();
        
        // Mise à jour du commercial
        foreach ($tras as $tra) {
                    Commerciaux::where('codeCom', $tra->managerH)->update([
                        'Niveau' => $tra->structureH, 
                        'codeEquipe' => $tra->codeH,
                        'codeInspection' => Hierarchie::where('codeH', $tra->codeH)->first()->superieurH,
                        'codeRegion' => 9999
                    ]);
        }
        dd($tra); */
        
    }
    
    public static function validateficheenvoyer(){
        if( request('code')  == 200)
            DB::table('documents')->where('Agent', request('agent'))->update(['statut' => "true"]);
    }
    
    public static function AutresAgentNonGerer(){
            
    }
    
    public static function RechercheAgentDotation($list, $codecom){
        
        $control = $codecom;
        
        foreach ($list as $apporteur) {
            if($apporteur->Commercial == $codecom) $control = 0;
        }
        
        return $control;
    }
    
}