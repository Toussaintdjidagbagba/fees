<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportReclamation;
use Illuminate\Support\Collection;
use App\Http\Model\Trace;
use App\Providers\InterfaceServiceProvider;

class ReclamationController extends Controller
{
    public function __construct() 
    {
        set_time_limit(72000); 
        ini_set('memory_limit', '1024M'); // or you could use 1G
    }
    //
    public function getreclamation()
    {
        $list = DB::table('reclamations')->orderby("id", "DESC")->get();
        return view('reclamation.listrecl', compact('list'));
    }
    
    public function deletereclamation(Request $request){
        if (!in_array("update_recl", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table('reclamations')->where('id', request('idrecl'))->first();
            $ancien = json_encode($info);
            $addt = new Trace();
            $addt->libelleTrace = "Réclamation supprimé : ".$ancien;
            $addt->user_action = session("utilisateur")->idUser;
            $addt->save();
            DB::table('reclamations')->where('id', request('idrecl'))->delete();
            flash('Suppression effectué avec succès.')->success();
            return Back();
        }
    }
    
    public function seachreclamation(Request $request){
        if(request('seach') != ""){
                
            $list = DB::table('reclamations')
                ->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur")
                ->where('apporteur', 'like', request('seach').'%')
                ->orwhere('police', 'like', request('seach').'%')
                ->orwhere('client', 'like', request('seach').'%')
                ->orwhere('quittance', 'like', request('seach').'%')
                ->orwhere('commerciauxes.nomCom', 'like', request('seach').'%')
                ->orwhere('commerciauxes.prenomCom', 'like', request('seach').'%')
                ->orderby("id", "DESC")
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
        $list = DB::table('reclamations')->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur")->get();
        $data = json_encode(["success"=> true,"data"=> $list]);
        return $data;
    }
    
    public function triereclamation(Request $request){
        if(request('etat') != "" && request('type') != ""){
                
            $list = DB::table('reclamations')
                ->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur")
                ->where('typerecl', 'like', request('type').'%')
                ->where('etatnsia', 'like', request('etat').'%')
                ->orderby("id", "DESC")
                ->get();
            if(isset($list) && count($list) != 0)
            {
                $data = json_encode(["success"=> true, "data"=> $list]);
                return $data;
            }else{
                $data = json_encode(["success"=> false]);
                return $data;
            }
        }elseif(request('etat') != ""){
            $list = DB::table('reclamations')
                ->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur")
                ->where('etatnsia', 'like', request('etat').'%')
                ->orderby("id", "DESC")
                ->get();
            if(isset($list) && count($list) != 0)
            {
                $data = json_encode(["success"=> true, "data"=> $list]);
                return $data;
            }else{
                $data = json_encode(["success"=> false]);
                return $data;
            }
        }elseif(request('type') != ""){
            $list = DB::table('reclamations')
                ->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur")
                ->where('typerecl', 'like', request('type').'%')
                ->orderby("id", "DESC")
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
        $list = DB::table('reclamations')->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur")->get();
        $data = json_encode(["success"=> true,"data"=> $list]);
        return $data;
    }
    
    public function getmodifreclamation(Request $request)
    {
        if (!in_array("update_recl", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table('reclamations')->where('id', request('id'))->first();
            return view('reclamation.modifrecl', compact('info'));
        }
    }

    public function setmodifreclamation(Request $request)
    {
        if (!in_array("update_recl", session("auto_action"))) {
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
                        'periode' => $request->mois,
                    ]);
            // recup apporteur
            $reclamation = DB::table('reclamations')->where('id', request('id'))->first();
            $apporteur = $reclamation->apporteur;
            // recup mail
            $app = DB::table('commerciauxes')->where('codeCom', $apporteur)->first();
            $emailapp = $app->mail;
            $nom = $app->nomCom.' '.$app->prenomCom; 
            // Send mail to apporteur
            $data = ["observation"=>htmlspecialchars(trim($request->obs)), "librecl"=> $reclamation->librecl, "nom"=>$nom];
            SendMail::sendresponsereclamation($emailapp, "RE: Réclamation NSIA", $data);
            
            flash("La réclamation est traité avec succès. ")->success();
            TraceController::setTrace("La réclamation est traité avec succès.",session("utilisateur")->idUser);
            return redirect('/listreclamation');
        }
    }
    
    public function exportreclamation(){
        if (!in_array("export_recl", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $list = DB::table('reclamations')
                ->select('codeCom as codeCom', 'nomCom', 'prenomCom', 'police', 'quittance', 'client', 
                'typerecl', 'librecl', 'etatnsia', 'obsnsia', 'reclamations.created_at as created_at', 'dateresponsensia', 'usernsia')
                ->join("commerciauxes", "commerciauxes.codeCom", "=", "reclamations.apporteur");
            if(request('seach') != ""){
                $list = $list->where('apporteur', 'like', request('seach').'%')
                    ->orwhere('police', 'like', request('seach').'%')
                    ->orwhere('client', 'like', request('seach').'%')
                    ->orwhere('quittance', 'like', request('seach').'%')
                    ->orwhere('commerciauxes.nomCom', 'like', request('seach').'%')
                    ->orwhere('commerciauxes.prenomCom', 'like', request('seach').'%');
            }
            
            if(request('type') != "")
                $list = $list->orwhere('typerecl', 'like', request('type').'%');
               
            if(request('type') != "") 
                $list = $list->orwhere('etatnsia', 'like', request('etat').'%');
                
            $list = $list->orderby("id", "DESC")->get();
            
            $i = 0;
            $tabl[$i]["app"] = "";
            $tabl[$i]["nomapp"] = "";
            $tabl[$i]["police"] = "";
            $tabl[$i]["quittance"] = "";
            $tabl[$i]["client"] = "";
            $tabl[$i]["typerecl"] = "";
            $tabl[$i]["recl"] = "";
            $tabl[$i]["etat"] = "";
            $tabl[$i]["obs"] = "";
            $tabl[$i]["decla"] = "";
            $tabl[$i]["dateretour"] = "";
            $tabl[$i]["usernsia"] = "";
            $i++;
            foreach ($list as $item){
                $tabl[$i]["app"] = $item->codeCom;
                $tabl[$i]["nomapp"] = $item->nomCom.' '.$item->prenomCom;
                $tabl[$i]["police"] = $item->police;
                $tabl[$i]["quittance"] = $item->quittance;
                $tabl[$i]["client"] = $item->client;
                $tabl[$i]["typerecl"] = $item->typerecl;
                $tabl[$i]["recl"] = $item->librecl;
                if($item->etatnsia != 0) 
                $tabl[$i]["etat"] = 'traité'; else $tabl[$i]["etat"] = 'Non traité';
                $tabl[$i]["obs"] = $item->obsnsia;
                $tabl[$i]["decla"] = $item->created_at;
                $tabl[$i]["dateretour"] = $item->dateresponsensia;
                $tabl[$i]["usernsia"] = InterfaceServiceProvider::LibelleUserRecl($item->usernsia);
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allrecl', $autre);
            TraceController::setTrace("Vous avez exporté les réclamations en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportReclamation, 'Export_Reclamation_'.date('Y-m-d-h-i-s').'.xlsx');
        }
        
    }
    
    
}
