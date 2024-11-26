<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\FonctionControllers\VerificationController;
use App\Http\Model\Hierarchie;
use App\Http\Model\Commerciaux;
use App\Http\Model\Trace;
use App\Http\Model\Avenant;
use DB;
use Validator;

class RegionController extends Controller
{
    //
    public function listrg()  
    {
        $list = DB::table('hierarchies')->where('structureH', "RG");
        
        $listmag = DB::table('commerciauxes')->whereIn('Niveau', trans('var.ins'))->get();
        
        $listsup = DB::table('hierarchies')->where('managerH', "!=", 0)->where('structureH','CD')->get();

        $com_manageur = "";
        //dd($request->mag);
        if (isset($request->mag) && request('mag') != null) {
            //flash(trans('flash.complete'));
            $com_manageur = AllTable::table("commerciauxes")->where("codeCom", request('mag'))->first();
        }

        if(request('rec') == 1){
            if(request('check') != "" && request('check') != null){
                $list = $list->where('codeH', 'like', '%'.request('check').'%')
                ->orwhere('structureH', 'like', '%'.request('check').'%')
                ->orwhere('libelleH', 'like', '%'.request('check').'%')->paginate(20);
                return view("rg.search", compact('list', 'listmag', 'com_manageur'));
            }else{
                $list = $list->paginate(20);
                return view("rg.search", compact('list', 'listmag', 'com_manageur'));
            }
        }
        $list = $list->paginate(20);

        return view('rg.listrg', compact('list','listmag', 'listsup', 'com_manageur'));
    }

