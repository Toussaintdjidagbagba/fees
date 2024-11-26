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

class InspectionController extends Controller
{ 
    // 
    public function listins() 
    {
        $list = DB::table('hierarchies')
                ->where("statut", "!=", "sup")
                ->where("managerH", "!=", 0)
                ->whereNotIn('structureH', trans('var.notins'));
        $listcat = DB::table('niveaux')->where("statut", "!=", "sup")
                ->whereNotIn('codeNiveau', trans('var.notins'))->get();
        
        $listmag = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->where('Niveau', trans('var.magins'))->get();

        $listsup = DB::table('hierarchies')->where("statut", "!=", "sup")
                ->where('managerH', "!=", 0)
                ->where('structureH','RG')->get();
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
                return view("ins.search", compact('list', 'listsup', 'listmag', 'com_manageur'));
            }else{
                $list = $list->paginate(20);
                return view("ins.search", compact('list', 'listsup', 'listmag', 'com_manageur'));
            }
        }
        $list = $list->paginate(20);

        return view('ins.listins', compact('list', 'listcat', 'listsup', 'listmag', 'com_manageur'));
    }

    public function addins(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codeh' => 'required|string',
            'manageur' => 'numeric',
            'cat' => 'required|string',
            'sup' => 'required|string',
        ]);

        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-ADDINS-".date('ymdhis');
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
                    flash(trans('flash.managernull'))->error();
                    return Back();
                }

                // Vérification de l'existance de l'inspection
                if (VerificationController::ExisteInspection($request->codeh, $request->manageur, $request->cat) || VerificationController::ExisteInspectionSansManageur($request->codeh, $request->cat)) {
                    flash(trans('flash.addinserr'))->error();
                    return Back();
                }
                else{

                    // Enregrement de l'inspection
                    $add = new Hierarchie(); 
                    $add->codeH = htmlspecialchars(trim($request->codeh));
                    $add->libelleH =  htmlspecialchars(trim($request->lib));
                    $add->villeH =  htmlspecialchars(trim($request->ville));
                    $add->managerH =  $request->manageur;
                    $add->structureH =  htmlspecialchars(trim($request->cat));
                    $add->superieurH =  htmlspecialchars(trim($request->sup));
                    $add->user_action = session("utilisateur")->idUser;
                    $add->save();

                    $codeins = htmlspecialchars(trim($request->codeh));

                    // Recherche du supérieure du supérieure
                    $supsup = Hierarchie::where('codeH', $codeins)->first()->superieurH;

                    $codeancienequipe = Hierarchie::where('managerH', $request->manageur)
                                        ->where('structureH', "CEQP")
                                        ->first()->codeH;

                    // Mise à jour du commercial
                    Commerciaux::where('codeCom', $request->manageur)->update([
                        'Niveau' => htmlspecialchars(trim($request->cat)),
                        'codeEquipe' =>  "0000",
                        'codeInspection' => $codeins,
                        'codeRegion' =>  $supsup,
                    ]);

                    // Dans ce cas, le nouveau manageur n'est plus chef d'équipe
                    Hierarchie::where('codeH', $codeancienequipe)->where('structureH', "CEQP")->update([
                        'managerH' => 0,
                    ]);

                    // Enregistrer la trace de l'opération

                    // Message de retour
                    flash(trans('flash.addinssucces'))->success();
                    return Back();
                }
        }else{
                flash('Aucun fichier importé')->error();
                return Back();
        }
    }

    public function deleteins(Request $request)
    {
        $occurence = json_encode(Hierarchie::where('codeH', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Inspection supprimer : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        Hierarchie::where('codeH', request('id'))->delete();
        flash(trans('flash.deleteinssucces')); return Back();
    }

    public function getmodifins(Request $request)
    {
        $listcat = DB::table('niveaux')->where("statut", "!=", "sup")
                ->whereNotIn('codeNiveau', ['CEQP', 'CONS', "RG", 'DEV'])->get();
        
        $listmag = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->where('Niveau','CEQP')->get();
        $listsup = DB::table('hierarchies')->where("statut", "!=", "sup")
                ->where('managerH', "!=", 0)
                ->where('structureH','RG')->get();
        $infoins = DB::table('hierarchies')->where('codeH', $request->id)->first();
        return view('ins.modifins', compact('infoins', 'listcat', 'listsup', 'listmag'));
    }
    
    public function setmodifins(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manageur' => 'numeric',
            'cat' => 'required|string',
            'sup' => 'required|string',
        ]);
        
                if($validator->fails()){
                    $err ='<ul>';
                    foreach($validator->errors()->all() as $e){$err .="<li>$e</li>";}
                    $err .='<ul>';
                    flash($err)->error();
                    return Back();
                }

                if($request->manageur == null){
                    flash(trans('flash.managernull'))->error();
                    return Back();
                }
                

        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-MODIFIEINSPECTION-".date('ymdhis');
                $namefile = str_replace("/", "", $referenceNote).".pdf";
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
                $addA->existantSup = Hierarchie::where('codeH', $request->codeh)->first()->superieurH;
                $addA->nouveauSup = $request->sup;
                $addA->structure = htmlspecialchars(trim($request->cat));
                $addA->user_action = session("utilisateur")->idUser;
                $addA->save();

                
                $codeancienmanag = Hierarchie::where('codeH', $request->codeh)->first()->managerH;
                
                Hierarchie::where('codeH', $request->codeh)->update(
                        [
                            'libelleH' =>  htmlspecialchars(trim($request->lib)),
                            'villeH' =>  htmlspecialchars(trim($request->ville)),
                            'managerH' =>  $request->manageur,
                            'structureH' =>  htmlspecialchars(trim($request->cat)),
                            'superieurH' =>  htmlspecialchars(trim($request->sup)),
                            'user_action' => session("utilisateur")->idUser,
                        ]);
                    // Mise à jour du commercial
                    Commerciaux::where('codeCom', $request->manageur)->update(['Niveau' => htmlspecialchars(trim($request->cat)), 
                        'codeEquipe' => "",
                        'codeInspection' => $request->codeh,
                        'codeRegion' => Hierarchie::where('codeH', $request->codeh)->first()->superieurH, 
                        "codeCD" => Hierarchie::where('codeH', Hierarchie::where('codeH', $request->codeh)->first()->superieurH)->first()->superieurH ]);

                    // Rétrograder au rang de conseiller l'ancien manager
                if($codeancienmanag != $request->manageur)
                    Commerciaux::where('codeCom', $codeancienmanag)->update(['Niveau' => "CONS", "codeEquipe" => "", "codeInspection" => "", "codeRegion" => "", "codeCD" => "" ]);

                flash(trans('flash.modifinssucces'))->success();
                return Back();
        }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }
        
    }
    
    public function getmutationins(Request $request){
        $listcat = DB::table('niveaux')->where("statut", "!=", "sup")
                ->whereNotIn('codeNiveau', ['CEQP', 'CONS', "RG", 'DEV'])->get();
        
        $listmag = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->where('Niveau','CEQP')->get();
        $listmagins = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->where('Niveau','INS')->orderby("created_at", "desc")->get();
        $listsup = DB::table('hierarchies')->where("statut", "!=", "sup")
                ->where('managerH', "!=", 0)
                ->where('structureH','RG')->get();
        $infoins = DB::table('hierarchies')->where('codeH', $request->id)->first();
        return view('ins.mutation', compact('infoins', 'listcat', 'listsup', 'listmag', 'listmagins'));
    }
    
    public function setmutationins(Request $request)
    {
               
        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-MODIFIEINSPECTION-".date('ymdhis');
                $namefile = str_replace("/", "", $referenceNote).".pdf";
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
                $addA->structure = htmlspecialchars(trim($request->cat));
                $addA->user_action = session("utilisateur")->idUser;
                $addA->save();
                
                $manageurdestnew = Hierarchie::where('codeH', $request->codeh)->first()->managerH;
                
                // Pour le nouveau manageur source
                Hierarchie::where('codeH', $request->codeh)->update(
                        [
                            'villeH' =>  htmlspecialchars(trim($request->ville)),
                            'managerH' =>  $request->newmanageur,
                            'structureH' =>  htmlspecialchars(trim($request->cat)),
                            'superieurH' =>  htmlspecialchars(trim($request->sup)),
                            'user_action' => session("utilisateur")->idUser,
                        ]);
                
                // Mise à jour du commercial
                Commerciaux::where('codeCom', $request->newmanageur)->update(['Niveau' => htmlspecialchars(trim($request->cat)),
                        'codeEquipe' => "",
                        'codeInspection' => $request->codeh,
                        'codeRegion' => Hierarchie::where('codeH', $request->codeh)->first()->superieurH, 
                        "codeCD" => Hierarchie::where('codeH', Hierarchie::where('codeH', $request->codeh)->first()->superieurH)->first()->superieurH ]);
                
                // Pour le manageur muté
                $codeancienmanagdesthie = Hierarchie::where('managerH', $request->ancienrempmanageur)->first()->codeH;
                Hierarchie::where('codeH', $codeancienmanagdesthie)->update(
                        [
                            'managerH' =>  $manageurdestnew,
                            'user_action' => session("utilisateur")->idUser,
                        ]);
                // Mise à jour du commercial
                Commerciaux::where('codeCom', $manageurdestnew)->update(['Niveau' => htmlspecialchars(trim($request->cat)), 
                        'codeInspection' => $codeancienmanagdesthie,
                        'codeRegion' => Hierarchie::where('codeH', $codeancienmanagdesthie)->first()->superieurH, 
                        "codeCD" => Hierarchie::where('codeH', Hierarchie::where('codeH', $codeancienmanagdesthie)->first()->superieurH)->first()->superieurH ]);
                
                // Rétrograder l'ancien destinataire au rang de conseiller
                Commerciaux::where('codeCom', $request->ancienrempmanageur)->update(['Niveau' => "CONS", "codeEquipe" => "", "codeInspection" => "", "codeRegion" => "", "codeCD" => "" ]);

                flash(trans('flash.modifinssucces'))->success();
                return Back();
        }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }
        
    }

    

}
