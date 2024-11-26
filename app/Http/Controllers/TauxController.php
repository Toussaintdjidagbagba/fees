<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\Model\TauxNiveau;
use App\Http\Model\Trace;
use DB;

class TauxController extends Controller
{
    //
    public static function getlisttaux(){ 

        $list = AllTable::table('taux_niveaus')->where('Produit', request('prod'))->orderBy('created_at', 'dsc')->get();
        $produit = request('prod'); // Produit auquel sera appliqué le taux
        $allNiveau = AllTable::gettable('niveaux');
        $allPeriodicite = AllTable::gettable('periodicites');
        if (!isset($allNiveau[0]->codeNiveau)) {
            flash("Pas de Niveau enregistré !!! ")->error();
            return Back(); 
        }
        if (!isset($allPeriodicite[0]->idPeriodicite)) {
            flash("Pas de Périodicité enregistré !!! ")->error();
            return Back();
        }
            
        return view('admin.taux', compact('list','produit', 'allNiveau', 'allPeriodicite'));
    }

    public function addtaux(Request $request)
    {
        if(request('niv') == "0" || request('periodicite') == "0")
        {
            flash("Informations manquantes !!! ")->error();
            return Back();
        }

        if (TauxController::ExisteTaux(htmlspecialchars(trim($request->niv)),
            htmlspecialchars(trim($request->periodicite)), request('prod'),
            request('schema'), $request->dureecontratmin, $request->dureecontratmax, $request->duree, $request->agent, $request->quittance )) {
            flash("Un Taux correspond déjà à ces informations renseigner.")->error();
            return Back();
        }
        else{
            $dureenapplication = $request->duree;
            $duremax = $request->dureecontratmax;
            if($dureenapplication == "") $dureenapplication = -1;
            $add = new TauxNiveau();
            $add->Produit = $request->prod;
            $add->Niveau = htmlspecialchars(trim($request->niv));
            $add->Periodicite =  $request->periodicite;
            $add->dureenapplication = $dureenapplication;
            $add->dureecontratmin = $request->dureecontratmin;
            $add->dureecontratmax = $duremax;
            $add->tauxCommissionnement = $request->taux;
            $add->pourcentage = $request->pourcentage;
            $add->Agent = $request->agent;
            $add->police = $request->police;
            $add->comfixe = $request->fixecom;
            $add->conv = $request->convent;
            $add->acces = $request->access;
            $add->basemin = $request->combasemin;
            $add->basemax = $request->combasemax;
            $add->Quittance = $request->quittance;
            $add->Schema = request('schema');
            $add->statad = $request->statad;
            
            $add->user_action = session("utilisateur")->idUser;
            $add->save();

            flash("Taux enregistré avec succès. ")->success();
            return Back();
        }
    }

    public function deletetaux(Request $request)
    {
        //TauxNiveau::where('idTauxNiveau', request('id'))->update(['statut' =>  "sup"]);
        $occurence = json_encode(TauxNiveau::where('idTauxNiveau', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Taux supprimé : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        TauxNiveau::where('idTauxNiveau', request('id'))->delete();
        $info = "Taux supprimé avec succès."; flash($info); return Back();
    }

    public function getmodiftaux(Request $request)
    {
        $info = TauxNiveau::where('idTauxNiveau', $request->id)->first();
        //$list = Menu::get();
        $allNiveau = AllTable::gettable('niveaux');
        $allPeriodicite = AllTable::gettable('periodicites');
        return view('admin.modiftaux', compact('info', 'allPeriodicite', 'allNiveau'));
    }

    public function modiftaux(Request $request)
    {
        $occurence = json_encode(TauxNiveau::where('idTauxNiveau', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Taux ancien : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        
        TauxNiveau::where('idTauxNiveau', request('id'))->update(
                [
                    'Niveau' =>  htmlspecialchars(trim($request->niv)),
                    'Periodicite' =>  htmlspecialchars(trim($request->periodicite)),
                    'tauxCommissionnement' =>  htmlspecialchars(trim($request->taux)),
                    'dureenapplication' =>  $request->duree,
                    'Schema' =>  $request->schema,
                    'pourcentage' =>  $request->pourc,
                    'dureecontratmax' =>  $request->dureemax,
                    'dureecontratmin' => $request->dureemin,
                    'Agent' => $request->agent,
                    'Quittance' => $request->quitt,
                    'police' => $request->police,
                    'conv' => $request->convent,
                    'basemin' => $request->combasemin,
                    'basemax' => $request->combasemax,
                    'comfixe' => $request->fixecom,
                    'acces' => $request->access,
                    'statad' => $request->statad,
                    'user_action' => session("utilisateur")->idUser,
                ]);
        $occurence = json_encode(TauxNiveau::where('idTauxNiveau', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Taux nouveau : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        
        $prod = TauxNiveau::where('idTauxNiveau', request('id'))->first()->Produit;
        flash("Taux modifié avec succès. ")->success();
        return redirect('/add-Taux-'.$prod);
    }

    public static function ExisteTaux($niv, $periodicite, $prod, $schema, $min, $max, $dureenapplication, $agent, $quittance){
        $exit = DB::table('taux_niveaus')
            ->where('Niveau', $niv)
            ->where('Periodicite', $periodicite)
            ->where('Produit', $prod)
            ->where('Schema', $schema)
            ->where('dureecontratmin', $min)
            ->where('dureecontratmax', $max)
            ->where('Quittance', $quittance)
            ->where('Agent', $agent)
            ->first();

        if (isset($exit) && $exit->idTauxNiveau != 0)
            if ($dureenapplication >= 13 && $schema == "ANCIEN")
                return false;
            else
                return true;
        else
            return false;
    }
}
