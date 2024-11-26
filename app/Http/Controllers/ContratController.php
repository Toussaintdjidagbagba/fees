<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportContrat;
use Illuminate\Support\Collection;
use App\Http\Model\Trace;

class ContratController extends Controller
{
    public function __construct() 
    {
        set_time_limit(72000); 
        ini_set('memory_limit', '1024M'); // or you could use 1G
    }
    //
    public function getcontrat()
    {

        $list = DB::table('contrats');
        $search = "Rechercher";
        if(request('rec') == 1){
                if(request('check') != "" && request('check') != null){
                    $search = request('check');

                    $list = $list->where('contrats.Agent', 'like', request('check').'%')
                    ->orwhere('contrats.police', 'like', request('check').'%')
                    ->orwhere('contrats.Client', 'like', request('check').'%')
                    ->paginate(20);
                    return view('contrat.listcontrat', compact('list', 'search'));
                }else{
                    $list = $list->paginate(20);
                    return view("contrat.listcontrat", compact('list', 'search'));
                }

            
        }

        $list = $list->paginate(20);
        return view('contrat.listcontrat', compact('list', 'search'));
    }
    
    public function getmodifcontrat(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table('contrats')->where('police', request('id'))->first();
            return view('contrat.modifcontrat', compact('info'));
        }
    }

    public function setmodifcontrat(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $validator = Validator::make($request->all(), [
                    'fract' => 'required|string',
                    'sta' => 'required|string',
                    'dde' => 'required|date',
                    'dfe' => 'required|date',
                ]);
            if($validator->fails())
                flash("Valeurs manquantes. ")->error();
            
            DB::table('contrats')->where('police', request('id'))->update(
                    [
                        'statutSunshine' =>  htmlspecialchars(trim($request->sta)),
                        'fractionnement' =>  htmlspecialchars(trim($request->fract)),
                        'DateDebutEffet' =>  date("d-m-Y", strtotime($request->dde)),
                        'DateFinEffet' =>  date("d-m-Y", strtotime($request->dfe)),
                        'user_action' => session("utilisateur")->idUser,
                    ]);
            flash("Le contrat est modifié avec succès. ")->success();
            TraceController::setTrace("Vous avez modifié le contrat ".request('id')." .",session("utilisateur")->idUser);
            return redirect('/listcontrat');
        }
    }
    
    public function exportcontrat(){
        $list = DB::table('contrats')->get();
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
            return Excel::download(new ExportContrat, 'Export_Contrat_'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    
}