    public function addrg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codeh' => 'required|string',
            'manageur' => 'numeric',
        ]);

        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-ADDRG-".date('ymdhis');
                $namefile = $referenceNote.".pdf";
                $upload = "document/upload/";
                $request->file('note')->move($upload, $namefile);
                
                $path = $upload.$namefile;
                $desc = htmlspecialchars(trim($request->desc));
                $ref = htmlspecialchars(trim($request->ref));
                $dateeffet = htmlspecialchars(trim($request->dateeffet));

                // Enregistrement du motif de la modification de l'inspection existante
                $addA = new Avenant();
                $addA->codeHerarchieModifier = $request->codeh; // La ligne d'occurence qui a connu de changement
                $addA->path = $path;
                $addA->referenceNoteSave = $referenceNote;
                $addA->description = $desc;
                $addA->reference = $ref;
                $addA->dateeffet = $dateeffet;
                $addA->existantManageur = "";
                $addA->nouveauManageur = "";
                $addA->structure = "";
                $addA->user_action = session("utilisateur")->idUser;
                $addA->save();

            if($validator->fails()){
                $err ='<ul>';
                foreach($validator->errors()->all() as $e){$err .="<li>$e</li>";}
                $err .='<ul>';
                flash($err)->error();
                return Back();
            }

            if($request->manageur == null){
                flash(trans('flash.managerrgnull'))->error();
                return Back();
            }

            // Vérification de l'existance de l'inspection
            if (VerificationController::ExisteInspection($request->codeh, $request->manageur, "RG")) {
                flash(trans('flash.addinserrg'))->error();
                return Back();
            }
            else{
                $catancien = Hierarchie::where('managerH', $request->manageur)->first()->structureH;
                // Enregrement de l'inspection
                $add = new Hierarchie();
                $add->codeH = htmlspecialchars(trim($request->codeh));
                $add->libelleH =  htmlspecialchars(trim($request->lib));
                $add->villeH =  htmlspecialchars(trim($request->ville));
                $add->managerH =  $request->manageur;
                $add->structureH =  "RG";
                $add->superieurH =  htmlspecialchars(trim($request->sup));
                $add->user_action = session("utilisateur")->idUser;
                $add->save();

                $coderg = htmlspecialchars(trim($request->codeh));

                $codeancienins = Hierarchie::where('managerH', $request->manageur)->where('structureH', $catancien)->first()->codeH;

                // Mise à jour du commercial
                Commerciaux::where('codeCom', $request->manageur)->update([
                    'Niveau' => "RG",
                    'codeEquipe' =>  "",
                    'codeInspection' => "",
                    'codeRegion' => $coderg,
                ]);

                // Dans ce cas, le nouveau manageur n'est plus chef d'une inspection
                Hierarchie::where('codeH', $codeancienins)->where('structureH', $catancien)->update([
                    'managerH' => 0,
                ]);

                // Enregistrer la trace de l'opération

                // Message de retour
                flash(trans('flash.addrgsucces'))->success();
                return Back();
            }

        }else{
                flash('Aucun fichier importé')->error();
                return Back();
        }
    }

    public function deleterg(Request $request)
    {
        $occurence = json_encode(Hierarchie::where('codeH', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Région supprimé : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        Hierarchie::where('codeH', request('id'))->delete();
        flash(trans('flash.deletergsucces')); return Back();
    }

    public function getmodifrg(Request $request)
    {
        
        $listmag = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->whereIn('Niveau',trans('var.ins'))->get();
        $listsup = DB::table('hierarchies')->where('managerH', "!=", 0)
                ->where('structureH','CD')->get();
        $inforg = DB::table('hierarchies')->where('codeH', $request->id)->first();
        return view('rg.modifrg', compact('inforg', 'listmag', 'listsup'));
    }

    public function setmodifrg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manageur' => 'numeric',
        ]);

        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-MODIFIEINSPECTION-".date('ymdhis');
                $namefile = $referenceNote.".pdf";
                $upload = "document/upload/";
                $request->file('note')->move($upload, $namefile);
                
                $path = $upload.$namefile;
                $desc = htmlspecialchars(trim($request->desc));
                $ref = htmlspecialchars(trim($request->ref));
                $dateeffet = htmlspecialchars(trim($request->dateeffet));

                // Enregistrement du motif de la modification de l'inspection existante
                $addA = new Avenant();
                $addA->codeHerarchieModifier = $request->codeh; // La ligne d'occurence qui a connu de changement
                $addA->path = $path;
                $addA->referenceNoteSave = $referenceNote;
                $addA->description = $desc;
                $addA->reference = $ref;
                $addA->dateeffet = $dateeffet;
                $addA->existantManageur = Hierarchie::where('codeH', $request->codeh)->first()->managerH;
                $addA->nouveauManageur = $request->manageur;
                $addA->structure = "RG";
                $addA->user_action = session("utilisateur")->idUser;
                $addA->save();

                if($validator->fails()){
                    $err ='<ul>';
                    foreach($validator->errors()->all() as $e){$err .="<li>$e</li>";}
                    $err .='<ul>';
                    flash($err)->error();
                    return Back();
                }

                if($request->manageur == null){
                    flash(trans('flash.managerrgnull'))->error();
                    return Back();
                }
                $codeancienmanag = Hierarchie::where('codeH', $request->codeh)->first()->managerH;
                Hierarchie::where('codeH', $request->codeh)->update(
                        [
                            'libelleH' =>  htmlspecialchars(trim($request->lib)),
                            'villeH' =>  htmlspecialchars(trim($request->ville)),
                            'managerH' =>  $request->manageur,
                            'structureH' => "RG",
                            'superieurH' => htmlspecialchars(trim($request->sup)),
                            'user_action' => session("utilisateur")->idUser,
                        ]);
                    // Mise à jour du commercial
                    Commerciaux::where('codeCom', $request->manageur)->update(['Niveau' => "RG" ]);

                    // Enregistrer la trace de l'opération
                if($codeancienmanag != $request->manageur)
                    Commerciaux::where('codeCom', $codeancienmanag)->update(['Niveau' => "CONS", "codeEquipe" => "", "codeInspection" => "", "codeRegion" =>"" ]);

                 /* Mise à jour de la branche
                    Commerciaux::where('codeInspection', $ins->codeInspection)->update([
                        'codeRegion' => $ins->codeRegion,
                        'codeCD' => $ins->codeCD
                    ]);
                    */

                flash(trans('flash.modifrgsucces'))->success();
                return Back();
        }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }
        
    }
}
