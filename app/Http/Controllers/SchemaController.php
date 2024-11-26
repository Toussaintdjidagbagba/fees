<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\Model\Schema;
use App\Http\Model\Produit;
use App\Http\Model\Trace;
use DB;

class SchemaController extends Controller
{ 
    //
    public static function getlistbareme(){
        $list = AllTable::table('schemas')->paginate(20);
        $prodall = DB::table('produits')
                    ->select(DB::raw('count(*) as num, codeProduit'))
                    ->where('codeProduit', '<>', "")->groupBy('codeProduit')->get();
        
        return view('admin.bareme', compact('list', 'prodall'));
    }

    public function addbareme(Request $request)
    {
        if (SchemaController::ExisteSchema(htmlspecialchars(trim($request->lib)))) {
            flash("Un Schéma correspond déjà à ces informations renseigner.")->error();
            return Back();
        }
        else{
            $add = new Schema(); 
            $add->libelle = htmlspecialchars(trim($request->lib));
            $add->tauxAIB = htmlspecialchars(trim($request->tauxaib));
            $add->tauxNonAIB = htmlspecialchars(trim($request->tauxnonaib));
            $add->Produit = htmlspecialchars(trim($request->prod));
            $add->save();

            flash("Schéma enregistré avec succès. ")->success();
            return Back();
        }
    }

    public function deletebareme(Request $request)
    {
        //Schema::where('idSchema', request('id'))->update(['statut' =>  "sup"]);
        

        $occurence = json_encode(Schema::where('idSchema', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Schema supprimé : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        Schema::where('idSchema', request('id'))->delete();
        $info = "Schéma supprimé avec succès."; flash($info); return Back();
    }

    public function getmodifbareme(Request $request)
    {
        $prodall = DB::table('produits')
                    ->select(DB::raw('count(*) as num, codeProduit'))
                    ->where('codeProduit', '<>', "")->groupBy('codeProduit')->get();
        $info = Schema::where('idSchema', $request->id)->first();
        return view('admin.modifschema', compact('prodall', 'info'));
    }

    public function modifbareme(Request $request)
    {
        Schema::where('idSchema', request('id'))->update(
                [
                    'libelle' =>  htmlspecialchars(trim($request->libelle)),
                    'tauxAIB' =>  $request->tauxaib,
                    'tauxNonAIB' =>  $request->tauxnonaib,
                    'Produit' => $request->prod,
                ]);
        flash("Schéma modifié avec succès. ")->success();
        return redirect('/listbareme');
        
    }

    public static function ExisteSchema($libelle){
        $niveau = Schema::where('libelle', $libelle)->first();
        if (isset($niveau) && $niveau->libelle!= 0) return true; else return false;
    }
}
