<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\FonctionControllers\VerificationController;
use App\Http\Model\Hierarchie;
use App\Http\Model\Commerciaux;
use App\Http\Model\Trace;
use App\Http\Model\Contrat;
use App\Http\Model\Avenant;
use DB;
use Validator;

class EquipeController extends Controller
{ 
    // 
    public function listeqp(Request $request)
    {
        $list = DB::table('hierarchies')
                ->where("statut", "!=", "sup")
                ->where('structureH', trans('var.equipe'));
        
        $listmag = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->whereIn('Niveau', trans('var.magequipe'))->get();

        $listsup = DB::table('hierarchies')->where("statut", "!=", "sup")
                ->whereIn('structureH', trans('var.ins'))
                ->where('managerH', "!=", 0)
                ->get();

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
                return view("equipe.search", compact('list', 'listsup', 'listmag', 'com_manageur'));
            }else{
                $list = $list->paginate(20);
                return view("equipe.search", compact('list', 'listsup', 'listmag', 'com_manageur'));
            }
        }

        $list = $list->paginate(20);

        return view('equipe.listequipe', compact('list', 'listsup', 'listmag', 'com_manageur'));
    }

    public function addequipe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codeh' => 'required|string',
            'manageur' => 'numeric',
            'sup' => 'required|string',
        ]);

        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-ADDEQUIPE-".date('ymdhis');
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
                $addA->nouveauManageur = $request->manageur;
                $addA->structure = "CEQP";
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
                    flash(trans('flash.managereqnull'))->error();
                    return Back();
                }

                // Vérification de l'existance de l'inspection
                if (VerificationController::ExisteEquipe($request->codeh, $request->manageur, "CEQP") || VerificationController::ExisteEquipeSansManageur($request->codeh, "CEQP")) {
                    flash(trans('flash.addequipeerr'))->error();
                    return Back();
                }
                else{

                    // Enregrement de l'inspection
                    $add = new Hierarchie();
                    $add->codeH = htmlspecialchars(trim($request->codeh));
                    $add->libelleH =  htmlspecialchars(trim($request->lib));
                    $add->managerH =  $request->manageur;
                    $add->structureH =  "CEQP";
                    $add->superieurH =  htmlspecialchars(trim($request->sup));
                    $add->user_action = session("utilisateur")->idUser;
                    $add->save();

                    $codeequipe = htmlspecialchars(trim($request->codeh));

                    // Recherche du supérieure
                    $sup = Hierarchie::where('codeH', $codeequipe)->first()->superieurH;

                    // Recherche du supérieure du supérieure
                    $supsup = Hierarchie::where('codeH', $sup)->first()->superieurH;

                    // Mise à jour du commercial
                    Commerciaux::where('codeCom', $request->manageur)->update([
                        'Niveau' => "CEQP",
                        'codeEquipe' =>  $codeequipe,
                        'codeInspection' => $sup,
                        'codeRegion' =>  $supsup
                    ]);

                    // Enregistrer la trace de l'opération

                    // Message de retour
                    flash(trans('flash.addequipesucces'))->success();
                    return redirect('/listequipe');
                }
        }else{
                flash('Aucun fichier importé')->error();
                return Back();
        }

    }

    public function deleteequipe(Request $request)
    {
        $occurence = json_encode(Hierarchie::where('codeH', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Equipe supprimer : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();
        Hierarchie::where('codeH', request('id'))->delete();
        flash(trans('flash.deleteinssucces')); return Back();
    }

    public function getmodifequipe(Request $request)
    {
        $listmag = DB::table('commerciauxes')->where("statut", "!=", "sup")
                ->whereIn('Niveau', ["CEQP"])->get();
        $listsup = DB::table('hierarchies')->where("statut", "!=", "sup")
                ->where('managerH', "!=", 0)
                ->whereNotIn('structureH', trans('var.notsupequipe'))->get();
        $infoeqp = DB::table('hierarchies')->where('codeH', $request->id)->first();
        return view('equipe.modifequipe', compact('infoeqp', 'listmag', 'listsup'));
    }

    public function modifequipe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manageur' => 'numeric',
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
                    flash(trans('flash.managereqnull'))->error();
                    return Back();
                }
                
                

        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-MODIFIEEQUIPE-".date('ymdhis');
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
                $addA->existantSup = Hierarchie::where('codeH', $request->codeh)->first()->superieurH;
                $addA->nouveauSup = $request->sup;
                $addA->structure = "CEQP";
                $addA->user_action = session("utilisateur")->idUser;
                $addA->save();
			
				$codeancienmanag = Hierarchie::where('codeH', $request->codeh)->first()->managerH;
				
				$codeequipancien = $request->codeh; // code de l'équipe de l'ancien manageur à enlever
			
				//permettre à l'ancien de toujours bénéficier des ces coms 2 de son ancien équipe
			
				//all conseiller
				$allconseillerancceqp =  Commerciaux::where('codeEquipe', $codeequipancien)->where("Niveau", "CONS")->get();
				foreach($allconseillerancceqp as $consanc)
				{
					//all contrat in conseiller
					$allContrat = Contrat::where('Agent', $consanc->codeCom)->get();
					foreach($allContrat as $contratagent){
						if($contratagent->ceqp == null || $contratagent->ceqp == ""){
							$maganc = Hierarchie::where('codeH', $codeequipancien)->first()->managerH;
							$supinsanccodeh = Hierarchie::where('codeH', $codeequipancien)->first()->superieurH;
							$supinsanc = Hierarchie::where('codeH', $supinsanccodeh)->first()->managerH;
							Contrat::where('Agent', $consanc->codeCom)->update([
								"ceqp" => $maganc, 
								"ins" => $supinsanc,
							]);
						}
					}
					
				}
			
				$codeequipedunouveau = Hierarchie::where('managerH', $request->manageur)->first()->codeH; // code de l'équipe du nouveau mangeur à ajouter
				// Et au nouveau également de toujours bénéficier des coms 2 de son ancien équipe
				$allconseillernewceqp =  Commerciaux::where('codeEquipe', $codeequipedunouveau)->where("Niveau", "CONS")->get();
				foreach($allconseillernewceqp as $consnew)
				{
					//all contrat in conseiller
					$allContratnew = Contrat::where('Agent', $consnew->codeCom)->get();
					foreach($allContratnew as $contratagentnew){
						if($contratagentnew->ceqp == null || $contratagentnew->ceqp == ""){
							$magcodeH = Hierarchie::where('managerH', $request->manageur)->first()->superieurH; 
							$magequipnew = Hierarchie::where('codeH', $magcodeH)->first()->managerH;
							Contrat::where('Agent', $consnew->codeCom)->update([
								"ceqp" => $request->manageur, 
								"ins" => $magequipnew,
							]);
						}
					}
					// Dégager les anciens conseillers du nouveau venu
							Commerciaux::where('codeCom', $consnew->codeCom)->update([
								'codeEquipe' => "",
								'codeInspection' => "",
								'codeRegion' => "",
								'codeCD' => ""
							]);
				}
				
                // Mise à jour du chef d'équipe
                Hierarchie::where('codeH', $request->codeh)->update(
                        [
                            'libelleH' =>  htmlspecialchars(trim($request->lib)),
                            'managerH' =>  $request->manageur,
                            'superieurH' =>  htmlspecialchars(trim($request->sup)),
                            'user_action' => session("utilisateur")->idUser,
                        ]);

                    // Mise à jour du commercial nouveau
                    $ins = Hierarchie::where('codeH', $request->codeh)->first()->superieurH;
                    $rg = Hierarchie::where('codeH', $ins)->first()->superieurH;
                    $cd = Hierarchie::where('codeH', $rg)->first()->superieurH;
                    Commerciaux::where('codeCom', $request->manageur)->update([
                        'Niveau' => "CEQP", 
                        'codeEquipe' => $request->codeh,
                        'codeInspection' => $ins,
                        'codeRegion' => $rg,
                        'codeCD' => $cd
                    ]);

                    // Rétrograder au rang de conseiller l'ancien manager
             	if($codeancienmanag != $request->manageur)
                    Commerciaux::where('codeCom', $codeancienmanag)->update([
						'Niveau' => "CONS", 
						"codeEquipe" => "", 
						"codeInspection" => "", 
						"codeRegion" => "", 
						"codeCD" => "" ]);
			

                /*
                $codeancienmanag = Hierarchie::where('codeH', $request->codeh)->first()->managerH;
                
                Hierarchie::where('codeH', $request->codeh)->update(
                        [
                            'libelleH' =>  htmlspecialchars(trim($request->lib)),
                            'managerH' =>  $request->manageur,
                            'superieurH' =>  htmlspecialchars(trim($request->sup)),
                            'user_action' => session("utilisateur")->idUser,
                        ]);

                    // Mise à jour du commercial
                    $ins = Hierarchie::where('codeH', $request->codeh)->first()->superieurH;
                    $rg = Hierarchie::where('codeH', $ins)->first()->superieurH;
                    $cd = Hierarchie::where('codeH', $rg)->first()->superieurH;
                    Commerciaux::where('codeCom', $request->manageur)->update([
                        'Niveau' => "CEQP", 
                        'codeEquipe' => $request->codeh,
                        'codeInspection' => $ins,
                        'codeRegion' => $rg,
                        'codeCD' => $cd
                    ]);

                    // Rétrograder au rang de conseiller l'ancien manager
                if($codeancienmanag != $request->manageur)
                    Commerciaux::where('codeCom', $codeancienmanag)->update(['Niveau' => "CONS", "codeEquipe" => "", "codeInspection" => "", "codeRegion" => "", "codeCD" => "" ]); */

                flash(trans('flash.modifequipesucces'))->success();
                return Back();
        }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }
    }

}
