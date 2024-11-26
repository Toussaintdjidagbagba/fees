<?php

namespace App\Http\Controllers;
use DB;
use App\Providers\InterfaceServiceProvider;
use App\Http\Model\Commerciaux;
use App\Http\Model\Commission;
use Illuminate\Support\Collection;
use App\Http\Model\Hierarchie;
use App\Http\Model\Trace; 
use Illuminate\Http\Request;
use Crabbly\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel; 
use App\Exports\ExportCommissionGlobale;
use App\Exports\ExportCommissionDETAILGlobale;
use App\Exports\ExportCommissionControleDETAIL;
use App\Exports\ExportCommissionControlleResume;

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
        $this->Cell(0,10, iconv('utf-8', 'cp1252', "Tél (229) 21 36 55 00 / 21 36 54 00 Fax(229) 21 31 35 17 Email nsiavie.benin@groupensia.com - Site web : www.groupensia.com / www.nsiaviebenin.com"),0,0,'C');
    }
    
}

class HistoriqueGroupe extends Controller
{
    public function __construct()
    {
        set_time_limit(720000);
        ini_set("memory_limit", "512M");
    } 
    
	public function gethistcommission(){
        $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc");
        
        
        $list = $list->paginate(20);
		return view("historique.listcomgroupe", compact('list'));
	}
	
