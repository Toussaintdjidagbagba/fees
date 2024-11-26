<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportContrat;
use App\Exports\ExportClient;
use Illuminate\Support\Collection;
use App\Http\Model\Trace;

class ClientController extends Controller
{
    public function __construct() 
    {
        set_time_limit(72000); 
        ini_set('memory_limit', '1024M'); // or you could use 1G
    }
    //
    public function getclient()
    {

        $list = DB::table('clients');
        $search = "Rechercher";
        if(request('rec') == 1){
                if(request('check') != "" && request('check') != null){
                    $search = request('check');
                    $list = $list->where('clients.nom', 'like', request('check').'%')
                    ->orwhere('clients.prenom', 'like', request('check').'%')
                    ->paginate(20);
                    return view('client.listclient', compact('list', 'search'));
                }else{
                    $list = $list->paginate(20);
                    return view("client.listclient", compact('list', 'search'));
                }
        }

        $list = $list->paginate(20);
        return view('client.listclient', compact('list', 'search'));
    }
    
    public function exportclient(){
        $list = DB::table('clients')->get();
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["client"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["sexe"] = "";
            $tabl[$i]["payeur"] = "";
            $i++;
            
            foreach ($list as $item){
                $tabl[$i]["client"] = $item->idClient;
                $tabl[$i]["nom"] = $item->nom;
                $tabl[$i]["prenom"] = $item->prenom;
                $tabl[$i]["sexe"] = $item->sexe;
                $tabl[$i]["payeur"] = $item->Payeur;
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allclients', $autre);
            TraceController::setTrace("Vous avez exporté les clients en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportClient, 'Export_Client_'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    
}
