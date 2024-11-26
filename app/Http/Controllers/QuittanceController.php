<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportContrat;
use Illuminate\Support\Collection;
use App\Http\Model\Trace;

class QuittanceController extends Controller
{
    public function __construct() 
    {
        set_time_limit(72000); 
        ini_set('memory_limit', '1024M'); // or you could use 1G
    }
    //
    public function getquittance()
    {
        $list = DB::table('commissions')
                    ->join("contrats", "contrats.police", "=", "commissions.NumPolice")
                    ->join("clients", "clients.idClient", "=", "contrats.Client")
                    ->join("produits", "produits.idProduit", "=", "contrats.Produit")
                    ->select('contrats.Agent as app', 'clients.idClient as payeur', 'contrats.police as police', 'produits.libelle as produit',
                    'clients.nom as nom', 'clients.prenom as prenom', 'commissions.NumQuittance as quittance',
                    "commissions.Statut as periode", "commissions.ctrl as etat",
                    "commissions.BaseCommission as base", "commissions.MontantConseiller as montcons",
                    "commissions.Toc as equipe", "commissions.MontantCEQ as montceq",
                    "commissions.premiervalidation as ins", "commissions.MontantInspecteur as montins",
                    "commissions.regionCom as region", "commissions.MontantRG as montrg",
                    "commissions.cdCom as cd", "commissions.MontantCD as montcd",
                    "commissions.DateDebutQuittance as periodequittance", "commissions.ncom as ncom", 
							 "commissions.confirmertresorerie as ct", "commissions.commentaire as ctre")
                    ->orderBy('commissions.id', 'desc')
                    ->paginate(20);
        return view('quittance.listquit', compact('list'));
    }
    
    public function seachquittance(Request $request){
        
        if(request('seach') != ""){
                $list = DB::table('commissions')
                    ->join("contrats", "contrats.police", "=", "commissions.NumPolice")
                    ->join("clients", "clients.idClient", "=", "contrats.Client")
                    ->join("produits", "produits.idProduit", "=", "contrats.Produit")
                    ->select('contrats.Agent as app', 'clients.idClient as payeur', 'contrats.police as police', 'produits.libelle as produit',
                    'clients.nom as nom', 'clients.prenom as prenom', 'commissions.NumQuittance as quittance',
                    "commissions.Statut as periode", "commissions.ctrl as etat",
                    "commissions.BaseCommission as base", "commissions.MontantConseiller as montcons",
                    "commissions.Toc as equipe", "commissions.MontantCEQ as montceq",
                    "commissions.premiervalidation as ins", "commissions.MontantInspecteur as montins",
                    "commissions.regionCom as region", "commissions.MontantRG as montrg",
                    "commissions.cdCom as cd", "commissions.MontantCD as montcd",
                    "commissions.DateDebutQuittance as periodequittance", "commissions.ncom as ncom", "commissions.confirmertresorerie as ct", "commissions.commentaire as ctre")
                    ->where('commissions.NumQuittance', request('seach'))
                    ->orwhere('contrats.police', request('seach'))
                    ->orderBy('commissions.id', 'desc')
                    ->get();
            if(isset($list) && count($list) != 0)
            {
                $data = json_encode(["success"=> true, "data"=> $list]);
                return $data;
            }else{
                $data = json_encode(["success"=> false]);
                return $data;
            }
        }
        $data = json_encode(["success"=> false]);
        return $data;

    }
    
    public function getmodifreclamation(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table('reclamations')->where('id', request('id'))->first();
            return view('quittance.modifquit', compact('info'));
        }
    }

    public function setmodifreclamation(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $validator = Validator::make($request->all(), [
                    'obs' => 'required|string',
                ]);
            if($validator->fails())
                flash("Valeurs manquantes. ")->error();
            
            DB::table('reclamations')->where('id', request('id'))->update(
                    [
                        'obsnsia' =>  htmlspecialchars(trim($request->obs)),
                        'usernsia' =>   session("utilisateur")->idUser,
                        'dateresponsensia' =>  date("d-m-Y"),
                        'etatnsia' =>  1,
                    ]);
            flash("La réclamation est traité avec succès. ")->success();
            TraceController::setTrace("La réclamation est traité avec succès.",session("utilisateur")->idUser);
            return redirect('/listreclamation');
        }
    }
    
    public function exportreclamation(){
        /*
        $list = DB::table('reclamations')->where('Produit', 8000)->get();
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["police"] = "";
            $tabl[$i]["produit"] = "";
            $tabl[$i]["libproduit"] = "";
            $tabl[$i]["codeclient"] = "";
            $tabl[$i]["client"] = "";
            $tabl[$i]["codepayeur"] = "";
            $tabl[$i]["payeur"] = "";
            $tabl[$i]["apport"] = "";
            $tabl[$i]["nomapport"] = "";
            $tabl[$i]["statut"] = "";
            $tabl[$i]["dateeffet"] = "";
            $tabl[$i]["datefineffet"] = "";
            $tabl[$i]["farc"] = "";
            $i++;
            
            foreach ($list as $item){
                $tabl[$i]["police"] = $item->police;
                $tabl[$i]["produit"] = $item->Produit;
                $tabl[$i]["libproduit"] = DB::table('produits')->where('idProduit', $item->Produit)->first()->libelle;
                $tabl[$i]["codeclient"] = $item->Client;
                $client = DB::table('clients')->where('idClient', $item->Client)->first();
                if(isset($client->nom)){
                    $tabl[$i]["client"] = $client->nom." ".$client->prenom;
                    
                    $codepayeur = DB::table('clients')->where('idClient', $item->Client)->first()->Payeur;
                    $payeur = DB::table('clients')->where('idClient', $codepayeur)->first();
                    
                    if(isset($payeur->nom)){
                        $tabl[$i]["codepayeur"] = $payeur->idClient;
                        $tabl[$i]["payeur"] = $payeur->nom." ".$payeur->prenom;
                    }else{
                        $tabl[$i]["codepayeur"] = "";
                        $tabl[$i]["payeur"] = "";
                    }
                    
                }else{
                    $tabl[$i]["client"] = "";
                    $tabl[$i]["codepayeur"] = "";
                    $tabl[$i]["payeur"] = "";
                }
                $tabl[$i]["apport"] = $item->Agent;
                $appr = DB::table('commerciauxes')->where('codeCom', $item->Agent)->first();
                if(isset($appr->nomCom))
                    $tabl[$i]["nomapport"] = $appr->nomCom." ".$appr->prenomCom;
                else
                    $tabl[$i]["nomapport"] = "";
                $tabl[$i]["statut"] = $item->statutSunshine;
                $tabl[$i]["dateeffet"] = $item->DateDebutEffet;
                $tabl[$i]["datefineffet"] = $item->DateFinEffet;
                $tabl[$i]["farc"] = $item->fractionnement;
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allcontrats', $autre);
            TraceController::setTrace("Vous avez exporté les contrats en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportContrat, 'Export_Contrat_'.date('Y-m-d-h-i-s').'.xlsx'); */
    }
    
    
}