	public function gethistcommissiondetail(){
	   
	   $apporteur = DB::table('tracecomptes')->where('id', trim(request('id')))->first();
	   $comp = InterfaceServiceProvider::RecupCompteAncien($apporteur->Commercial, $apporteur->moiscalculer); 
	   $net_temp = ($comp['compteNetapayerMoisCalculer'] + $comp['retenue'] + $comp['aibMoisCalculer']);
       $taux = 0;
       if($net_temp != 0)
	       $taux = round(($comp['aibMoisCalculer'] / $net_temp) * 100);
	  
	   $list = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where('confirmertresorerie', "oui")
                    ->where('ctrl', 2)
                    ->where('Apporteur', $apporteur->Commercial)
                    ->where('commissions.Statut', $apporteur->moiscalculer);
        $niveau = "CONS";
        $detailCom ="";
        $nivcommer = "";
        if(!isset($list->first()->Niveau))
            $nivcommer = DB::table('commerciauxes')->where("commerciauxes.codeCom", $apporteur->Commercial)->first()->Niveau;
        else
            $nivcommer = $list->first()->Niveau;
            
        if ($nivcommer == "CEQP") {
            $niveau = "CEQP";
            // Ce qu'il a gagner en tant chef d'équipe
            $codeEquipe = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeEquipe;
            $detailCom = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 2)
                            ->where("commissions.Statut", $apporteur->moiscalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeEquipe', $codeEquipe)->get(); 
        }
        if ($nivcommer == "INS") {
            $niveau = "INS";
            
                            $codeIns = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeInspection;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 2)
                            ->where("commissions.Statut", $apporteur->moiscalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeInspection', $codeIns)->get();
                        }
        if ($nivcommer == "RG") {
                            $niveau = "RG";
                            $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->Commercial)->first()->codeRegion;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 2)
                            ->where("commissions.Statut", $apporteur->moiscalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->Commercial)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeRegion', $codeRG)->get();
                        }
        
        $comm = DB::table('commerciauxes')->where("commerciauxes.codeCom", $apporteur->Commercial)->first();
	   $nomapp = $comm->nomCom.' '.$comm->prenomCom;
	   setlocale(LC_ALL, 'fr_FR', 'fra_FRA');
	   $mois = strftime('%B %Y', strtotime(implode('-',array_reverse  (explode('-',$apporteur->moiscalculer)))));
	   
       $list = $list->paginate(1000);
	   return view("historique.listdetailcom", compact('list', 'taux', 'nomapp', 'mois', 'niveau', 'detailCom'));
	}
	
	public function gethistcommissiondetailCorrectionJuillet2022($app){
	   
	   $apporteur = DB::table('tracecomptes')->where('id', $app)->first();
	   $comp = InterfaceServiceProvider::RecupCompteAncien($app, "07-2022"); 
	  
	   $list = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where('confirmertresorerie', "oui")
                    ->where('ctrl', 2)
                    ->where('Apporteur', $app)
                    ->where('commissions.Statut', "07-2022");
        $niveau = "CONS";
        $detailCom ="";
        $nivcommer = "";
        if(!isset($list->first()->Niveau))
            $nivcommer = DB::table('commerciauxes')->where("commerciauxes.codeCom", $app)->first()->Niveau;
        else
            $nivcommer = $list->first()->Niveau;
            
        if ($nivcommer == "CEQP") {
            $niveau = "CEQP";
            // Ce qu'il a gagner en tant chef d'équipe
            $codeEquipe = DB::table('commerciauxes')->where('codeCom', $app)->first()->codeEquipe;
            $detailCom = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 2)
                            ->where("commissions.Statut", "07-2022")
                            ->where('commerciauxes.codeCom', '<>', $app)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeEquipe', $codeEquipe)->get(); 
        }
        if ($nivcommer == "INS") {
            $niveau = "INS";
            
                            $codeIns = DB::table('commerciauxes')->where('codeCom', $app)->first()->codeInspection;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 2)
                            ->where("commissions.Statut", "07-2022")
                            ->where('commerciauxes.codeCom', '<>', $app)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeInspection', $codeIns)->get();
                        }
        if ($nivcommer == "RG") {
                            $niveau = "RG";
                            $codeRG = DB::table('commerciauxes')->where('codeCom', $app)->first()->codeRegion;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where('confirmercsp', "oui")
                            ->where('confirmerdt', "oui")
                            ->where('confirmerdg', "oui")
                            ->where('confirmercdaf', "oui")
                            ->where('confirmertresorerie', "oui")
                            ->where("ctrl", 2)
                            ->where("commissions.Statut", "07-2022")
                            ->where('commerciauxes.codeCom', '<>', $app)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commerciauxes.codeRegion', $codeRG)->get();
                        }
        $somcomc = 0; 
        if($nivcommer == "CEQP" ||  $nivcommer == "INS" || $nivcommer == "RG")
        foreach($detailCom as $comm){
            
            if($niveau == "CEQP")
    			$commission = $comm->MontantCEQ;
    	    if($niveau == "INS")
                $commission = $comm->MontantInspecteur;
            if($niveau == "RG")
                $commission = $comm->MontantRG;
            
            $somcomc += $commission;
        }
        
        
	   return $somcomc;
	}

    public function sethistcommission(Request $request)
    {
        
        $mois = implode('-',array_reverse  (explode('-',request('mois'))));
        if(request('exceldetail') == "EXCEL DETAIL"){
            $path = HistoriqueController::sethistcommissionExcelDETAIL($request);
            return Excel::download(new ExportCommissionDETAILGlobale, $path);
        }
        
        if(request('excel') == null && request("pdf") == null){
            $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc");
        	if( (request('codeApporteur') != "" && request('codeApporteur') != null) && ($mois != 0 && $mois != null) ){
        	    $list = $list->where('tracecomptes.moiscalculer',$mois)->where('tracecomptes.Commercial', request('codeApporteur'))->paginate(20);
            	return view("historique.listcomgroupe", compact('list'));
        	}
        	else
            	if((request('codeApporteur') != "" && request('codeApporteur') != null) || ($mois != 0 && $mois != null)){
            	   
            		$list = $list->where('tracecomptes.moiscalculer',$mois)->orwhere('tracecomptes.Commercial', request('codeApporteur'))->paginate(20);
            		return view("historique.listcomgroupe", compact('list'));
            	}else{
            		$list = $list->paginate(20);
            		return view("historique.listcomgroupe", compact('list'));
            	}
        }
        
        
        
        if(request('excel') == "EXCEL"){
            $path = HistoriqueController::sethistcommissionExcel($request);
            return Excel::download(new ExportCommissionGlobale, $path);
        }
        else{
            $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc");
                    
                $signature = DB::table("signataires");
                $titre = "";
                
            if ($request->codeApporteur != null && $mois != null) {
                $list = $list->where('tracecomptes.Commercial', request('codeApporteur'))->where('tracecomptes.moiscalculer', $mois)->get();
                $titre = "ETAT DES COMMISSIONS DE L'APPORTEUR ".$request->codeApporteur;
            }
    
            if ($request->codeApporteur != null) {
                $list = $list->where('tracecomptes.Commercial', request('codeApporteur'))->get();
                $titre = "ETAT DES COMMISSIONS DE L'APPORTEUR ".$request->codeApporteur;
            }
    
            if ($mois != "") {
                if($mois == "12-2021")
                    $list = $list->where('tracecomptes.moiscalculer', $mois)->where('tracecomptes.created_at', ">=", "2021-12-27")->get(); // En raison de plusieurs modification qui a été faite le mois là, celle qui est le recap est celui du 27-12-2021
                else
                    $list = $list->where('tracecomptes.moiscalculer', $mois)->get();
                $signature = $signature->where('DateCalculer', $mois);
                setlocale(LC_ALL, 'fr_FR', 'fra_FRA');
                $titre = "ETAT DES COMMISSIONS DU ".strtoupper(strftime('%B %Y', strtotime(request('mois'))));
            }
    
            if ($request->codeApporteur == null && $mois == "" ) {
                $list = $list->get();
                $titre = "ETAT DES COMMISSIONS A DATE";
            }

            setlocale(LC_TIME,  'french');

            $datelettre = strtoupper(strftime('%B %Y'));
            //$datelettre = "NOVEMBRE 2021"; "11-2021"

            $signature = $signature->get();
            $signaturecdaf = $signature[4]->pathSignataire;
            $signaturedg = $signature[3]->pathSignataire;
            $signaturedt = $signature[2]->pathSignataire;
            $signaturecsp = $signature[1]->pathSignataire;
            $signaturesp = $signature[0]->pathSignataire;

            // Création du PDF
            $name = "pdf";

            
            $path = "document/commission/".$titre.".pdf";

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
            //$pdf->Image("document/commission/nn.png", 270, 10, -300);
            $pdf->SetFont("Arial", "", 6);
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
            $pdf->Cell(30,6,'Code Apporteur', 1, 0, 'C', 1);
            $pdf->Cell(45,6,iconv('utf-8','cp1252','Nom et Prénoms Apporteur'), 1, 0, 'C', 1);
            $pdf->Cell(20,6,'IFU', 1, 0, 'C', 1);
            $pdf->Cell(25,6,'Gains', 1, 0, 'C', 1);
            $pdf->Cell(25,6,'Taux AIB', 1, 0, 'C', 1);
            $pdf->Cell(25,6,'AIB', 1, 0, 'C', 1);
            $pdf->Cell(35,6,iconv('utf-8','cp1252','Avance Com Remboursée'), 1, 0, 'C', 1);
            $pdf->Cell(25,6,iconv('utf-8','cp1252','Prelèvement'), 1, 0, 'C', 1);
            $pdf->Cell(30,6,'Commission Nette', 1, 0, 'C', 1);
            $pdf->Cell(20,6,iconv('utf-8','cp1252','Période'), 1, 0, 'C', 1);
            $som = 0;
            $somgain = 0;
            $somaib = 0;
            $somavan = 0;
            $somprel = 0;
            $pdf->SetFont("Arial", "", 7);
            foreach ($list as $value){ 
                $comp = InterfaceServiceProvider::RecupCompteAncien($value->Commercial, $value->moiscalculer);
                $pdf->Ln(6);
                $pdf->Cell(30,6,iconv('utf-8','cp1252', $value->Commercial ), 1, 0, 'C');
                //$pdf->Cell(20,6,iconv('utf-8','cp1252', $value->Agent ), 1, 0, 'C');
                $pdf->Cell(45,6,iconv('utf-8','cp1252', $value->nomCom.' '.$value->prenomCom), 1, 0, 'C');
                $net_temp = ($comp['compteNetapayerMoisCalculer'] + $comp['retenue'] + $comp['aibMoisCalculer']);
                if($net_temp == 0)
                    $pdf->Cell(20,6,iconv('utf-8','cp1252', ""), 1, 0, 'C');
                else
                    if(round(($comp['aibMoisCalculer'] / $net_temp) * 100) == 5)
                        $pdf->Cell(20,6,iconv('utf-8','cp1252', ""), 1, 0, 'C');
                    else
                        $pdf->Cell(20,6,iconv('utf-8','cp1252', InterfaceServiceProvider::recipIFU($value->Commercial)), 1, 0, 'C');
                
                if($comp['compteBloquerBackup'] < 0){
                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format(($comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] + 
									($comp['compteBloquerBackup']) + $comp['recentrembourcer'] ), 0, '.', ' ')).' CFA', 1, 0, 'C');
					$somgain += ($comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] + 
									($comp['compteBloquerBackup']) + $comp['recentrembourcer'] );
                }
                else
                {    
                    $pdf->Cell(25,6,iconv('utf-8','cp1252', number_format(($comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] ), 0, '.', ' ')).' CFA', 1, 0, 'C');
					$somgain += ($comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] );
                }
                
                if($net_temp == 0)
                    $pdf->Cell(25,6,iconv('utf-8','cp1252', "" ), 1, 0, 'C');
                else
                    $pdf->Cell(25,6,iconv('utf-8','cp1252', round(($comp['aibMoisCalculer'] / $net_temp) * 100). ' %' ), 1, 0, 'C');
                $pdf->Cell(25,6,$comp['aibMoisCalculer'].' CFA', 1, 0, 'C');
                $pdf->Cell(35,6, number_format($comp['recentrembourcer'] , 0, '.', ' ').' CFA', 1, 0, 'C');
                $pdf->Cell(25,6, number_format($comp['retenue'], 0, '.', ' ')." CFA", 1, 0, 'C');
                
                $pdf->Cell(30,6, number_format($comp['compte'], 0, '.', ' ').' CFA', 1, 0, 'C');
                $som += $comp['compte'];
                
                $somaib += $comp['aibMoisCalculer'];
                $somavan += $comp['recentrembourcer'];
                $somprel += $comp['retenue'];
                $pdf->Cell(20,6, $value->moiscalculer, 1, 0, 'C');
                
            }
            $pdf->Ln(6);
            $pdf->SetFont("Arial", "B", 8);
            $pdf->setFillColor(230,230,230);
            $pdf->Cell(30,6,'', 1, 0, 'C', 1);
            $pdf->Cell(65,6,iconv('utf-8','cp1252','Total :'), 1, 0, 'C', 1);
            $pdf->Cell(25,6,number_format($somgain, 0, '.', ' ').' CFA', 1, 0, 'C', 1);
            $pdf->Cell(25,6,'', 1, 0, 'C', 1);
            $pdf->Cell(25,6,number_format($somaib, 0, '.', ' ').' CFA', 1, 0, 'C', 1);
            $pdf->Cell(35,6,number_format($somavan, 0, '.', ' ').' CFA', 1, 0, 'C', 1);
            $pdf->Cell(25,6,number_format($somprel, 0, '.', ' ').' CFA', 1, 0, 'C', 1);
            $pdf->Cell(30,6, number_format($som, 0, '.', ' ').' CFA', 1, 0, 'C', 1);
            $pdf->Cell(20,6,iconv('utf-8','cp1252', $mois ), 1, 0, 'C', 1);
            
            // Sauvegarde du pdf
            $pdf->Output('F', $path);
            Storage::put($path, $pdf->output());
            return $path;
        }        
    }
    
    public function sethistcommissionExcel($request)
    {
        $mois = implode('-',array_reverse  (explode('-',$request->mois)));
        
        $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc");
                //->where("commerciauxes.codeInspection", 103)->where('tracecomptes.moiscalculer', '!=', "12-2021")
        
        $titre = "";
        
        if ($request->codeApporteur != null && $mois != null) {
            $list = $list->where('tracecomptes.Commercial', $request->codeApporteur)->where('tracecomptes.moiscalculer', $mois)->get();
            $titre = "ETAT DES COMMISSIONS DE L'APPORTEUR ".$request->codeApporteur;
        }

        if ($request->codeApporteur != null) {
            $list = $list->where('tracecomptes.Commercial', $request->codeApporteur)->get();
            $titre = "ETAT DES COMMISSIONS DE L'APPORTEUR ".$request->codeApporteur;
        }

        if ($mois != "") {
            if($mois == "12-2021")
                $list = $list->where('tracecomptes.moiscalculer', $mois)->where('tracecomptes.created_at', ">=", "2021-12-27")->get(); // En raison de plusieurs modification qui a été faite le mois là, celle qui est le recap est celui du 27-12-2021
            else
                $list = $list->where('tracecomptes.moiscalculer', $mois)->get();
            setlocale(LC_ALL, 'fr_FR', 'fra_FRA');
            $titre = "ETAT DES COMMISSIONS DU ".strtoupper(strftime('%B %Y', strtotime($request->mois)));
        }

        if ($request->codeApporteur == null && $mois == "" ) {
            $list = $list->get();
            $titre = "ETAT DES COMMISSIONS A DATE";
        }

            setlocale(LC_TIME,  'french');

            $datelettre = strtoupper(strftime('%B %Y'));
            //$datelettre = "NOVEMBRE 2021";

            // Création du fichier Excel
            $name = "Historique_Globale".date('dmYhis');
        
            $path = $name.".xlsx";
        
            $tabl[0]["code"] = "";
            $tabl[0]["apporteur"] = "";
            $tabl[0]["ifu"] = "";
            $tabl[0]["gains"] = "";
            $tabl[0]["tauxaib"] = "";
            $tabl[0]["aib"] = "";
            $tabl[0]["avance"] = "";
            $tabl[0]["prel"] = "";
            $tabl[0]["carec"] = "";
            $tabl[0]["amical"] = "";
            $tabl[0]["commission"] = "";
            $tabl[0]["peri"] = "";
            $som = 0;
            $somgain = 0;
            $somaib = 0;
            $somavan = 0;
            $somprel = 0;
            $somcarec = 0;
            $somamical = 0;
            $i = 1;
            
            foreach ($list as $value){
                $comp = InterfaceServiceProvider::RecupCompteAncien($value->Commercial, $value->moiscalculer); 
                
                $encard = 0;
                if($value->moiscalculer == "07-2022"){
                    
                    $encard = HistoriqueController::gethistcommissiondetailCorrectionJuillet2022($value->Commercial);
                }
                $tabl[$i]["code"] = $value->Commercial;
                $tabl[$i]["apporteur"] = $value->nomCom.' '.$value->prenomCom;
                $net_temp = 0;
                
                $net_temp = ($comp['compteNetapayerMoisCalculer'] + $comp['retenue'] + $comp['aibMoisCalculer']);
                    
                if($net_temp == 0)
                    $tabl[$i]["ifu"] = "";
                else
                    if(round(($comp['aibMoisCalculer'] / $net_temp) * 100) == 5)
                        $tabl[$i]["ifu"] = "";
                    else
                        $tabl[$i]["ifu"] = InterfaceServiceProvider::recipIFU($value->Commercial);
                
                if($comp['compteBloquerBackup'] < 0){
                    if($value->moiscalculer == "07-2022")
                    {
                        $tabl[$i]["gains"] = $comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $encard + ($comp['compteBloquerBackup']) + $comp['recentrembourcer'];
                
                        $somgain += $tabl[$i]["gains"];
                    }else{
                        $tabl[$i]["gains"] = $comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] + ($comp['compteBloquerBackup']) + $comp['recentrembourcer'];
                
                        $somgain += $tabl[$i]["gains"];
                    }
                    
                }
                else {
                    if($value->moiscalculer == "07-2022"){
                        $tabl[$i]["gains"] = $comp['bonus'] + $comp['fixe'] + 
    									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $encard;
                    
                        $somgain += $tabl[$i]["gains"];
                    }else{
                        $tabl[$i]["gains"] = $comp['bonus'] + $comp['fixe'] + 
    									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'];
                    
                        $somgain += $tabl[$i]["gains"];
                    }
                }
                if($net_temp == 0)
                    $tabl[$i]["tauxaib"] = "";
                else
                    $tabl[$i]["tauxaib"] = round(($comp['aibMoisCalculer'] / $net_temp) * 100). ' %';
                $tabl[$i]["aib"] = $comp['aibMoisCalculer'];
                $tabl[$i]["avance"] = $comp['recentrembourcer'];
                $tabl[$i]["prel"] = $comp['retenue'];
                if(isset($comp['traceCarec'])){
                $tabl[$i]["carec"] = $comp['traceCarec'];
                $tabl[$i]["amical"] = $comp['tracesAmical'];
                $somcarec += $comp['traceCarec'];
                $somamical += $comp['tracesAmical'];}
                else{
                    $tabl[$i]["carec"] = 0;
                    $tabl[$i]["amical"] = 0;
                    $somcarec += 0;
                    $somamical += 0;
                }
                $tabl[$i]["commission"] = $comp['compte'];
                $som += $comp['compte'];
                $somaib += $comp['aibMoisCalculer'];
                $somavan += $comp['recentrembourcer'];
                $somprel += $comp['retenue'];
                //$tauxifu = InterfaceServiceProvider::RecupererTaux($value->Agent);
                
                $tabl[$i]["peri"] = $value->moiscalculer;
                //$tabl[$i]["net"] = (round($value->MontantConseiller - ($value->MontantConseiller * $tauxifu / 100)));
                $i++;
            }
            $i++;
            $tabl[$i]["code"] = "";
            $tabl[$i]["apporteur"] = "Total :";
            $tabl[$i]["ifu"] = "";
            $tabl[$i]["gains"] = number_format($somgain, 0, '.', ' ')." CFA";
            $tabl[$i]["tauxaib"] = "";
            $tabl[$i]["aib"] = number_format($somaib, 0, '.', ' ')." CFA";
            $tabl[$i]["avance"] = number_format($somavan, 0, '.', ' ')." CFA";
            $tabl[$i]["prel"] = number_format($somprel, 0, '.', ' ')." CFA";
            $tabl[$i]["carec"] = number_format($somcarec, 0, '.', ' ')." CFA";
            $tabl[$i]["amical"] = number_format($somamical, 0, '.', ' ')." CFA";
            $tabl[$i]["commission"] = number_format($som, 0, '.', ' ')." CFA";
            $tabl[$i]["peri"] = "";
            
            // Exporter tous les commissions 
            $autre = new Collection($tabl);
            Session()->put('commissionglobale', $autre);
            return $path;
            // Téléchargement du fichier excel
    }
    
    public function sethistcommissionExcelDETAIL($request){
        $mois = implode('-',array_reverse  (explode('-',$request->mois)));
        
        //$list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc");
        $list = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    //->where("commerciauxes.codeInspection", 103)
                    //->where('commissions.Statut', '!=', "12-2021")
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where('confirmertresorerie', "oui")
                    ->where('ctrl', 2);

        $titre = "";
        
        if ($request->codeApporteur != null && $mois != null) {
            $list = $list->where('commissions.Apporteur', $request->codeApporteur)->where('commissions.Statut', $mois)->get();
            $titre = "ETAT DETAILLE DES COMMISSIONS DE L'APPORTEUR ".$request->codeApporteur;
        }

        if ($request->codeApporteur != null) {
            $list = $list->where('commissions.Apporteur', $request->codeApporteur)->get();
            $titre = "ETAT DETAILLE DES COMMISSIONS DE L'APPORTEUR ".$request->codeApporteur;
        }

        if ($mois != "") {
            $list = $list->where('commissions.Statut', $mois)->get();
            setlocale(LC_ALL, 'fr_FR', 'fra_FRA');
            $titre = "ETAT DETAILLE DES COMMISSIONS DU ".strtoupper(strftime('%B %Y', strtotime($request->mois)));
        }

        if ($request->codeApporteur == null && $mois == "" ) {
            $list = $list->get();
            $titre = "ETAT DETAILLE DES COMMISSIONS A DATE";
        }

            setlocale(LC_TIME,  'french');

            $datelettre = strtoupper(strftime('%B %Y'));
            //$datelettre = "NOVEMBRE 2021";

            // Création du fichier Excel
            $name = "Historique_Globale";
        
            $path = $name.".xlsx";
            
            $tabl[0]["codeApp"] = "";
            $tabl[0]["nomApp"] = "";
            $tabl[0]["cat"] = $titre;
            $tabl[0]["police"] = "";
            $tabl[0]["effet"] = "";
            $tabl[0]["effetfin"] = "";
            $tabl[0]["frac"] = "";
            $tabl[0]["quittance"] = "";
            $tabl[0]["idpayeur"] = ' ';
            $tabl[0]["payeur"] = "";
            $tabl[0]["client"] = "";
            $tabl[0]["periode"] = "";
            $tabl[0]["codeCom"] = "";
            $tabl[0]["base"] = "";
            $tabl[0]["commission"] = "";
            $tabl[0]["chefequipe"] = "";
            $tabl[0]["chefins"] = "";
            $tabl[0]["chefrg"] = "";
            $sombase = 0;
            $somcom = 0;
            $somequi = 0;
            $somins = 0;
            $somrg = 0;
            $i = 1;
            
            foreach ($list as $value){
                $client = DB::table('clients')->where('idClient', $value->Client)->first();
                
                
                
                $tabl[$i]["codeApp"] = $value->Apporteur;
                $tabl[$i]["nomApp"] = $value->nomCom.' '.$value->prenomCom;
                $tabl[$i]["cat"] = $value->Niveau;
                $tabl[$i]["police"] = $value->NumPolice;
                $tabl[$i]["effet"] = $value->DateDebutEffet;
                $tabl[$i]["effetfin"] = $value->DateFinEffet;
                $tabl[$i]["frac"] = $value->fractionnement;
                $tabl[$i]["quittance"] = $value->NumQuittance;
                
                $payeur = DB::table('clients')->where('idClient', $client->Payeur)->first();
                
                if(isset($payeur->nom)){
                    $tabl[$i]["idpayeur"] = $payeur->idClient;
                    $tabl[$i]["payeur"] = $payeur->nom.' '.$payeur->prenom;
                }
                else{
                    $tabl[$i]["idpayeur"] = ' ';
                    $tabl[$i]["payeur"] = ' ';
                }
                $tabl[$i]["client"] = $client->nom.' '.$client->prenom;
                
                $tabl[$i]["periode"] = $value->Statut;
                $tabl[$i]["codeCom"] = $value->NumCommission;
                $tabl[$i]["base"] = $value->BaseCommission;
                $tabl[$i]["commission"] = $value->MontantConseiller;
                $tabl[$i]["chefequipe"] = $value->MontantCEQ;
                $tabl[$i]["chefins"] = $value->MontantInspecteur;
                $tabl[$i]["chefrg"] = $value->MontantRG;
                $sombase += $value->BaseCommission;
                $somcom += $value->MontantConseiller;
                $somequi += $value->MontantCEQ;
                $somins += $value->MontantInspecteur;
                $somrg += $value->MontantRG;
                $i++;
            }
            $i++;
            $tabl[$i]["codeApp"] = "";
            $tabl[$i]["nomApp"] = "";
            $tabl[$i]["cat"] = $titre;
            $tabl[$i]["police"] = "";
            $tabl[$i]["effet"] = "";
            $tabl[$i]["effetfin"] = "";
            $tabl[$i]["frac"] = "";
            $tabl[$i]["quittance"] = "";
            $tabl[$i]["idpayeur"] = ' ';
            $tabl[$i]["payeur"] = "";
            $tabl[$i]["client"] = "";
            $tabl[$i]["periode"] = "";
            $tabl[$i]["codeCom"] = "Total :";
            $tabl[$i]["base"] = $sombase;
            $tabl[$i]["commission"] = $somcom;
            $tabl[$i]["chefequipe"] = $somequi ;
            $tabl[$i]["chefins"] = $somins;
            $tabl[$i]["chefrg"] = $somrg;
            
            // Exporter tous les commissions 
            $autre = new Collection($tabl);
            Session()->put('commissiondetailglobale', $autre);
            return $path;
    }
    
    public function sethistcommissionExcelDETAILCedric($request){
        $mois = implode('-',array_reverse  (explode('-',$request->mois)));
        
        //$list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc");
        $list = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    //->where("commerciauxes.codeInspection", 103)
                    ->where('contrats.Produit', 8000)
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where('confirmertresorerie', "oui")
                    ->where('ctrl', 2);

        $titre = "";

        if ($request->codeApporteur == null && $mois == "" ) {
            $list = $list->get();
            $titre = "ETAT DETAILLE DES COMMISSIONS A DATE";
        }

            setlocale(LC_TIME,  'french');

            $datelettre = strtoupper(strftime('%B %Y'));
            //$datelettre = "NOVEMBRE 2021";

            // Création du fichier Excel
            $name = "Historique_Globale";
        
            $path = $name.".xlsx";
            
            $tabl[0]["police"] = "";
            $tabl[0]["prod"] = "";
            $tabl[0]["libprod"] = "";
            $tabl[0]["payeur"] = "";
            $tabl[0]["client"] = "";
            $tabl[0]["codeApp"] = "";
            $tabl[0]["nomApp"] = "";
            $tabl[0]["statut"] = "";
            $tabl[0]["effet"] = "";
            $tabl[0]["effetfin"] = "";
            $tabl[0]["quittance"] = "";
            $tabl[0]["periode"] = "";
            $tabl[0]["frac"] = "";
            $tabl[0]["base"] = "";
            $tabl[0]["commission"] = "";
            $tabl[0]["ifu"] = "";
            $tabl[0]["aib"] = "";
            $tabl[0]["comnet"] = "";
            $tabl[0]["chefequipe"] = "";
            $tabl[0]["chefins"] = "";
            $tabl[0]["chefrg"] = "";
            
            
            $sombase = 0;
            $somcom = 0;
            $somequi = 0;
            $somins = 0;
            $somrg = 0;
            $i = 1;
            
            foreach ($list as $value){
                $client = DB::table('clients')->where('idClient', $value->Client)->first();
                
                $comp = InterfaceServiceProvider::RecupCompteAncien($value->Apporteur, $value->Statut);
                if($comp != "")
            	{
            	    $net_temp = (intval($comp['compteNetapayerMoisCalculer']) + intval($comp['retenue']) + intval($comp['aibMoisCalculer']));
                    $taux = 0;
                    if($net_temp != 0)
                	  $taux = round((intval($comp['aibMoisCalculer']) / $net_temp) * 100);
            	}else{
            	    $taux = InterfaceServiceProvider::RecupererTaux($value->Apporteur);
            	}
                $tabl[$i]["police"] = $value->NumPolice;
                $tabl[$i]["prod"] = $value->Produit;
                $tabl[$i]["libprod"] = InterfaceServiceProvider::libprodid($value->Produit);
                $payeur = DB::table('clients')->where('idClient', $client->Payeur)->first();
                if(isset($payeur->nom)){
                    $tabl[$i]["payeur"] = $payeur->idClient;
                }
                else{
                    $tabl[$i]["payeur"] = ' ';
                }
                $tabl[$i]["client"] = $client->nom.' '.$client->prenom;
                $tabl[$i]["codeApp"] = $value->Apporteur;
                $tabl[$i]["nomApp"] = $value->nomCom.' '.$value->prenomCom;
                $tabl[$i]["statut"] = $value->statutSunshine;
                $tabl[$i]["effet"] = $value->DateDebutEffet;
                $tabl[$i]["effetfin"] = $value->DateFinEffet;
                $tabl[$i]["quittance"] = $value->NumQuittance;
                $tabl[$i]["periode"] = $value->Statut;
                $tabl[$i]["frac"] = $value->fractionnement;
                $tabl[$i]["base"] = $value->BaseCommission;
                $tabl[$i]["commission"] = $value->MontantConseiller;
                $tabl[$i]["ifu"] = $taux;
                $tabl[$i]["aib"] = round($value->MontantConseiller * $taux / 100, 0);
                $tabl[$i]["comnet"] = round ($value->MontantConseiller - ($value->MontantConseiller * $taux / 100), 0);
                $tabl[$i]["chefequipe"] = $value->MontantCEQ;
                $tabl[$i]["chefins"] = $value->MontantInspecteur;
                $tabl[$i]["chefrg"] = $value->MontantRG;
            
                $i++;
            }
            
            // Exporter tous les commissions 
            $autre = new Collection($tabl);
            Session()->put('commissiondetailglobale', $autre);
            return $path;
    }
    
    public function listCm(){
        $listIns = DB::table('hierarchies')->whereIn("structureH", ["INS", "FV"])->where("managerH", '!=', null)->where("managerH", '!=', 0)->get();
        
		return view("controle.control", compact('listIns'));
    }
    
    public function controleCommercialDETAIl(Request $request){
        
        // Code commercial inspecteurs
        $dataCom = DB::table('commerciauxes')->where('codeCom', $request->Commercial)->first();
        
        if(isset($dataCom->codeCom))
        {
            $list = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    ->where('commissions.Statut', date('m-Y'))
                    ->where('statutcalculer', "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where('ctrl', 1)
                    ->where('confirmertresorerie', null);
            
            // Cas d'un inspecteur
            if($dataCom->Niveau == "INS"){
                /***********************************Inspecteur******************************************************/
                
                $listIns = $list->where('Apporteur', $dataCom->codeCom)->get();
                
                $titre = "INSPECTEUR : ".$dataCom->codeCom;
                
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
                $sombase = 0;
                $somcom = 0;
                $somequi = 0;
                $somins = 0;
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
                    $sombase += $value->BaseCommission;
                    $somcom += $value->MontantConseiller;
                    $somequi += $value->MontantCEQ;
                    $somins += $value->MontantInspecteur;
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
                
                
                /****************************Equipe**************************************/
                
                $allmembres = DB::table('commerciauxes')->where('Niveau', 'CEQP')->where('codeInspection', $dataCom->codeInspection)->get();
                
                foreach($allmembres as $com)
                {
                   // Cas d'un chef d'équipe
                    if($com->Niveau == "CEQP"){
                        
                        $listEqui = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    ->where('commissions.Statut', date('m-Y'))
                    ->where('statutcalculer', "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('confirmercsp', "oui")
                    ->where('confirmerdt', "oui")
                    ->where('confirmerdg', "oui")
                    ->where('confirmercdaf', "oui")
                    ->where('ctrl', 1)
                    ->where('confirmertresorerie', null)->where('Apporteur', $com->codeCom)->get();
                        
                        $titreE = $titre." Equipe : ".$com->codeCom;
                
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
                        $sombaseE = 0;
                        $somcomE = 0;
                        $somequiE = 0;
                        $sominsE = 0;
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
                            $sombaseE += $value->BaseCommission;
                            $somcomE += $value->MontantConseiller;
                            $somequiE += $value->MontantCEQ;
                            $sominsE += $value->MontantInspecteur;
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
                        
                        /******************************Conseiller****************************/
                        $allmembress = DB::table('commerciauxes')->where('codeEquipe', $com->codeEquipe)->get();
                    
                        foreach($allmembress as $comm)
                        {
                            // Cas des conseillers
                            if($comm->Niveau != "CEQP"){
                                $listCons = Commission::join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                                ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                                ->where('commissions.Statut', date('m-Y'))
                                ->where('statutcalculer', "oui")
                                ->where('confirmercalcule', "oui")
                                ->where('confirmercsp', "oui")
                                ->where('confirmerdt', "oui")
                                ->where('confirmerdg', "oui")
                                ->where('confirmercdaf', "oui")
                                ->where('ctrl', 1)
                                ->where('confirmertresorerie', null)->where('Apporteur', $comm->codeCom)->get();
                        
                                $titreC = $titreE." Conseiller : ".$comm->codeCom;
                        
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
                                $sombaseC = 0;
                                $somcomC = 0;
                                $somequiC = 0;
                                $sominsC = 0;
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
                                    $sombaseC += $value->BaseCommission;
                                    $somcomC += $value->MontantConseiller;
                                    $somequiC += $value->MontantCEQ;
                                    $sominsC += $value->MontantInspecteur;
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
                            }
                        }
                        
                        /****************************** Fin Conseiller****************************/
                        
                        /****************************fin Equipe******************************/
                    }
                }
               
                /**************************** Fin Inspecteur*******************************/
                
                
                
                // Exporter tous les commissions 
                
                // Création du fichier Excel
                $path = "Commission_du_mois_en_cours_".$dataCom->codeCom.".xlsx";
                
                $autre = new Collection($tabl);
                Session()->put('commissiondetailcontrole', $autre);
                
                Excel::store(new ExportCommissionControleDETAIL, $path, 'excelstore');
                
                $data = ["pat" => $path];
                
                SendMail::sendFichecontr("touskanths@gmail.com", "Détail Commission par Inspection", $data);
                
                //return Excel::download(new ExportCommissionControleDETAIL, $path);
                
                
            }
            
        }
    }
    
    public function controleCommercialDETAIlResum(Request $request){
        $listIns = DB::table('hierarchies')->whereIn("structureH", ["INS", "FV"])->where("managerH", '!=', null)->where("managerH", '!=', 0)->get();
        
        foreach($listIns as $conseiller)
        {
            // Code commercial inspecteurs
            $dataCom = DB::table('commerciauxes')->where('codeCom', $conseiller->managerH)->first();
            
            if(isset($dataCom->codeCom))
            {
                $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->where('tracecomptes.moiscalculer', date('m-Y'));
                
                // Cas d'un inspecteur
                if($dataCom->Niveau == "INS"){
                    /***********************************Inspecteur******************************************************/
                    
                    //$listIns = $list->where('tracecomptes.Commercial', $dataCom->codeCom)->get();
                    
                    
                    $compteA = DB::table('compteagents')->where('Agent', $dataCom->codeCom)->first();
                    
                    $titre = "INSPECTEUR : ".$dataCom->codeCom;
                    
                    $tabl[0]["code"] = "";
                    $tabl[0]["apporteur"] = $titre;
                    $tabl[0]["ifu"] = "";
                    $tabl[0]["gains"] = "";
                    $tabl[0]["tauxaib"] = "";
                    $tabl[0]["aib"] = "";
                    $tabl[0]["avance"] = "";
                    $tabl[0]["prel"] = "";
                    $tabl[0]["carec"] = "";
                    $tabl[0]["amical"] = "";
                    $tabl[0]["commission"] = "";
                    $tabl[0]["peri"] = "";
                    $som = 0;
                    $somgain = 0;
                    $somaib = 0;
                    $somavan = 0;
                    $somprel = 0;
                    $somcarec = 0;
                    $somamical = 0;
                    $i = 1;
                    
                    //foreach ($listIns as $value){
                      //  $comp = InterfaceServiceProvider::RecupCompteAncien($value->Commercial, $value->moiscalculer); 
                        
                        $tabl[$i]["code"] = $dataCom->codeCom;
                        $tabl[$i]["apporteur"] = $dataCom->nomCom.' '.$dataCom->prenomCom;
                        $net_temp = ($compteA->compteNetapayerMoisCalculer + $compteA->retenue + $compteA->aibMoisCalculer);
                        if($net_temp == 0)
                            $tabl[$i]["ifu"] = "";
                        else
                            if(round(($compteA->aibMoisCalculer / $net_temp) * 100) == 5)
                                $tabl[$i]["ifu"] = "";
                            else
                                $tabl[$i]["ifu"] = InterfaceServiceProvider::recipIFU($dataCom->codeCom);
                        
                        if($compteA->compteBloquerBackup < 0){
                            $tabl[$i]["gains"] = $compteA->bonus + $compteA->fixe + 
        									$compteA->AutreCommissionMoisCalculer + $compteA->compteMoisCalculer
        									+ $compteA->compteEncadrementMoisCalculer + ($compteA->compteBloquerBackup) + $compteA->recentrembourcer;
                        
                            $somgain += $tabl[$i]["gains"];
                        }
                        else {
                            $tabl[$i]["gains"] = $compteA->bonus + $compteA->fixe + 
        									$compteA->AutreCommissionMoisCalculer + $compteA->compteMoisCalculer + $compteA->compteEncadrementMoisCalculer;
                        
                            $somgain += $tabl[$i]["gains"];
                        }
                        if($net_temp == 0)
                            $tabl[$i]["tauxaib"] = "";
                        else
                            $tabl[$i]["tauxaib"] = round(($compteA->aibMoisCalculer / $net_temp) * 100). ' %';
                        $tabl[$i]["aib"] = $compteA->aibMoisCalculer;
                        $tabl[$i]["avance"] = $compteA->recentrembourcer;
                        $tabl[$i]["prel"] = $compteA->retenue;
                        $tabl[$i]["carec"] = $compteA->traceCarec;
                        $tabl[$i]["amical"] = $compteA->tracesAmical;
                        $somcarec += $compteA->traceCarec;
                        $somamical += $compteA->tracesAmical;
                        $tabl[$i]["commission"] = $compteA->compte;
                        $som += $compteA->compte;
                        
                        $somaib += $compteA->aibMoisCalculer;
                        $somavan += $compteA->recentrembourcer;
                        $somprel += $compteA->retenue;
                        //$tauxifu = InterfaceServiceProvider::RecupererTaux($value->Agent);
                        
                        $tabl[$i]["peri"] = date('m-Y');
                        //$tabl[$i]["net"] = (round($value->MontantConseiller - ($value->MontantConseiller * $tauxifu / 100)));
                        $i++;
                    //}
                    $i++;
                    $tabl[$i]["code"] = "";
                    $tabl[$i]["apporteur"] = "Total :";
                    $tabl[$i]["ifu"] = "";
                    $tabl[$i]["gains"] = number_format($somgain, 0, '.', ' ')." CFA";
                    $tabl[$i]["tauxaib"] = "";
                    $tabl[$i]["aib"] = number_format($somaib, 0, '.', ' ')." CFA";
                    $tabl[$i]["avance"] = number_format($somavan, 0, '.', ' ')." CFA";
                    $tabl[$i]["prel"] = number_format($somprel, 0, '.', ' ')." CFA";
                    $tabl[$i]["carec"] = number_format($somcarec, 0, '.', ' ')." CFA";
                    $tabl[$i]["amical"] = number_format($somamical, 0, '.', ' ')." CFA";
                    $tabl[$i]["commission"] = number_format($som, 0, '.', ' ')." CFA";
                    $tabl[$i]["peri"] = "";
                    
                    
                    /****************************Equipe**************************************/
                    
                    $allmembres = DB::table('commerciauxes')->where('Niveau', 'CEQP')->where('codeInspection', $dataCom->codeInspection)->get();
                    
                    foreach($allmembres as $com)
                    {
                       // Cas d'un chef d'équipe
                        if($com->Niveau == "CEQP"){
                            
                            
                            
                            $compteE = DB::table('compteagents')->where('Agent', $com->codeCom)->first();
                            
                            $titreE = $titre." Equipe : ".$com->codeCom;
                    
                            $i++;
                            $tabl[$i]["code"] = "";
                            $tabl[$i]["apporteur"] = "";
                            $tabl[$i]["ifu"] = "";
                            $tabl[$i]["gains"] = "";
                            $tabl[$i]["tauxaib"] = "";
                            $tabl[$i]["aib"] = "";
                            $tabl[$i]["avance"] = "";
                            $tabl[$i]["prel"] = "";
                            $tabl[$i]["carec"] = "";
                            $tabl[$i]["amical"] = "";
                            $tabl[$i]["commission"] = "";
                            $tabl[$i]["peri"] = "";
                            
                            $i++;
                            $tabl[$i]["code"] = "";
                            $tabl[$i]["apporteur"] = $titreE;
                            $tabl[$i]["ifu"] = "";
                            $tabl[$i]["gains"] = "";
                            $tabl[$i]["tauxaib"] = "";
                            $tabl[$i]["aib"] = "";
                            $tabl[$i]["avance"] = "";
                            $tabl[$i]["prel"] = "";
                            $tabl[$i]["carec"] = "";
                            $tabl[$i]["amical"] = "";
                            $tabl[$i]["commission"] = "";
                            $tabl[$i]["peri"] = "";
                            $som = 0;
                            $somgain = 0;
                            $somaib = 0;
                            $somavan = 0;
                            $somprel = 0;
                            $somcarec = 0;
                            $somamical = 0;
                            $i++;
                            //$listEqui = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->where('tracecomptes.moiscalculer', date('m-Y'))
                            //->where('tracecomptes.Commercial', $com->codeCom)->get();
                            //foreach ($listEqui as $value){
                                //$comp = InterfaceServiceProvider::RecupCompteAncien($value->Commercial, $value->moiscalculer); 
                                
                                $tabl[$i]["code"] = $com->codeCom;
                                $tabl[$i]["apporteur"] = $com->nomCom.' '.$com->prenomCom;
                                $net_temp = ($compteE->compteNetapayerMoisCalculer + $compteE->retenue + $compteE->aibMoisCalculer);
                                if($net_temp == 0)
                                    $tabl[$i]["ifu"] = "";
                                else
                                    if(round(($compteE->aibMoisCalculer / $net_temp) * 100) == 5)
                                        $tabl[$i]["ifu"] = "";
                                    else
                                        $tabl[$i]["ifu"] = InterfaceServiceProvider::recipIFU($com->codeCom);
                                
                                if($compteE->compteBloquerBackup < 0){
                                    $tabl[$i]["gains"] = $compteE->bonus + $compteE->fixe + 
                									$compteE->AutreCommissionMoisCalculer + $compteE->compteMoisCalculer + 
                									$compteE->compteEncadrementMoisCalculer + ($compteE->compteBloquerBackup) + $compteE->recentrembourcer;
                                
                                    $somgain += $tabl[$i]["gains"];
                                }
                                else {
                                    $tabl[$i]["gains"] = $compteE->bonus + $compteE->fixe + 
                									$compteE->AutreCommissionMoisCalculer + $compteE->compteMoisCalculer + $compteE->compteEncadrementMoisCalculer;
                                
                                    $somgain += $tabl[$i]["gains"];
                                }
                                if($net_temp == 0)
                                    $tabl[$i]["tauxaib"] = "";
                                else
                                    $tabl[$i]["tauxaib"] = round(($compteE->aibMoisCalculer / $net_temp) * 100). ' %';
                                $tabl[$i]["aib"] = $compteE->aibMoisCalculer;
                                $tabl[$i]["avance"] = $compteE->recentrembourcer;
                                $tabl[$i]["prel"] = $compteE->retenue;
                                $tabl[$i]["carec"] = $compteE->traceCarec;
                                $tabl[$i]["amical"] = $compteE->tracesAmical;
                                $somcarec += $compteE->traceCarec;
                                $somamical += $compteE->tracesAmical;
                                $tabl[$i]["commission"] = $compteE->compte;
                                $som += $compteE->compte;
                                
                                $somaib += $compteE->aibMoisCalculer;
                                $somavan += $compteE->recentrembourcer;
                                $somprel += $compteE->retenue;
                                //$tauxifu = InterfaceServiceProvider::RecupererTaux($value->Agent);
                                
                                $tabl[$i]["peri"] = $compteE->moiscalculer;
                                //$tabl[$i]["net"] = (round($value->MontantConseiller - ($value->MontantConseiller * $tauxifu / 100)));
                                $i++;
                            //}
                            $i++;
                            $tabl[$i]["code"] = "";
                            $tabl[$i]["apporteur"] = "Total :";
                            $tabl[$i]["ifu"] = "";
                            $tabl[$i]["gains"] = number_format($somgain, 0, '.', ' ')." CFA";
                            $tabl[$i]["tauxaib"] = "";
                            $tabl[$i]["aib"] = number_format($somaib, 0, '.', ' ')." CFA";
                            $tabl[$i]["avance"] = number_format($somavan, 0, '.', ' ')." CFA";
                            $tabl[$i]["prel"] = number_format($somprel, 0, '.', ' ')." CFA";
                            $tabl[$i]["carec"] = number_format($somcarec, 0, '.', ' ')." CFA";
                            $tabl[$i]["amical"] = number_format($somamical, 0, '.', ' ')." CFA";
                            $tabl[$i]["commission"] = number_format($som, 0, '.', ' ')." CFA";
                            $tabl[$i]["peri"] = "";
                            
                            /******************************Conseiller****************************/
                            $allmembress = DB::table('commerciauxes')->where('codeEquipe', $com->codeEquipe)->get();
                        
                            foreach($allmembress as $comm)
                            {
                                // Cas des conseillers
                                if($comm->Niveau != "CEQP"){
                                    
                            
                                    $titreC = $titreE." Conseiller : ".$comm->codeCom;
                            
                                    $i++;
                                    $tabl[$i]["code"] = "";
                                    $tabl[$i]["apporteur"] = "";
                                    $tabl[$i]["ifu"] = "";
                                    $tabl[$i]["gains"] = "";
                                    $tabl[$i]["tauxaib"] = "";
                                    $tabl[$i]["aib"] = "";
                                    $tabl[$i]["avance"] = "";
                                    $tabl[$i]["prel"] = "";
                                    $tabl[$i]["carec"] = "";
                                    $tabl[$i]["amical"] = "";
                                    $tabl[$i]["commission"] = "";
                                    $tabl[$i]["peri"] = "";
                                    
                                    $i++;
                                    $tabl[$i]["code"] = "";
                                    $tabl[$i]["apporteur"] = $titreC;
                                    $tabl[$i]["ifu"] = "";
                                    $tabl[$i]["gains"] = "";
                                    $tabl[$i]["tauxaib"] = "";
                                    $tabl[$i]["aib"] = "";
                                    $tabl[$i]["avance"] = "";
                                    $tabl[$i]["prel"] = "";
                                    $tabl[$i]["carec"] = "";
                                    $tabl[$i]["amical"] = "";
                                    $tabl[$i]["commission"] = "";
                                    $tabl[$i]["peri"] = "";
                                    $som = 0;
                                    $somgain = 0;
                                    $somaib = 0;
                                    $somavan = 0;
                                    $somprel = 0;
                                    $somcarec = 0;
                                    $somamical = 0;
                                    $i++;

                                    /*$listCons = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')
                                    ->where('tracecomptes.moiscalculer', date('m-Y'))
                            ->where('tracecomptes.Commercial', $comm->codeCom)->get();*/
                            $compteC = DB::table('compteagents')->where('Agent', $comm->codeCom)->first();
                                    
                                    //foreach ($listCons as $value){
                                     //   $comp = InterfaceServiceProvider::RecupCompteAncien($value->Commercial, $value->moiscalculer); 
                                        
                                        $tabl[$i]["code"] = $comm->codeCom;
                                        $tabl[$i]["apporteur"] = $comm->nomCom.' '.$comm->prenomCom;
                                        $net_temp = ($compteC->compteNetapayerMoisCalculer + $compteC->retenue + $compteC->aibMoisCalculer);
                                        if($net_temp == 0)
                                            $tabl[$i]["ifu"] = "";
                                        else
                                            if(round(($compteC->aibMoisCalculer / $net_temp) * 100) == 5)
                                                $tabl[$i]["ifu"] = "";
                                            else
                                                $tabl[$i]["ifu"] = InterfaceServiceProvider::recipIFU($comm->Commercial);
                                        
                                        if($compteC->compteBloquerBackup < 0){
                                            $tabl[$i]["gains"] = $compteC->bonus + $compteC->fixe + 
                        									$compteC->AutreCommissionMoisCalculer + $compteC->compteMoisCalculer + $compteC->compteEncadrementMoisCalculer + ($compteC->compteBloquerBackup) + $compteC->recentrembourcer;
                                        
                                            $somgain += $tabl[$i]["gains"];
                                        }
                                        else {
                                            $tabl[$i]["gains"] = $compteC->bonus + $compteC->fixe + 
                        									$compteC->AutreCommissionMoisCalculer + $compteC->compteMoisCalculer + $compteC->compteEncadrementMoisCalculer;
                                        
                                            $somgain += $tabl[$i]["gains"];
                                        }
                                        if($net_temp == 0)
                                            $tabl[$i]["tauxaib"] = "";
                                        else
                                            $tabl[$i]["tauxaib"] = round(($compteC->aibMoisCalculer / $net_temp) * 100). ' %';
                                        $tabl[$i]["aib"] = $compteC->aibMoisCalculer;
                                        $tabl[$i]["avance"] = $compteC->recentrembourcer;
                                        $tabl[$i]["prel"] = $compteC->retenue;
                                        $tabl[$i]["carec"] = $compteC->traceCarec;
                                        $tabl[$i]["amical"] = $compteC->tracesAmical;
                                        $somcarec += $compteC->traceCarec;
                                        $somamical += $compteC->tracesAmical;
                                        $tabl[$i]["commission"] = $compteC->compte;
                                        $som += $compteC->compte;
                                        
                                        $somaib += $compteC->aibMoisCalculer;
                                        $somavan += $compteC->recentrembourcer;
                                        $somprel += $compteC->retenue;
                                        //$tauxifu = InterfaceServiceProvider::RecupererTaux($value->Agent);
                                        
                                        $tabl[$i]["peri"] = $value->moiscalculer;
                                        //$tabl[$i]["net"] = (round($value->MontantConseiller - ($value->MontantConseiller * $tauxifu / 100)));
                                        $i++;
                              //      }
                                    $i++;
                                    $tabl[$i]["code"] = "";
                                    $tabl[$i]["apporteur"] = "Total :";
                                    $tabl[$i]["ifu"] = "";
                                    $tabl[$i]["gains"] = number_format($somgain, 0, '.', ' ')." CFA";
                                    $tabl[$i]["tauxaib"] = "";
                                    $tabl[$i]["aib"] = number_format($somaib, 0, '.', ' ')." CFA";
                                    $tabl[$i]["avance"] = number_format($somavan, 0, '.', ' ')." CFA";
                                    $tabl[$i]["prel"] = number_format($somprel, 0, '.', ' ')." CFA";
                                    $tabl[$i]["carec"] = number_format($somcarec, 0, '.', ' ')." CFA";
                                    $tabl[$i]["amical"] = number_format($somamical, 0, '.', ' ')." CFA";
                                    $tabl[$i]["commission"] = number_format($som, 0, '.', ' ')." CFA";
                                    $tabl[$i]["peri"] = "";
                                }
                            }
                            
                            /****************************** Fin Conseiller****************************/
                            
                            /****************************fin Equipe******************************/
                        }
                    }
                   
                    /**************************** Fin Inspecteur*******************************/
                    
                    
                    
                    // Exporter tous les commissions 
                    
                    // Création du fichier Excel
                    $path = "Commission_du_mois_en_cours_resume_".$dataCom->codeCom.".xlsx";
                    
                    $autre = new Collection($tabl);
                    Session()->put('commissioncontrlere', $autre);
                    
                    Excel::store(new ExportCommissionControlleResume, $path, 'excelstore');
                        
                        $data = ["pat" => $path];
                        SendMail::sendFichecontr($dataCom->mail, "Détail Commission par Inspection", $data);
                        return 0;
                        
                }
                
            }
            
        }
    }
}