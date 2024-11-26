<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\Model\Niveau;
use App\Http\Model\Trace;

class NiveauController extends Controller
{
    // 
    public function listniveau()
    {
        $list = AllTable::table('niveaux');

        if(request('rec') == 1){
            if(request('check') != "" && request('check') != null){
                $list = $list->where('libelleNiveau', 'like', '%'.request('check').'%')
                ->orwhere('codeNiveau', 'like', '%'.request('check').'%')->paginate(20);
                return view("niveau.search", compact('list'));
            }else{
                $list = $list->paginate(20);
                return view("niveau.search", compact('list'));
            }
        }

        $list = $list->paginate(20);
        return view('niveau.listniveau', compact('list'));
    }

    public function addniveau(Request $request)
    {
        if (!in_array("add_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            if (NiveauController::ExisteNiveau(htmlspecialchars(trim($request->code)))) {
                flash("Le Niveau que vous voulez ajouter existe déjà!! ")->error();
                return Back();
            }
            else{
                $add = new Niveau();
                $add->codeNiveau = strtoupper(htmlspecialchars(trim($request->code)));
                $add->libelleNiveau =  htmlspecialchars(trim($request->lib));
                $add->user_action = session("utilisateur")->idUser;
                $add->save();

                flash("Le Niveau est enregistré avec succès. ")->success();
                TraceController::setTrace(
                "Vous avez enregistré le niveau ".$request->lib." .",
                session("utilisateur")->idUser);
                return Back();
            }
        }
    }

    public function deleteniveau(Request $request)
    {
        if (!in_array("delete_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            //Niveau::where('codeNiveau', request('id'))->update(['statut' =>  "sup"]);
            $occurence = json_encode(Niveau::where('codeNiveau', request('id'))->first());
            $addt = new Trace();
            $addt->libelleTrace = "Niveau supprimé : ".$occurence;
            $addt->user_action = session("utilisateur")->idUser;
            $addt->save();
            Niveau::where('codeNiveau', request('id'))->delete();
            $info = "Le Niveau est supprimé avec succès."; flash($info); return Back();
        }
    }

    public function getmodifniveau(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = Niveau::where('codeNiveau', request('id'))->first();
            return view('niveau.modifniveau', compact('info'));
        }
    }

    public function modifniveau(Request $request)
    {
        if (!in_array("update_niv", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $request->validate([
                    'libelle' => 'required|string', 
                ]);

            Niveau::where('codeNiveau', request('id'))->update(
                    [
                        'libelleNiveau' =>  htmlspecialchars(trim($request->libelle)),
                        'user_action' => session("utilisateur")->idUser,
                    ]);
            flash("Le Niveau est modifié avec succès. ")->success();
            TraceController::setTrace(
                "Vous avez modifié le niveau ".$request->libelle." .",
                session("utilisateur")->idUser);
            return redirect('/listniveaux');
        }
    }

    public static function ExisteNiveau($libelle){
        $niveau = Niveau::where('codeNiveau', $libelle)->first();
        if (isset($niveau) && $niveau->codeNiveau!= 0) return true; else return false;
    }
}
