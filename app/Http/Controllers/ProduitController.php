<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\Model\Produit;
use App\Http\Model\Trace;

class ProduitController extends Controller
{
    //
    public function listprod()
    {
        $list = AllTable::table('produits');
        if(request('rec') == 1){
            if(request('check') != "" && request('check') != null){
                $list = $list->where('idProduit', 'like', '%'.request('check').'%')
                ->orwhere('libelle', 'like', '%'.request('check').'%')->paginate(20);
                return view("produit.search", compact('list'));
            }else{
                $list = $list->paginate(20);
                return view("produit.search", compact('list'));
            }
        }

        $list = $list->paginate(20);

        return view('produit.listprod', compact('list'));
    } 

    public function addprod(Request $request)
    {
        
        if (ProduitController::ExisteProduit($request->num)) {
            flash("Le Produit que vous voulez ajouter existe déjà!! ")->error();
            return Back();
        }
        else{
            $add = new Produit();
            $add->idProduit = strtoupper(htmlspecialchars(trim($request->num)));
            $add->libelle =  htmlspecialchars(trim($request->lib));
            $add->codeProduit =  strtolower(htmlspecialchars(trim($request->code)));
            $add->user_action = session("utilisateur")->idUser;
            $add->save();

            flash("Produit est enregistré avec succès. ")->success();
            return Back();
        }
    }

    public function deleteprod(Request $request)
    {
        //Produit::where('idProduit', request('id'))->update(['statut' =>  "sup"]);
        $occurence = json_encode(Produit::where('idProduit', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Produit supprimé : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        Produit::where('idProduit', request('id'))->delete();

        // Le paramétrage du produit aussi à supprimer
        $info = "Le Produit est supprimé avec succès."; flash($info); return Back();
    }

    public function getmodifprod(Request $request)
    {
        $info = Produit::where('idProduit', request('id'))->first();
        return view('produit.modifproduit', compact('info'));
    }

    public function modifprod(Request $request)
    {
        $request->validate([
                'libelle' => 'required|string', 
            ]);

        Produit::where('idProduit', request('id'))->update(
                [
                    'libelle' =>  htmlspecialchars(trim($request->libelle)),
                    'user_action' => session("utilisateur")->idUser,
                ]);
        flash("Le Produit est modifié avec succès. ")->success();
        return redirect('/listproduit');
        
    }

    public static function ExisteProduit($libelle){
        $prod = Produit::where('idProduit', $libelle)->first();
        if (isset($prod) && $prod->idProduit != 0) return true; else return false;
    }

}
