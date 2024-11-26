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
use App\Exports\ExportErreurAutreCommission;
use App\Http\Import\ImportExcel;
use Illuminate\Support\Facades\Session;
use QrCode;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte;
use Crabbly\Fpdf\Fpdf;

class CommissionValidationController extends Controller
{
    public function __construct()
    {
        set_time_limit(72000);
    }
    
    /**
     *  Get && Set Validation SP
     * */
    public function getcommissioncons() 
    {
        $moisencours = view()->shared('periode');
        $list = DB::table('commissions')
            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->select('NumCommission as Commission', 'NumQuittance as Quittance', 'NumPolice as Police',
                'contrats.Produit as Produit', 'commissions.bareme as sch',
                'commissions.BaseCommission as base', 'commissions.MontantConseiller as mont', 'commissions.MontantSunShine as montSun',
                'Garantie', 'contrats.Agent as Commercial', 'commissions.MontantCEQ as montceq', 'commissions.MontantInspecteur as montins', 'commissions.MontantRG as montrg', 'commissions.MontantCD as montcd' )
            ->where("statutcalculer", "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
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
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", view()->shared('periode'))
                ->where('confirmercalcule', null)->get();
            
            if (isset($commission) && sizeof($commission) != 0) {
                if(request('check') != "" && request('check') != null){
                    $search = request('check');
                    $list= $list->whereRaw(" (NumPolice = ".request('check')." or NumCommission = ".request('check')." or NumQuittance = ".request('check')." or Apporteur = ".request('check')." ) ")
                    ->paginate(100);
                    return view('commission.listcomcons', compact('list','sigle', 'search'));
                }else{
                    $list = $list->paginate(100);
                    return view("commission.listcomcons", compact('list','sigle', 'search'));
                }
            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }
        
        $list = $list->paginate(100);
        
        return view('commission.listcomcons', compact('list', 'sigle', 'search'));
    }
    
    public function geterreurcommissioncons() 
    {
        $list = DB::table('commissions')
            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->select('NumCommission as Commission', 'NumQuittance as Quittance', 'NumPolice as Police',
                'contrats.Produit as Produit', 'commissions.bareme as sch',
                'commissions.BaseCommission as base', 'commissions.MontantConseiller as mont', 'commissions.MontantSunShine as montSun',
                'Garantie', 'contrats.Agent as Commercial' )
            ->where("statutcalculer", "oui")
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where("commissions.Statut", view()->shared('periode'))
            ->where('MontantConseiller', '!=', 'MontantSunShine')
            ->where('confirmercalcule', null); // Mode TEST
            
        $prenom = explode(" ", session('utilisateur')->prenom);
        $sigleprenom = "";
        foreach ($prenom as $value) {
            $sigleprenom .= substr( $value , 0, 1);
        }

        $sigle = substr( session('utilisateur')->nom , 0, 1).$sigleprenom;
        
        $libelleRole = strtoupper(DB::table('roles')->where('idRole', session('utilisateur')->Role)->first()->code);
        
        $search = "Rechercher";
        //dd(request('check'));
        if(request('rec') == 1){
            $commission = DB::table('commissions')
            ->where('statutcalculer', 'oui')
            ->where("ctrl", 1)
            ->where('TypeCommission', 'i')
            ->where("commissions.Statut", view()->shared('periode'))
            ->where('confirmercalcule', null)->get();
            if (isset($commission) && sizeof($commission) != 0) {
                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('NumPolice', request('check'))->orwhere('NumCommission', request('check'))->orwhere('NumQuittance', request('check'))
                    ->paginate(2000);
                    return view('commission.erreurcalcul', compact('list','sigle', 'libelleRole', 'search'));
                }else{
                    $list = $list->paginate(2000);
                    return view("commission.erreurcalcul", compact('list','sigle', 'libelleRole', 'search'));
                }
            }else{
                flash(" Pas de commission disponible pour ce mois!!! ");
                return Back();
            }
        }
        
        $list = $list->paginate(2000);
        
        return view('commission.erreurcalcul', compact('list', 'sigle', 'search', 'libelleRole'));
    }
    
