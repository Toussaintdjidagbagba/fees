<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\Model\Periodicite;
use App\Http\Model\Trace;

class PeriodiciteController extends Controller
{
    //
    public function listperiodicite() 
    {
        $list = AllTable::table('periodicites')->paginate(20);
        return view('admin.listperiodicite', compact('list'));
    }

    public function addperiodicite(Request $request)
    {
        if (!in_array("add_period", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            
            if (PeriodiciteController::Existeperiodicite(htmlspecialchars(trim($request->code)))) {
                flash("La Periodicité que vous voulez ajouter existe déjà!! ")->error();
                return Back();
            }
            else{
                $add = new Periodicite();
                $add->libelle =  htmlspecialchars(trim($request->lib));
                $add->user_action = session("utilisateur")->idUser;
                $add->action_save = 's';
                $add->save();
    
                flash("La Periodicité est enregistré avec succès. ")->success();
                TraceController::setTrace(
                    'Vous avez enregistré la Periodicité '.htmlspecialchars(trim($request->lib)).'.',
                    session("utilisateur")->idUser);
    
                return Back();
            }
        }
    }

    public function deleteperiodicite(Request $request)
    {
        if (!in_array("delete_period", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            //Periodicite::where('idPeriodicite', request('id'))->update(['statut' =>  "sup"]);
            $occurence = json_encode(Periodicite::where('idPeriodicite', request('id'))->first());
            $addt = new Trace();
            $addt->libelleTrace = "Périodicité supprimé : ".$occurence;
            $addt->user_action = session("utilisateur")->idUser;
            $addt->save();
            Periodicite::where('idPeriodicite', request('id'))->delete();
            $info = "La Periodicité est supprimé avec succès."; flash($info); return Back();
        }
    }

    public function getmodifperiodicite(Request $request)
    {
        
        if (!in_array("update_period", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = Periodicite::where('idPeriodicite', $request->id)->first();
            return view('admin.modifperiodicite', compact('info'));
        }
    }

    public function modifperiodicite(Request $request)
    {
        $request->validate([
                'libelle' => 'required|string', 
            ]);

        Periodicite::where('idPeriodicite', request('id'))->update(
                [
                    'libelle' =>  htmlspecialchars(trim($request->libelle)),
                    'user_action' => session("utilisateur")->idUser,
                ]);
        TraceController::setTrace(
                'Vous avez modifié la Periodicité '.htmlspecialchars(trim($request->libelle)).'.',
                session("utilisateur")->idUser);
        flash("La Periodicité est modifié avec succès. ")->success();
        return redirect('/listperiodicite');
        
    }

    public static function Existeperiodicite($libelle){
        $periodicite = Periodicite::where('idPeriodicite', $libelle)->first();
        if (isset($periodicite) && $periodicite->idPeriodicite!= 0) return true; else return false;
    }
}