    public function getcommissiondetail(){
        
        //dd("Fait");
        
        $apporteur = DB::table('commerciauxes')->where("commerciauxes.codeCom", trim(request('id')))->first();
        $compte = DB::table('compteagents')->where('Agent', trim(request('id')))->first();
        
        //$apporteur = DB::table('tracecomptes')->where('id', trim(request('id')))->first();
	    //$comp = InterfaceServiceProvider::RecupCompteAncien($apporteur->Commercial, $apporteur->moiscalculer); 
	    $net_temp = ($compte->compteNetapayerMoisCalculer + $compte->retenue + $compte->aibMoisCalculer);
        $taux = 0;
        if($net_temp != 0)
	       $taux = round(($compte->aibMoisCalculer / $net_temp) * 100);
	  
	    $list = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                    ->join('commerciauxes', 'commerciauxes.codeCom', '=', 'contrats.Agent')
                    ->where("statutcalculer", "oui")
                    ->where('confirmercalcule', "oui")
                    ->where('ctrl', 1)
                    ->where('TypeCommission', 'i')
                    ->where('Apporteur', $apporteur->codeCom)
                    ->where('commissions.Statut', $compte->MoisCalculer);
        $niveau = "CONS";
        $detailCom ="";$detailComA =array();$niveaua="";
        
        $nivcommer = $apporteur->Niveau;
            
        if ($nivcommer == "CEQP") {
            $niveau = "CEQP";
            $detailCom = DB::table('commissions')->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'i')
                            ->where("commissions.Statut", $compte->MoisCalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commissions.Toc', $apporteur->codeCom)->get(); 
        }
        if ($nivcommer == "INS") {
            $niveau = "INS";
            $detailCom = DB::table('commissions')
                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", $compte->MoisCalculer)
                ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                ->where('commerciauxes.Niveau', 'CONS')
                ->where("commissions.premiervalidation", $apporteur->codeCom)
                ->get();
                
            
            //dd($som);
        }
                        
        //dd($detailCom);
        
        if ($nivcommer == "RG") {
                            $niveau = "RG";
                            $codeRG = DB::table('commerciauxes')->where('codeCom', $apporteur->codeCom)->first()->codeRegion;
                            $detailCom = DB::table('commissions')
                            ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                            ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                            ->where("statutcalculer", "oui")
                            ->where('confirmercalcule', "oui")
                            ->where("ctrl", 1)
                            ->where('TypeCommission', 'i')
                            ->where("commissions.Statut", $compte->MoisCalculer)
                            ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                            ->where('commerciauxes.Niveau', 'CONS')
                            ->where('commissions.MontantRG', '!=', 0)
                            ->where('commerciauxes.codeRegion', $codeRG)->get();
        }
        
        if ($nivcommer == "CD") {
            $niveau = "CD";
            $codeCD = DB::table('commerciauxes')->where('codeCom', $apporteur->codeCom)->first()->codeCD;
            $detailCom = DB::table('commissions')
                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", $compte->MoisCalculer)
                ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                ->where('commerciauxes.Niveau', 'CONS')
                ->where('commissions.MontantCD', '!=', 0)
                ->where('commerciauxes.codeCD', $codeCD)->get();
        }
        
        if ($nivcommer == "CD" || $nivcommer == "RG") {
            $niveaua = "ANCIEN INS";
            
            $detailComA = DB::table('commissions')
                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", $compte->MoisCalculer)
                ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                ->where('commerciauxes.Niveau', 'CONS')
                ->where('commerciauxes.codeEquipe', '!=', 0)
                ->where('contrats.ins', $apporteur->codeCom)
                ->get();
        }
        
        if ($nivcommer == "INS" ) {
            $niveaua = "ANCIEN CEQP";
            
            $detailComA = DB::table('commissions')
                ->join('contrats', 'contrats.police', '=', 'commissions.NumPolice')
                ->join('commerciauxes', 'contrats.Agent', '=', 'commerciauxes.codeCom')
                ->where("statutcalculer", "oui")
                ->where('confirmercalcule', "oui")
                ->where("ctrl", 1)
                ->where('TypeCommission', 'i')
                ->where("commissions.Statut", $compte->MoisCalculer)
                ->where('commerciauxes.codeCom', '<>', $apporteur->codeCom)
                ->where('commerciauxes.Niveau', 'CONS')
                ->where('commerciauxes.codeEquipe', '!=', 0)
                ->where('contrats.ceqp', $apporteur->codeCom)
                ->get();
        }
        
       $nomapp = $apporteur->nomCom.' '.$apporteur->prenomCom;
	   setlocale(LC_ALL, 'fr_FR', 'fra_FRA');
	   $mois = strftime('%B %Y', strtotime(implode('-',array_reverse  (explode('-',$compte->MoisCalculer)))));
	   
       $list = $list->paginate(1000);
	   return view("commission.listdetailcom", compact('apporteur', 'compte', 'list', 'taux', 'nomapp', 'mois', 'niveau', 'detailCom', "niveaua", "detailComA"));
    }
    
    public function getdetailcontrat(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table('contrats')->where('police', request('id'))->first();
            return view('commission.detailpolicecom', compact('info'));
        }
    }
    
    public function getdetailquittance(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table('commissions')->where('NumQuittance', request('id'))->first();
            return view('commission.detailquittance', compact('info'));
        }
    }
}