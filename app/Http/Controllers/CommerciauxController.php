<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\FonctionControllers\AllTable;
use App\Http\FonctionControllers\HistoriqueController;
use App\Http\FonctionControllers\Fonction;
use App\Http\Model\Commerciaux;
use Illuminate\Support\Collection;
use App\Http\Model\Hierarchie;
use App\Http\Model\Trace;
use App\Exports\ExportErreurCommerciaux;
use DB;
use App\Http\Model\Avenant;
use App\Http\Model\Compteagent;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportCommerciaux;
use App\Http\Import\ImportExcel;
use App\Exports\ExportAmical;
use App\Exports\ExportAvance;
use App\Exports\ExportAvanceDues;
use App\Exports\ExportCarec;
use App\Exports\ExportSimla;
use App\Exports\ExportNaf;
use App\Providers\InterfaceServiceProvider;


class CommerciauxController extends Controller
{
    //
    public function __construct() 
    {
        set_time_limit(72000); 
    }
	
	// Insertion dans une coordination

    public static function getadhcoordination()
    {
        $affcom = AllTable::table('commerciauxes')->where('codeCom', request('id'))->first();
        $allcoord = AllTable::table('hierarchies')->where('structureH', "CD")->where('managerH', "!=", 0)->get();

        return view('admin.adhcoord', compact('affcom', 'allcoord'));
    }

    public function setadhcoord(Request $request)
    {
        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-ADHERECOORD-".date('ymdhis');
                $namefile = $referenceNote.".pdf";
                $upload = "document/upload/";
                $request->file('note')->move($upload, $namefile);
                
                $path = $upload.$namefile;
                $desc = htmlspecialchars(trim($request->desc));
                $ref = htmlspecialchars(trim($request->ref));
                $dateeffet = htmlspecialchars(trim($request->dateeffet));

                // Enregistrement du motif de la modification de l'inspection existante
                $addA = new Avenant();
                $addA->codeHerarchieModifier = $request->codeC; // La ligne d'occurence qui a connu de changement
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

                   Commerciaux::where('codeCom', $request->codeC)->update(
                        [
                            'codeEquipe' =>  "",
                            'codeInspection' => "",
                            'codeRegion' =>  "",
                            'codeCD' =>  $request->coordselect,
                        ]);
                   flash("Le Commercial est bien affecté dans sa nouvelle coordination.");
                   return Back();
                
            }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }

    }
    
    public static function getinfoscommerciaux($commercial){
        $dataCom = DB::table('commerciauxes')->where('codeCom', $commercial)->first();
            
            if(isset($dataCom->codeCom))
            {
                $tabcomm = array();
                
                $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $dataCom->codeEquipe)->first();
                if(isset($magEquipe->managerH))
                {
                    $codechefequipe = $magEquipe->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magEquipe->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefequipe = $data->nomCom;
                        $prenomchefequipe = $data->prenomCom;
                    }else{
                        $nomchefequipe = "";
                        $prenomchefequipe = "";
                    }
                }else{ 
                    $codechefequipe = "";
                    $nomchefequipe = "";
                    $prenomchefequipe = "";
                }
                    
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $dataCom->codeInspection)->first();
                if(isset($magIns->managerH)) {
                    $codechefins = $magIns->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magIns->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefins = $data->nomCom;
                        $prenomchefins = $data->prenomCom;
                    }else{
                        $nomchefins = "";
                        $prenomchefins = "";
                    }
                }else{
                    $codechefins = ""; 
                    $nomchefins = "";
                    $prenomchefins = "";
                } 
                
                $magRg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $dataCom->codeRegion)->first();
                if(isset($magRg->managerH)) {
                    $codechefrg = $magRg->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magRg->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefrg = $data->nomCom;
                        $prenomchefrg = $data->prenomCom;
                    }else{
                        $nomchefrg = "";
                        $prenomchefrg = "";
                    }
                }else{ 
                    $codechefrg = "";
                    $nomchefrg = "";
                    $prenomchefrg = "";
                }
                
                $magcd = DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $dataCom->codeCD)->first();
                if(isset($magcd->managerH)){ 
                    $codecd = $magcd->managerH;
                    $data = DB::table('commerciauxes')->where('codeCom', $magcd->managerH)->first();
                    if(isset($data->nomCom)){
                        $nomchefcd = $data->nomCom;
                        $prenomchefcd = $data->prenomCom;
                    }else{
                        $nomchefcd = "";
                        $prenomchefcd = "";
                    }
                }else{ 
                    $codecd = "";
                    $nomchefcd = "";
                    $prenomchefcd = "";
                }
                
                $tabdata = [
                    'Commercial' => $dataCom->codeCom, 
                    'Nom' => $dataCom->nomCom, 
                    'Prenom' => $dataCom->prenomCom,
                    'Niveau' => $dataCom->Niveau,
                    'Equipe' => $codechefequipe,
                    'NomEquipe' => $nomchefequipe, 
                    'PrenomEquipe' => $prenomchefequipe,
                    'Inspecteur' => $codechefins,
                    'NomInspecteur' => $nomchefins, 
                    'PrenomInspecteur' => $prenomchefins,
                    'RG' => $codechefrg,
                    'NomRG' => $nomchefrg, 
                    'PrenomRG' => $prenomchefrg,
                    'CD' => $codecd,
                    'NomCD' => $nomchefcd, 
                    'PrenomCD' => $prenomchefcd
                ];
                
                return $tabdata;
            }else{
                return null;
            }
    }
    
    public function setautrecom(Request $request)
    {
        //dd(DB::table('compteagents')->where('Agent', $request->codecom)->first()->AutreCommissionMoisCalculer);
        $solde = DB::table('compteagents')->where('Agent', $request->codecom)->first()->AutreCommissionMoisCalculer + $request->soldeautre;

         DB::table('compteagents')->where('Agent', $request->codecom)->update([
            'AutreCommissionMoisCalculer' => $solde,
            'libautrecom' => $request->libother
        ]);

        TraceController::setTrace(
                "Vous avez ajouté Autre Commission dont le montant est ".$request->soldeautre." au compte de l'Agent dont le code commercial est ".$request->codecom.".",
                session("utilisateur")->idUser);

        flash('Autres commissions ajoutées avec succès.');
        return Back();
    }
    
    public function setbonus(Request $request)
    {

        $solde = DB::table('compteagents')->where('Agent', $request->codecom)->first()->bonus + $request->soldebonus;

        DB::table('compteagents')->where('Agent', $request->codecom)->update([
            'bonus' => $solde,
            'libbonus' => $request->libbonu
        ]);

        TraceController::setTrace("Vous avez ajouté Bonus dont le montant est ".$request->soldebonus." au compte de l'Agent dont le code commercial est ".$request->codecom.".",
                session("utilisateur")->idUser);

        flash('Bonus ajouté avec succès.');
        return Back();
    }
    
    public function setretenue(Request $request)
    {

        $solde = DB::table('compteagents')->where('Agent', $request->codecom)->first()->retenue + $request->soldedefalque;

        DB::table('compteagents')->where('Agent', $request->codecom)->update([
            'retenue' => $solde,
            'libretenue' => $request->libretenue
        ]);

        TraceController::setTrace(
                "Vous avez ajouté de retenue dont le montant est ".$request->soldedefalque." au compte de l'Agent dont le code commercial est ".$request->codecom.".",
                session("utilisateur")->idUser);

        flash('Solde retenue ajoutée avec succès.');
        return Back();
    }
    
    public function sdac(Request $reque){
        $comm = Commerciaux::where('codeCom', $reque->codeagent)->first();
        if($comm->statut == "0"){
            Commerciaux::where('codeCom', $reque->codeagent)->update(["statut"=>"1"]);
            Compteagent::where('Agent', $reque->codeagent)->update(["statut"=>"1"]);
            flash("Vous avez désactiver le commercial ".$comm->nomCom." ".$comm->prenomCom);
        }else{
            Commerciaux::where('codeCom', $reque->codeagent)->update(["statut"=>"0"]);
            Compteagent::where('Agent', $reque->codeagent)->update(["statut"=>"0"]);
            flash("Vous avez activer le commercial ".$comm->nomCom." ".$comm->prenomCom);
        }
        return Back();
    }
    
    public function setpropreAmical(Request $request){
        Compteagent::where('Agent', $request->codeagent)->update(['montantAmical', $request->ami_propre]);
        
        $comm = Commerciaux::where('codeCom', $request->codeagent)->first();
        
        $nom = $comm->nomCom.' '.$comm->prenomCom;
        TraceController::setTrace("Vous avez définir ".$request->ami_propre." comme montant amical du commercial ".$nom, session("utilisateur")->idUser);
        
        flash("Vous avez définir ".$request->ami_propre." comme montant amical du commercial ".$nom);
        return Back();
    }
    
    public function setnafpropre(Request $request){
        Compteagent::where('Agent', $request->codeagent)->update(['naf', $request->newnaf]);
        
        $comm = Commerciaux::where('codeCom', $request->codeagent)->first();
        
        $nom = $comm->nomCom.' '.$comm->prenomCom;
        TraceController::setTrace("Vous avez définir ".$request->newnaf." comme montant naf du commercial ".$nom, session("utilisateur")->idUser);
        
        flash("Vous avez définir ".$request->newnaf." comme montant naf du commercial ".$nom);
        return Back();
    }
    
    public function setpropreCarec(Request $request){
        Compteagent::where('Agent', $request->codeagent)->update(['tauxCarec', $request->tauxcarecpropre]);
        
        $comm = Commerciaux::where('codeCom', $request->codeagent)->first();
        
        $nom = $comm->nomCom.' '.$comm->prenomCom;
        TraceController::setTrace("Vous avez définir ".$request->tauxcarecpropre." % comme taux carec du commercial ".$nom, session("utilisateur")->idUser);
        
        flash("Vous avez définir ".$request->tauxcarecpropre." % comme taux carec du commercial ".$nom);
        return Back();
    }
    
    public static function exportationavances(){
            //$list = Compteagent::where('recentrembourcer', '!=', 0)->get();
			$list = DB::table('tracecomptes')->where('moiscalculer', "03-2024")->get();
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["mont"] = 0;
            $i++;
            Session()->put('allavances', "");
            foreach ($list as $item){
                $commercial = DB::table('commerciauxes')->where('codeCom', $item->Commercial)->first();
				$comp = InterfaceServiceProvider::RecupCompteAncien($commercial->codeCom, "03-2024");
                $tabl[$i]["code"] = $commercial->codeCom;
                $tabl[$i]["nom"] = $commercial->nomCom;
                $tabl[$i]["prenom"] = $commercial->prenomCom;
                $tabl[$i]["mont"] = $comp['recentrembourcer']; //$item->recentrembourcer;
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allavances', $autre);
            TraceController::setTrace("Vous avez exporté les avances rembourser des commerciaux au cours du mois en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportAvance, 'Commerciaux_Export_Avance_Recent'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    public static function exportationavancesdues(){
            $list = Compteagent::where('avances', '!=', 0)->get();
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["equipe"] = "";
            $tabl[$i]["nomequipe"] = "";
            $tabl[$i]["prenomequipe"] = "";
            $tabl[$i]["ins"] = "";
            $tabl[$i]["nomins"] = "";
            $tabl[$i]["prenomins"] = "";
            $tabl[$i]["rg"] = "";
            $tabl[$i]["nomrg"] = "";
            $tabl[$i]["prenomrg"] = "";
            $tabl[$i]["mont"] = 0;
			$tabl[$i]["echeance"] = 0;
            $i++;
            
            foreach ($list as $item){
                $taux = DB::table('commerciauxes')->where('codeCom', $item->Agent)->first();
                $tabl[$i]["code"] = $taux->codeCom;
                $tabl[$i]["nom"] = $taux->nomCom;
                $tabl[$i]["prenom"] = $taux->prenomCom;
                
                $equipe = $taux->codeEquipe;
                if($equipe != null && $equipe != ""){
                    $managceqp = DB::table('hierarchies')->where('codeH', $equipe)->where('structureH', "CEQP")->first();
                    if(isset($managceqp->managerH)){
                        $infequipe = DB::table('commerciauxes')->where('codeCom', $managceqp->managerH)->first();
                        $tabl[$i]["equipe"] = $managceqp->managerH;
                        $tabl[$i]["nomequipe"] = $infequipe->nomCom;
                        $tabl[$i]["prenomequipe"] = $infequipe->prenomCom;
                    }else{
                        $tabl[$i]["equipe"] = "";
                        $tabl[$i]["nomequipe"] = "";
                        $tabl[$i]["prenomequipe"] = "";
                    }
                }else{
                    $tabl[$i]["equipe"] = "";
                    $tabl[$i]["nomequipe"] = "";
                    $tabl[$i]["prenomequipe"] = "";
                }
                
                $ins = $taux->codeInspection;
                if($ins != null && $ins != ""){
                    $managins = DB::table('hierarchies')->where('codeH', $ins)->whereIn('structureH', ["BD", "INS", "FV"])->first();
                    if(isset($managins->managerH)){
                        $infins = DB::table('commerciauxes')->where('codeCom', $managins->managerH)->first();
                        $tabl[$i]["ins"] = $managins->managerH;
                        $tabl[$i]["nomins"] = $infins->nomCom;
                        $tabl[$i]["prenomins"] = $infins->prenomCom;
                    }else{
                        $tabl[$i]["ins"] = "";
                        $tabl[$i]["nomins"] = "";
                        $tabl[$i]["prenomins"] = "";
                    }
                }else{
                    $tabl[$i]["ins"] = "";
                    $tabl[$i]["nomins"] = "";
                    $tabl[$i]["prenomins"] = "";
                }
                
                $rg = $taux->codeRegion;
                if($rg != null && $rg != ""){
                    $managrg = DB::table('hierarchies')->where('codeH', $rg)->where('structureH', "RG")->first();
                    if(isset($managrg->managerH)){
                        $infrg = DB::table('commerciauxes')->where('codeCom', $managrg->managerH)->first();
                        $tabl[$i]["rg"] = $managrg->managerH;
                        $tabl[$i]["nomrg"] = $infrg->nomCom;
                        $tabl[$i]["prenomrg"] = $infrg->prenomCom;
                    }else{
                        $tabl[$i]["rg"] = "";
                        $tabl[$i]["nomrg"] = "";
                        $tabl[$i]["prenomrg"] = "";
                    }
                }else{
                    $tabl[$i]["rg"] = "";
                    $tabl[$i]["nomrg"] = "";
                    $tabl[$i]["prenomrg"] = "";
                }
                
                $tabl[$i]["mont"] = $item->avances;
				$tabl[$i]["echeance"] = $item->duree;
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allavancesdues', $autre);
            TraceController::setTrace("Vous avez exporté les avances dues des commerciaux en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportAvanceDues, 'Commerciaux_Export_Avance_Dues'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    public static function exportationcarec(){
            $list = DB::table('commerciauxes')->orderBy('codeCom', 'desc')->get();
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["taux"] = 0;
            //$tabl[$i]["dureeini"] = 0;
            $tabl[$i]["duree"] = 0;
            $tabl[$i]["effet"] = "";
            $i++;
            
            foreach ($list as $item){
                $taux = Compteagent::where('Agent', $item->codeCom)->first();
                $tabl[$i]["code"] = $item->codeCom;
                $tabl[$i]["nom"] = $item->nomCom;
                $tabl[$i]["prenom"] = $item->prenomCom;
                $tabl[$i]["taux"] = $taux->tauxCarec;
                //$tabl[$i]["dureeini"] = $taux->dureeiniCarec;
                $tabl[$i]["duree"] = $taux->dureeencourCarec;
                $tabl[$i]["effet"] = $taux->dateeffetcarec;
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allcarec', $autre);
            TraceController::setTrace("Vous avez exporté les taux carec des commerciaux en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportCarec, 'Commerciaux_Export_Taux_Carec_'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    public static function exportationnaf(){
            $list = DB::table('commerciauxes')->orderBy('codeCom', 'desc')->get();
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["montant"] = "";
            $tabl[$i]["effet"] = "";
            $i++;
            
            foreach ($list as $item){
                $compte = Compteagent::where('Agent', $item->codeCom)->first();
                $tabl[$i]["code"] = $item->codeCom;
                $tabl[$i]["nom"] = $item->nomCom;
                $tabl[$i]["prenom"] = $item->prenomCom;
                $tabl[$i]["montant"] = $compte->naf;
                $tabl[$i]["effet"] = $compte->effetnaf;
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allnaf', $autre);
            TraceController::setTrace("Vous avez exporté les montant naf paramétrer des commerciaux en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportNaf, 'Commerciaux_Export_Naf_'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    public static function exportationamical(){
            
            $list = DB::table('commerciauxes')->orderBy('codeCom', 'desc')->get();
            
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["mont"] = "";
             $tabl[$i]["duree"] = "";
            $i++;
            
            foreach ($list as $item){
                $taux = Compteagent::where('Agent', $item->codeCom)->first();
                $tabl[$i]["code"] = $item->codeCom;
                $tabl[$i]["nom"] = $item->nomCom;
                $tabl[$i]["prenom"] = $item->prenomCom;
                $tabl[$i]["mont"] = $taux->montantAmical;
                $tabl[$i]["duree"] = $taux->dureeencourAmical;
                
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allamical', $autre);
            TraceController::setTrace("Vous avez exporté les monttant amical des commerciaux en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportAmical, 'Commerciaux_Export_Taux_Amical'.date('Y-m-d-h-i-s').'.xlsx');
    }
    
    public static function setnafimport(Request $request){
        if ($request->hasFile('fichienaf')) {
            $ext  = $request->file('fichienaf')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            $tabl = "";

            if(in_array($ext,['xlsx','xls'])){
                $reference = "REF-IMPORTER-NAF-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichienaf')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray(new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                for ($i=2; $i < count($commerciaux); $i++) { 
                    $app = $commerciaux[$i];
                    
                    if($app[3] == "" || $app[3] == null) $montant = 0; else $montant = $app[3];
                    
                    if($montant != 0){
                        //dd($app);
                        $dateeffetnaf = ((strlen($app[4])!= 10)?Fonction::ChangeFormatDate2(date('d/m/Y', ($app[4] - 25569)*24*60*60)):
                            Fonction::ChangeFormatDate2($app[4]));
                        Compteagent::where('Agent', $app[0])->update(["naf"=>$montant, "effetnaf" => $dateeffetnaf]);
                    }
                }
                flash("Le paramétrage des naf sur commissions s'est effectuer avec succès.");
            }else{
                flash("Le fichier n'est pas un fichier Excel.")->error();
            }
        }else{
            flash("Pas de fichier importer.")->error();
        }
        return Back();
    }
    
    public static function setcarecimport(Request $request){
        if ($request->hasFile('fichie')) {
            $ext  = $request->file('fichie')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            $tabl = "";

            if(in_array($ext,['xlsx','xls'])){
                $reference = "REF-IMPORTER-CAREC-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichie')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                for ($i=2; $i < count($commerciaux); $i++) { 
                    $app = $commerciaux[$i];
                    
                    if($app[3] != 0){
                        if($app[3] == "" || $app[3] == null) $tau = 0; else $tau = $app[3];
                        
                        if($app[4] == "" || $app[4] == null) $duree = 0; else $duree = $app[4];
                        
                        $dateeffetcarec = ((strlen($app[5])!= 10)?
                        Fonction::ChangeFormatDate2(date('d/m/Y', ($app[5] - 25569)*24*60*60)):
                        Fonction::ChangeFormatDate2($app[5]));
                        if(isset(Compteagent::where("dateeffetcarec", "")->orwhere("dateeffetcarec", null)->where('Agent', $app[0])->first()->Agent))
                            Compteagent::where('Agent', $app[0])->update(["tauxCarec"=>$tau, "dureeencourCarec" => $duree, "dureeiniCarec" => $duree, "dateeffetcarec"=>$dateeffetcarec]);
                    }
                }
                flash("Le paramétrage des carec sur commissions s'est effectuer avec succès.");
            }else{
                flash("Le fichier n'est pas un fichier Excel.")->error();
            }
        }else{
            flash("Pas de fichier importer.")->error();
        }
        return Back();
        
    }
    
    
    /*public static function setamicalimport(Request $request){
        if ($request->hasFile('fichie')) {
            $ext  = $request->file('fichie')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["login"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
             $tabl[$i]["tel"] = "";
             $tabl[$i]["emai"] = "";
             $tabl[$i]["niv"] = "";
            $i++;

            if(in_array($ext,['xlsx','xls'])){
                $reference = "TRAITEMENTSIMUlA-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichie')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                for ($f=1; $f < count($commerciaux); $f++) { 
                    $app = $commerciaux[$f];
                   
                    if($app[0] != null ){
                        $com = DB::table('commerciauxes')->where('codeCom', $app[0])->first();
                        $log = "";
                        if(!isset($com->codeCom)){
                            $tabl[$i]["code"] = $app[0];
                         $tabl[$i]["login"] = "";
                         $tabl[$i]["nom"] = "";
                         $tabl[$i]["prenom"] = "";
                         $tabl[$i]["tel"] = "";
                         $tabl[$i]["emai"] = "";
                         $tabl[$i]["niv"] = "";
                         $i++;
                        }else{
                         
                         $pren = $com->prenomCom;
                         $nom = $com->nomCom;
                        if($com->prenomCom != "")
                            $log  = strtolower(trim(substr($com->prenomCom, 0, 1).$com->nomCom));
                        else
                        {
                                $retour = array(); 
                                  $delimiteurs = ' .!?, :;(){}[]%'; 
                                  $tok = strtok($com->nomCom, " "); 
                                  while (strlen(join(" ", $retour)) != strlen($com->nomCom)) { 
                                  array_push($retour, $tok); 
                                  $tok = strtok($delimiteurs); 
                                  }
                                  
                                  $log  = strtolower(trim(substr($retour[1], 0, 1).$retour[0]));
                                  
                                  
                                  for ($g=1; $g < count($retour); $g++){
                                      $pren .= " ".$retour[$g];
                                  }
                                  
                                  
                                  $nom = $retour[0];
                        }
                         $tabl[$i]["code"] = $com->codeCom;
                         $tabl[$i]["login"] = $log;
                         $tabl[$i]["nom"] = $nom;
                         $tabl[$i]["prenom"] = $pren;
                         $tabl[$i]["tel"] = $com->telCom;
                         $tabl[$i]["emai"] = $com->mail;
                         $tabl[$i]["niv"] = $com->Niveau;
                         $i++;
                      }
                    }
                    
                }
                
                // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allsimla', $autre);
            //TraceController::setTrace("Vous avez exporté les taux carec des commerciaux en Excel.", session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportSimla, 'CSIMULA'.date('Y-m-d-h-i-s').'.xlsx');
            
            
            }else{
                
                flash("Le fichier n'est pas un fichier Excel.")->error();
            }
        }else{
            flash("Pas de fichier importer.")->error();
        }
        return Back();
    } */
    
    public static function setamicalimport(Request $request){
        if ($request->hasFile('fichie')) {
            $ext  = $request->file('fichie')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            $tabl = "";

            if(in_array($ext,['xlsx','xls'])){
                $reference = "REF-IMPORTER-AMICAL-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichie')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                for ($i=2; $i < count($commerciaux); $i++) { 
                    $app = $commerciaux[$i];
                    
                    if($app[3] == "" || $app[3] == null) $tau = 0; else $tau = $app[3];
                    
                    if($app[4] == "" || $app[4] == null) $duree = 0; else $duree = $app[4];
                    if(isset(Compteagent::where("montantAmical", 0)->where("dureeiniAmical", 0)->where('Agent', $app[0])->first()->Agent))
                    Compteagent::where('Agent', $app[0])->update(["montantAmical"=>$tau, "dureeencourAmical" => $duree, "dureeiniAmical" => $duree]);
                }
                flash("Le paramétrage des amical sur commissions s'est effectuer avec succès.");
            }else{
                flash("Le fichier n'est pas un fichier Excel.")->error();
            }
        }else{
            flash("Pas de fichier importer.")->error();
        }
        return Back();
    }  
    
    public static function exportationCommerciaux(){
            
            $list = DB::table('commerciauxes')->orderBy('codeCom', 'desc')->get();
            Session()->put('allcommerciaux', null);
            $i = 0;
            // préparation du fichier excel
            $tabl[$i]["code"] = "";
            $tabl[$i]["nom"] = "";
            $tabl[$i]["prenom"] = "";
            $tabl[$i]["sexe"] = "";
            $tabl[$i]["tel"] = "";
            $tabl[$i]["adresse"] = "";
            $tabl[$i]["email"] = "";
            $tabl[$i]["ifu"] = "";
            $tabl[$i]["niveau"] = "";
            $tabl[$i]["codechefequipe"] = "";
            //$tabl[$i]["nomequi"] = ""; // a enlever
            $tabl[$i]["codechefins"] = "";
            /*$tabl[$i]["nomin"] = ""; // a enlever
            $tabl[$i]["codechefreg"] = "";
            $tabl[$i]["nomrg"] = ""; // a enlever
            $tabl[$i]["codechefcord"] = "";
            $tabl[$i]["nomcd"] = ""; // a enlever
            //$tabl[$i]["nomins"] = "";*/
            $tabl[$i]["dateeffet"] = "";
            $tabl[$i]["modereglement"] = "";
            $tabl[$i]["Lreglement"] = "";
            $tabl[$i]["reglement"] = "";
            $tabl[$i]["fixe"] = "";
            $tabl[$i]["vide1"] = "";
            $tabl[$i]["vide2"] = "";
            $tabl[$i]["leg"] = "SIGLE";
            $tabl[$i]["leg2"] = "LIBELLE";
            $i++;
            
            $listPayement = DB::table('structures')->get();
            $p = 0;
            
            foreach ($list as $item){
                
                $tabl[$i]["code"] = $item->codeCom;
                $tabl[$i]["nom"] = $item->nomCom;
                $tabl[$i]["prenom"] = $item->prenomCom;
                $tabl[$i]["sexe"] = $item->sexeCom;
                $tabl[$i]["tel"] = $item->telCom;
                $tabl[$i]["adresse"] = $item->adresseCom;
                $tabl[$i]["email"] = $item->mail;
                $tabl[$i]["ifu"] = $item->AIB;
                $tabl[$i]["niveau"] = $item->Niveau;
                
                $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $item->codeEquipe)->first();
                if(isset($magEquipe->managerH))
                {
                    $tabl[$i]["codechefequipe"] = $magEquipe->managerH;
                    /*$nameapp = "";
                    $nameapp = DB::table('commerciauxes')->where('codeCom', $magEquipe->managerH)->first();
                    if(isset($nameapp->nomCom))
                        $tabl[$i]["nomequi"] = $nameapp->nomCom." ".$nameapp->prenomCom; else $tabl[$i]["nomequi"] = "";*/
                }
                else
                { 
                    $tabl[$i]["codechefequipe"] = "";
                    //$tabl[$i]["nomequi"] = "";
                }
                
                //$tabl[$i]["codeins"] = $item->codeInspection;
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $item->codeInspection)->first();
                if(isset($magIns->managerH)){
                    $tabl[$i]["codechefins"] = $magIns->managerH;
                    /*$nameapp = "";
                    $nameapp = DB::table('commerciauxes')->where('codeCom', $magIns->managerH)->first();
                    if(isset($nameapp->nomCom))
                        $tabl[$i]["nomin"] = $nameapp->nomCom." ".$nameapp->prenomCom; else $tabl[$i]["nomin"] = "";*/
                }
                else
                {
                    $tabl[$i]["codechefins"] = "";
                    //$tabl[$i]["nomin"] = "";
                }
                /*
                $magReg = DB::table('hierarchies')->where('structureH', 'RG')->where('codeH', $item->codeRegion)->first();
                if(isset($magReg->managerH)){
                    $tabl[$i]["codechefreg"] = $magReg->managerH;
                    $nameapp = "";
                    $nameapp = DB::table('commerciauxes')->where('codeCom', $magReg->managerH)->first();
                    if(isset($nameapp->nomCom))
                        $tabl[$i]["nomrg"] = $nameapp->nomCom." ".$nameapp->prenomCom; else $tabl[$i]["nomrg"] = "";
                }else{
                    $tabl[$i]["codechefreg"] = "";
                    $tabl[$i]["nomrg"] = "";
                }
                
                $magCD = DB::table('hierarchies')->where('structureH', 'CD')->where('codeH', $item->codeCD)->first();
                if(isset($magCD->managerH)){
                    $tabl[$i]["codechefcord"] = $magCD->managerH;
                    $nameapp = "";
                    $nameapp = DB::table('commerciauxes')->where('codeCom', $magCD->managerH)->first();
                    if(isset($nameapp->nomCom))
                        $tabl[$i]["nomcd"] = $nameapp->nomCom." ".$nameapp->prenomCom; else $tabl[$i]["nomcd"] = "";
                }else{
                    $tabl[$i]["codechefcord"] = "";
                    $tabl[$i]["nomcd"] = "";
                }*/
                
                $tabl[$i]["dateeffet"] = $item->dateEffet;
                $avoirs = DB::table('compteagents')->where('Agent', $item->codeCom)->first();
                if(isset($avoirs)){
                    if(strpos($avoirs->libCompte, "MTN") !== false){
                        $tabl[$i]["modereglement"] = "MOMO";
                        $tabl[$i]["Lreglement"] = substr($avoirs->libCompte, -4);
                    }
                    else
                        if(strpos($avoirs->libCompte, "MOOV") !== false){
                            $tabl[$i]["modereglement"] = "MOMO";
                            $tabl[$i]["Lreglement"] = substr($avoirs->libCompte, -4);
                        }
                        else
                            if($avoirs->libCompte == ""){
                                $tabl[$i]["modereglement"] = "";
                                $tabl[$i]["Lreglement"] = "";
                            }
                            else{
                                $tabl[$i]["modereglement"] = "BANQUE";
                                $tabl[$i]["Lreglement"] = $avoirs->libCompte;
                            }
                    $tabl[$i]["reglement"] = $avoirs->numCompte;
                    $tabl[$i]["fixe"] = $avoirs->fixe;
                }else{
                    $tabl[$i]["modereglement"] = "";
                    $tabl[$i]["Lreglement"] = "";
                    $tabl[$i]["reglement"] = "";
                    $tabl[$i]["fixe"] = "";
                }
                $tabl[$i]["vide1"] = "";
                $tabl[$i]["vide2"] = "";
                if(isset($listPayement[$p]->sigle)){
                    $tabl[$i]["leg"] = $listPayement[$p]->sigle;
                    $tabl[$i]["leg2"] = $listPayement[$p]->libelle;
                    $p++;
                }
                
                $i++;
            }
            
            // Exporter tous les commerciaux 
            $autre = new Collection($tabl);
            Session()->put('allcommerciaux', $autre);
            TraceController::setTrace(
                "Vous avez exporté les commerciaux en Excel.",
                session("utilisateur")->idUser);
            // Téléchargement du fichier excel
            return Excel::download(new ExportCommerciaux, 'Commerciaux_Export_'.date('Y-m-d-h-i-s').'.xlsx');
    }

    public static function importerCommerciaux(Request $request){

        if ($request->hasFile('fichie')) {
            $ext  = $request->file('fichie')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            $tabl = "";

            if(in_array($ext,['xlsx','xls'])){
                $reference = "REF-IMPORTER-COMMERCIAUX-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichie')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                // Vérification de l'entête du fichier 
                if($commerciaux[0][0] == "CODE" && $commerciaux[0][1] == "NOM" && $commerciaux[0][2] == "PRENOMS" && $commerciaux[0][3] == "SEXE" && $commerciaux[0][4] == "TELEPHONE" &&
                    $commerciaux[0][5] == "ADRESSE" && $commerciaux[0][6] == "EMAIL" && $commerciaux[0][7] == "IFU" && $commerciaux[0][8] == "NIVEAU" && $commerciaux[0][9] == "CODE CHEF EQUIPE" && 
                    $commerciaux[0][10] == "CODE CHEF INSPECTION" && $commerciaux[0][11] == "DATE EFFET" && $commerciaux[0][12] == "MODE DE REGLEMENT [MOMO, BANQUE, VIREMENT, CHEQUE]" && 
                    $commerciaux[0][13] == "MOYEN DE REGLEMENT" && $commerciaux[0][14] == "NUMERO DE COMPTE REGLEMENT" && $commerciaux[0][15] == "FIXE")
                    
                    {
                        for ($i=2; $i < count($commerciaux); $i++) { 
                            $app = $commerciaux[$i];
        
                            $code = $app[0];
                            $nom = $app[1];
                            $prenom = $app[2];
                            $sexe = $app[3];
                            $tel = $app[4];
                            $adress = $app[5];
                            $email = $app[6];
                            $ifu = $app[7];
                            $niv = strtoupper($app[8]);
                            $chefequipe = $app[9];
                            $inspecteur = $app[10];
                            $dateeffet = $app[11];
                            $modereglement = $app[12];
                            $libBan = trim($app[13]);
                            $compteBan = $app[14];
                            $fixe = $app[15];
                            
                            $carb = 0;
                            $teldota = 0;
                            if(isset($app[20]))
                                $carb = $app[20];
                            if(isset($app[21]))
                                $teldota = $app[21];
                            
                            $Equipe_v = "";
                            $Inspection_v = "";
                            $Region_v = "";
                            $cd_v = "";
                            
                            $dateeffet = ((strlen($dateeffet)!= 10)?
                            Fonction::ChangeFormatDate2(date('d/m/Y', ($dateeffet - 25569)*24*60*60)):
                            Fonction::ChangeFormatDate2($dateeffet));
                            
                                // Traitement niveau 
                                $niveau = DB::table('niveaux')->where("codeNiveau", $niv)->first();
                                if(!isset($niveau->codeNiveau)){
                                    $niveau = DB::table('niveaux')->where("libelleNiveau", $niv)->first();
                                    if(!isset($niveau->codeNiveau))
                                        $niv = CommerciauxController::getCodeNiveau($niv);
                                }
                                
                                if($email == null || $email == ""){
                                    $email = "nsiavieassurances.benin@gmail.com";
                                }
                                /*
                                // Traitement du chef d'inspection
                                if(ImporterCommissionController::CheckCodeInspecteur($inspecteur) != 0){
                                    $Equipe_v = "";
                                    $Inspection_v = ImporterCommissionController::CheckCodeInspecteur($inspecteur);
                                    $Region_v = DB::table('hierarchies')->where("codeH", $Inspection_v)->first()->superieurH;
                                    $cd_v = DB::table('hierarchies')->where("codeH", $Region_v)->first()->superieurH;
                                    
                                }*/
                                /* else{
                                    // Créer les inspections avant l'importation
                                    
                                    /*if($codeinspection != 0 || $codeinspection != "" || $codeinspection != null)
                                    {
                                        
                                        // Vérif de l'existance du manager dans une inspection auparavant
                                        $verifmanager = DB::table('hierarchies')->where("managerH", $inspecteur)->first();
                                        
                                        if(isset($verifmanager->managerH)){
                                            DB::table('hierarchies')->where("codeH", $verifmanager->codeH)->update([
                                                "managerH" => 0
                                            ]);
                                        }
                                        
                                        $verifinspection = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $codeinspection)->first();
                                        
                                        if(isset($verifinspection->codeH)){
                                            DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $codeinspection)->update([
                                                "managerH" => $inspecteur
                                            ]);
                                        }else{ 
                                            $ins = CommerciauxController::createInspection($inspecteur, $codeinspection, "9999");
                                            $Equipe_v = "";
                                            $Inspection_v = $ins;
                                            $Region_v = "9999";
                                        }
                                    }else{
                                        $Equipe_v = "";
                                        $Inspection_v = "";
                                        $Region_v = "";
                                    } 
                                } */
            
                                // Traitement du chef d'Equipe
                                if ($chefequipe == 101 || $chefequipe == 0 || $chefequipe == 102) {
                                    if($niv != "INS")
                                    {   
                                        $Equipe_v = "";
                                        $Inspection_v = "";
                                        $Region_v = "";
                                    }
                                }else{
                                    if(CommerciauxController::CheckCodeEquipe($chefequipe) != 0){
                                        /*
                                        $verifmanager = DB::table('hierarchies')->where("managerH", $chefequipe)->first();
                                        
                                        if(isset($verifmanager->managerH)){
                                            DB::table('hierarchies')->where("codeH", $verifmanager->codeH)->update([
                                                "managerH" => 0
                                            ]);
                                        }
                                        */
                                        $Equipe_v = CommerciauxController::CheckCodeEquipe($chefequipe);
                                        
                                        $verifceqp = DB::table('hierarchies')->where('structureH', "CEQP")->where('codeH', $Equipe_v)->first();
                                        
                                        /*if(isset($verifceqp->codeH)){
                                            DB::table('hierarchies')->where('structureH', "CEQP")->where('codeH', $Equipe_v)->update([
                                                "superieurH" => $Inspection_v
                                            ]);
                                        }*/
                                        $Inspection_v = $verifceqp->superieurH;
                                        $Equipe_v = $Equipe_v;
                                        $Region_v = DB::table('hierarchies')->where("codeH", $Inspection_v)->first()->superieurH;
                                        $cd_v = DB::table('hierarchies')->where("codeH", $Region_v)->first()->superieurH;
                                    }else{
                                          
                                        // Crée Equipe et récuprer le code de l'équipe
                                        $Inspection_v = $Inspection_v;
                                        $Equipe_v = CommerciauxController::createEquipe($chefequipe, $Inspection_v);
                                        $Region_v = DB::table('hierarchies')->where("codeH", $Inspection_v)->first()->superieurH;
                                        $cd_v = DB::table('hierarchies')->where("codeH", $Region_v)->first()->superieurH;
                                          
                                    }
                                }
                                
                                // traitement d'un conseiller qui était un manageur 
                                
                                if($niv == "CONS"){
                                    $verifmanagercons = DB::table('hierarchies')->where("managerH", $code)->first();
                                        
                                        if(isset($verifmanagercons->managerH)){
                                            DB::table('hierarchies')->where("codeH", $verifmanagercons->codeH)->update([
                                                "managerH" => 0
                                            ]);
                                        }
                                }
                                
        
                                // Traitement compte
                                if(strtoupper($modereglement) != "")
                                {
                                    if(strtoupper($modereglement) == "MOMO")
                                    {
                                        if(strtoupper($libBan) == "MTN" || strtoupper($libBan) == "MOOV" || strtoupper($libBan) == "MOMO"){
                                            $id_num = substr($compteBan, -8, 2);
                                            if(CommerciauxController::verifie_mtn($id_num) == 0)
                                                $libBan = "MTN";
                                            else
                                                if(CommerciauxController::verifie_moov($id_num) == 0)
                                                    $libBan = "MOOV";
                                                else
                                                    $libBan = "CELTIIS";
                                        }else{
                                            $error +=1;
                                            $message_error .= "Mode de reglement doit être MTN OU MOOV; ";
                                                
                                            // Erreur : Mode de reglement doit être MTN OU MOOV
                                        }
                                    }else{
                                        if(strtoupper($modereglement) == "BANQUE" || strtoupper($modereglement) == "VIREMENT" || strtoupper($modereglement) == "CHEQUE"){
                                            if($libBan == "DBB") $libBan = "NBB";
                                            if($libBan == "BAIC") $libBan = "BIIC";
                                            if($libBan == "BIBE") $libBan = "BIIC";
                                            if($libBan == "BHB") $libBan = "BOA";
                                            $sigle = DB::table('structures')->where('sigle', $libBan)->first();
                                            if(!isset($sigle->sigle))
                                            {
                                                $error +=1;
                                                $message_error .= "Le sigle du moyen de reglèment renseigner n'existe pas, consulté la legende; ";
                                                // Erreur : Le sigle du moyen de reglèment renseigner n'existe pas, consulté la legende   
                                            }
                                        }
                                        else{
                                                $error +=1;
                                                $message_error .= "Mode de règlement incorrect; ";
                                            // Erreur : Mode de règlement incorrect
                                        }
                                    }
                                }
                                else{
                                    $libBan = "Autres"; $modereglement = ""; $compteBan = 0;
                                }
							
								// traitement code
								if(!is_int($code) || $code == "" || $code == null){
									$error +=1;
                                    $message_error .= "Code doit être un entier";
								}
                                
                                // traitement fixe
                                if(!is_int($fixe) && $fixe != "" && $fixe != null){
                                    $error +=1;
                                    $message_error .= "Fixe doit être un entier";
                                }
                                
                                // traitement carb
                                if(!is_int($carb) && $carb != "" && $carb != null){
                                    $error +=1;
                                    $message_error .= "Dotation Carburant doit être un entier";
                                }
                                
                                // traitement telephonie
                                if(!is_int($teldota) && $teldota != "" && $teldota != null){
                                    $error +=1;
                                    $message_error .= "Dotation téléphonie doit être un entier";
                                }
                                
                                if(isset(Commerciaux::where('codeCom', $code)->first()->codeCom))
                                {
                                    $error +=1;
                                    $message_error .= "Le commercial existe déjà!";
                                }
                                
                                if($error == 0){
                                    
                                    if(!isset(Commerciaux::where('codeCom', $code)->first()->codeCom))
                                    {
                                        // Enregistrer commercial
                                        $add = new Commerciaux();
                                        $add->codeCom = $code;
                                        $add->nomCom = $nom;
                                        $add->prenomCom =  $prenom;
                                        $add->telCom = $tel;
                                        $add->sexeCom = $sexe;
                                        $add->adresseCom = $adress;
                                        $add->mail = $email;
                                        $add->AIB = $ifu;
                                        $add->Niveau = $niv;
                                        $add->codeEquipe = $Equipe_v;
                                        $add->codeInspection = $Inspection_v;
                                        $add->codeRegion = $Region_v;
                                        $add->codeCD = $cd_v;
                                        $add->action_save = 'i';
                                        $add->user_action = session("utilisateur")->idUser;
                                        $add->save();
                                        
                                        if($fixe == null) $fixe = 0 ; if($teldota == null) $teldota = 0 ; if($carb == null) $carb = 0 ;
                                        
                                        if(!isset(Compteagent::where('Agent', $add->id)->first()->Agent)){
                                            // Créer un compte commercial
                                            $addC = new Compteagent();
                                            $addC->Agent = $add->id;
                                            $addC->libCompte = $libBan;
                                            $addC->numCompte = $compteBan;
                                            $addC->fixe = $fixe;
                                            $addC->dotationTelephonie = $teldota;
                                            $addC->dotationCarburant = $carb;
                                            $addC->Modepayement = $modereglement;
                                            $addC->save();
                                        }else{
                                                Compteagent::where('Agent', $add->id)->update([
                                                    "libCompte" => $libBan,
                                                    "numCompte" => $compteBan,
                                                    "fixe" => $fixe,
                                                    "dotationTelephonie" => $teldota,
                                                    "dotationCarburant" => $carb,
                                                    "Modepayement" => $modereglement
                                                ]);
                                        }
                                    }
                                    /*else{
                                        $dataInfo = json_encode(Commerciaux::where('codeCom', $code)->first());
                                        $dataCompte = json_encode(Compteagent::where('Agent', $code)->first());
                                        $data = json_encode(array($dataInfo, $dataCompte));
                                        TraceController::setTrace("Existing sales (commercial) data : ".$data, session("utilisateur")->idUser);
                                        
                                        Commerciaux::where('codeCom', $code)->update([
                                            "nomCom" => $nom,
                                            "prenomCom" => $prenom,
                                            "telCom" => $tel, 
                                            "sexeCom" => $sexe,
                                            "adresseCom" => $adress,
                                            "mail" => $email,
                                            "AIB" => $ifu,
                                            "Niveau" => $niv,
                                            "codeEquipe" => $Equipe_v,
                                            "codeInspection" => $Inspection_v,
                                            "codeRegion" => $Region_v,
                                            "user_action" => session("utilisateur")->idUser
                                        ]);
                                        
                                        if($fixe == null) $fixe = 0 ; if($teldota == null) $teldota = 0 ; if($carb == null) $carb = 0 ;
                                        
                                        Compteagent::where('Agent', $code)->update([
                                            "libCompte" => $libBan,
                                            "numCompte" => $compteBan,
                                            "fixe" => $fixe,
                                            "dotationTelephonie" => $teldota,
                                            "dotationCarburant" => $carb,
                                            "Modepayement" => $modereglement
                                        ]);
                                        
                                    }*/
                                }else{
                                    // Préparer le fichier des erreurs
                                    $error_g += 1;
                                    $temp_error[$error_g]["code"] = $commerciaux[$i][0];
                                    $temp_error[$error_g]["nom"] = $commerciaux[$i][1];
                                    $temp_error[$error_g]["prenom"] = $commerciaux[$i][2];
                                    $temp_error[$error_g]["sexe"] = $commerciaux[$i][3];
                                    $temp_error[$error_g]["tel"] = $commerciaux[$i][4];
                                    $temp_error[$error_g]["adre"] = $commerciaux[$i][5];
                                    $temp_error[$error_g]["email"] = $commerciaux[$i][6];
                                    $temp_error[$error_g]["ifu"] = $commerciaux[$i][7];
                                    $temp_error[$error_g]["niveau"] = $commerciaux[$i][8];
                                    $temp_error[$error_g]["equipe"] = $commerciaux[$i][9];
                                    $temp_error[$error_g]["inspection"] = $commerciaux[$i][10];
                                    $temp_error[$error_g]["date"] = $dateeffet ;
                                    $temp_error[$error_g]["mod"] = $commerciaux[$i][12];
                                    $temp_error[$error_g]["compte"] = $commerciaux[$i][13];
                                    $temp_error[$error_g]["banque"] = $commerciaux[$i][14];
                                    $temp_error[$error_g]["fixe"] = $commerciaux[$i][15];
                                    $temp_error[$error_g]["Observations"] = $message_error;
            
                                }
                                
                                $message_error = "";
                                $error = 0;                    
                }
                        
                        if ($error_g == 0) {
                            flash("Tous les commerciaux importés avec succès.")->success();
                            TraceController::setTrace("Vous avez importés les commerciaux avec succès.", session("utilisateur")->idUser);
                            return Back();
                        }
                        else{
                            $autre = new Collection($temp_error);
                            Session()->put('commerciauxerror', $autre);
                            flash(count($commerciaux) - 2 - $error_g.' importé(s) avec succès et '.$error_g.' error (s) trouvée(s). <br> <a href="/erreurcommerciaux"> Télécharger le fichier d\'erreur. </a>')->error();
                            TraceController::setTrace(count($commerciaux) - 2 - $error_g.' importé(s) avec succès et '.$error_g.' error (s) trouvée(s).', session("utilisateur")->idUser);
                            return Back();
                        }
                    }else{
                        flash("L'entête du fichier n'est pas correcte. Veuillez télécharger le fichier exemplaire et remplir. ")->error();
                    }
            }else{
                flash("Le fichier n'est pas un fichier Excel.")->error();
            }
        }else{
            flash("Pas de fichier Commerciaux.")->error();
        }
        return Back();
    }

    public static function geterrorcommerciaux(){
        if (session("commerciauxerror") != ""){
            return Excel::download(new ExportErreurCommerciaux, 'ErreurCommerciaux'.date('Y-m-d-h-i-s').'.xlsx');
        }
    }

    public static function createInspection($code_v, $ins, $region){
        $r = "";
        if ($region == null && $region == "") {
            $r = "9999";
        }else{
            $r = $region;
        }

        $addH = new Hierarchie();
        $addH->codeH = $ins;
        $addH->structureH = "INS";
        $addH->managerH = $code_v;
        $addH->superieurH = $r;
        $addH->user_action = session('utilisateur')->idUser;
        $addH->save();
        return $ins;
    }

    public static function createEquipe($code_v, $Inspection){
        $equip = CommerciauxController::GenererCodeEquipe();
        //dd($equipe);
        $addH = new Hierarchie();
        $addH->codeH = $equip;
        $addH->structureH = "CEQP";
        $addH->managerH = $code_v;
        $addH->superieurH = $Inspection;
        $addH->user_action = session('utilisateur')->idUser;
        $addH->save();
        
        $Region_v = DB::table('hierarchies')->where("codeH", $Inspection)->first()->superieurH;
        $cd_v = DB::table('hierarchies')->where("codeH", $Region_v)->first()->superieurH;
        // Mise à jour du niveau, equipe, ins, région du nouveau manageur
        Commerciaux::where('codeCom', $code_v)->update([
                            'Niveau' => "CEQP",
                            'codeEquipe' => $equip,
                            'codeInspection' => $Inspection,
                            'codeRegion' =>  $Region_v,
                            'codeCD' =>  $cd_v,
                        ]);

        return $equip;
    }

    public static function GenererCodeEquipe()
    {
            $string = "";
            $universal_key = 4;

            $user_ramdom_key = "0123456789";
            srand((double)microtime()*time());
            for($i=0; $i<$universal_key; $i++) {
            $string .= $user_ramdom_key[rand()%strlen($user_ramdom_key)];
            }

            if (!isset(DB::table('hierarchies')->where('codeH', $string)->where('structureH', "CEQP")->first()->codeH)){
                return $string;
            }else
                 CommerciauxController::GenererCodeEquipe();
    }

    public static function getCodeNiveau($lib){
        if(isset(DB::table('niveaux')->where('libelleNiveau', $lib)->first()->codeNiveau))
            return DB::table('niveaux')->where('libelleNiveau', $lib)->first()->codeNiveau;
        else
            return "CONS";
        
    }

    public static function CheckCodeEquipe($codeChef){
        $verif = DB::table('hierarchies')->where('structureH', "CEQP")->where('managerH', $codeChef)->first();
        if(isset($verif->codeH)) return $verif->codeH; else return 0;
    }

    public static function CheckCodeInspection($inspection)
    {
        $verif = DB::table('hierarchies')->where('codeH', $inspection)->whereNotIn("structureH", trans('var.notins'))->first();
        if(isset($verif->codeH)) return $verif->codeH; else return 0;
    }

    public static function verifie_moov($numero)
    {
        $table_numero_possible = [60,64,65,58,55, 68,94,95,98,99];

            $v = 0; 

        for ($i=0; $i < 10; $i++)
        {
            if ($numero == $table_numero_possible[$i])
                $v = 1;
        }

        if ($v <> 0)
            return 0; // Bon
        else 
            return 1; // Mauvais
    }

    public static function verifie_mtn($id_numero)
    {
        $table_numero_possible = [50,51,52,53,54,56,57,59,61,62,66,67,69,90,91,96,97];
        
        $v = 0;

        for ($i=0; $i < 17; $i++)
        {
            if ($id_numero == $table_numero_possible[$i])
                $v = 1;
        }

        if ($v <> 0)
            return 0; // Bon
        else 
            return 1; // Mauvais
            
    }

    public static function getretrograder()
    {
        $affcom = AllTable::table('commerciauxes')->where('codeCom', request('mag'))->first();
        $all = "";
        $type = $affcom->Niveau;
        if ($type == "CEQP") {
            $all = AllTable::table('commerciauxes')->whereIn('Niveau', trans('var.magequipe'))->get();
        }
        if ($type == "INS" || $type == "BD" || $type == "BDS" || $type == "APL") {
            $all = AllTable::table('commerciauxes')->whereIn('Niveau', trans('var.magins'))->get();
        }
        if ($type == "RG") {
            $all = AllTable::table('commerciauxes')->where('Niveau', trans('var.rg'))->get();
        }
        if ($type == "CD") {
            $all = AllTable::table('commerciauxes')->where('Niveau', "CD")->get();
        }
        
        return view('admin.retrograde', compact('affcom', 'all', 'type'));
    }
     
    /* public function setremboursement(Request $request){
        
        // Vérification des avances dues à l'agent
		$checkavance = DB::table('compteagents')->where('Agent', request('agent'))->first();
		
		$sold = 0;
		
		if($checkavance->avances > 0 && request('echeance') != null){
			// avances / duree pour savoir comment doit être rembourser suivant la période
			$arembourcer = $checkavance->avances / $checkavance->duree;
			
			$rembource = $arembourcer * request('echeance');
			
			$sold = $rembource;
            
            if($checkavance->compte >= $rembource){
                
    			$soldeactu = $checkavance->compte - $rembource;
    
    			$resteavance = $checkavance->avances - $rembource;
    
    			$dureer = $checkavance->duree - request('echeance');
    
    			Compteagent::where('Agent', request('agent'))->update([
    				'avancesancien' => $checkavance->avances,
    				'avances' => $resteavance,
    				'duree' => $dureer,
    				'recentrembourcer' => $rembource, 
    				'compte' => $soldeactu
    			]);
            }else{
                // placer avances anticiper dans compteBloquer
                $resteavance = $checkavance->avances - $rembource;
                $dureer = $checkavance->duree - request('echeance');
                
                $aa = $checkavance->avancesancien + $checkavance->avances;
                
                $rr = $checkavance->recentrembourcer + $rembource;
                
                $taux = Fonction::RecupererTaux(request('agent'));
                
                $rembource = $rembource + ($rembource * $taux / 100);
                
                $cb = $checkavance->compteBloquer - $rembource;
                
                Compteagent::where('Agent', request('agent'))->update([
    				'avancesancien' => $aa, // ++
    				'avances' => $resteavance,
    				'duree' => $dureer,
    				'recentrembourcer' => $rr, // ++
    				'compteBloquer' => $cb // ++
    			]);
            }
		}else{
		    flash("Il n'y a pas d'avances en cours ou champ invalide.");
		    return Back();
		}
        
        TraceController::setTrace("Vous avez procédé à un rembourcement anticipé de ".$sold."F CFA", session("utilisateur")->idUser);
        
        flash('Avance rembourcée avec succès.');
        return Back();
    } */ // Ce 22 jUIELLET 2022
    public function setremboursement(Request $request){
        
        // Vérification des avances dues à l'agent
		$checkavance = DB::table('compteagents')->where('Agent', request('agent'))->first();
		
		if($checkavance->avances > 0 && request('echeance') != null){
			
			if($checkavance->duree >= request('echeance')){
			    Compteagent::where('Agent', request('agent'))->update([
    				'anticiper' => request('echeance')
    			]);
    			flash("Demande de remboursement anticiper pour le commercial ".request('agent'). " de ".request('echeance')." échéances enregistrer avec succès.");
    			TraceController::setTrace("Demande de remboursement anticiper pour le commercial ".request('agent'). " de ".request('echeance')." échéances enregistrer avec succès.", session("utilisateur")->idUser);
			}else{
			    // Echec
			    flash("Le nombre d'échéance déplace le reste d'échéance actuel.");
			}
			
		}else{
		    flash("Il n'y a pas d'avances en cours ou champ invalide.");
		    
		}
        return Back();
    }

    public function setretrograder(Request $request)
    {
        $com = $request->codeC;
            $newchef = $request->select; // Code du commercial qui devient chef
            
            if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-".date('ymdhis');
                $namefile = $referenceNote.".pdf";
                $upload = "document/upload/";
                $request->file('note')->move($upload, $namefile);
                
                $path = $upload.$namefile;
                $desc = htmlspecialchars(trim($request->desc));
                $ref = htmlspecialchars(trim($request->ref));
                $dateeffet = htmlspecialchars(trim($request->dateeffet));
                
                $codeComIns = $com; 
                $catins = $request->type;
                $type = $request->type;

                // Cas des inspections
                if ($type == "INS"  || $type == "BD" || $type == "BDS" || $type == "APL") {
                    $existanthierarchie = AllTable::table('commerciauxes')->where('codeCom', $com)->first()->codeInspection;

                    // Enregistrement du motif de la modification de l'inspection existante
                    $addA = new Avenant();
                    $addA->codeHerarchieModifier = $existanthierarchie; // La ligne d'occurence qui a connu de changement
                    $addA->path = $path;
                    $addA->referenceNoteSave = $referenceNote;
                    $addA->description = $desc;
                    $addA->reference = $ref;
                    $addA->dateeffet = $dateeffet;
                    $addA->existantManageur = $codeComIns;
                    $addA->nouveauManageur = $newchef;
                    $addA->structure = $catins;
                    $addA->user_action = session("utilisateur")->idUser;
                    $addA->save();

                        $addT = new Trace();
                        $addT->libelleTrace = "Le chef d'inspection dont le code Commercial ".$com." est rétrogradé et remplacé par le Commercial dont le code est ".$newchef;
                        $addT->user_action = session("utilisateur")->idUser;
                        $addT->save();

                        $sup = Hierarchie::where("codeH", $existanthierarchie)->first()->superieurH;
                        
                        // Modification de l'inspection existante 
                        Hierarchie::where("codeH", $existanthierarchie)->update([
                            'managerH' => $newchef
                        ]);

                        // Mise à jour du niveau, equipe, ins, région du nouveau manageur
                        Commerciaux::where('codeCom', $newchef)->update([
                            'Niveau' => $catins,
                            'codeEquipe' =>  "0000",
                            'codeInspection' => $existanthierarchie,
                            'codeRegion' =>  $sup,
                        ]); 

                        // Créer une nouvelle équipe pour celui qui est rétrograder
                        // Fonction pour générer un code equipe
                        //$codeeqp = Fonction::GenererCode('CEQP');
                        /*
                        $codeeqp = "9584";

                        $addH = new Hierarchie();
                        $addH->codeH = $codeeqp; 
                        $addH->libelleH =  "";
                        $addH->villeH =  "";
                        $addH->managerH =  $com;
                        $addH->structureH =  "CEQP"; 
                        $addH->superieurH =  $existanthierarchie;
                        $addH->user_action = session("utilisateur")->idUser;
                        $addH->save(); */

                        // Mise à jour de l'ancien commercial
                        Commerciaux::where('codeCom', $com)->update([
                            'Niveau' => "CEQP",
                            'codeEquipe' => ""
                        ]);

                        flash("Le chef d'inspection dont le code Commercial ".$com." est rétrogradé et remplacé par le Commercial dont le code est ".$newchef);
                }

                // Cas des équipes
                if ($request->type == "CEQP") {
                    $existanthierarchie = AllTable::table('commerciauxes')->where('codeCom', $com)->first()->codeEquipe;

                    // Enregistrement du motif de la modification de l'inspection existante
                    $addA = new Avenant();
                    $addA->codeHerarchieModifier = $existanthierarchie; // La ligne d'occurence qui a connu de changement
                    $addA->path = $path;
                    $addA->referenceNoteSave = $referenceNote;
                    $addA->description = $desc;
                    $addA->reference = $ref;
                    $addA->dateeffet = $dateeffet;
                    $addA->existantManageur = $codeComIns;
                    $addA->nouveauManageur = $newchef;
                    $addA->structure = $catins;
                    $addA->user_action = session("utilisateur")->idUser;
                    $addA->save();

                        $addT = new Trace();
                        $addT->libelleTrace = "Le chef d'équipe dont le code Commercial ".$com." est rétrogradé et remplacé par le Commercial dont le code est ".$newchef;
                        $addT->user_action = session("utilisateur")->idUser;
                        $addT->save();

                        $sup = Hierarchie::where("codeH", $existanthierarchie)->first()->superieurH;
                        $supsup = Hierarchie::where("codeH", $sup)->first()->superieurH;

                        // Modification de l'inspection existante 
                        Hierarchie::where("codeH", $existanthierarchie)->update([
                            'managerH' => $newchef
                        ]);

                        // Mise à jour du niveau, equipe, ins, région du nouveau manageur
                        Commerciaux::where('codeCom', $newchef)->update([
                            'Niveau' => $catins,
                            'codeEquipe' =>  $existanthierarchie,
                            'codeInspection' => $sup,
                            'codeRegion' =>  $supsup,
                        ]);

                        // Mise à jour de l'ancien commercial
                        Commerciaux::where('codeCom', $com)->update([
                            'Niveau' => "CONS"
                        ]);

                        flash("Le chef d'équipe dont le code Commercial ".$com." est rétrogradé et remplacé par le Commercial dont le code est ".$newchef);    
                }

                // Cas des régions
                if ($request->type == "RG" || $request->type == "CD") {
                    $existanthierarchie = AllTable::table('commerciauxes')->where('codeCom', $com)->first()->codeRegion;

                    // Enregistrement du motif de la modification de l'inspection existante
                    $addA = new Avenant();
                    $addA->codeHerarchieModifier = $existanthierarchie; // La ligne d'occurence qui a connu de changement
                    $addA->path = $path;
                    $addA->referenceNoteSave = $referenceNote;
                    $addA->description = $desc;
                    $addA->reference = $ref;
                    $addA->dateeffet = $dateeffet;
                    $addA->existantManageur = $codeComIns;
                    $addA->nouveauManageur = $newchef;
                    $addA->structure = $catins;
                    $addA->user_action = session("utilisateur")->idUser;
                    $addA->save();

                        $addT = new Trace();
                        $addT->libelleTrace = "Le chef région dont le code Commercial ".$com." est rétrogradé et remplacé par le Commercial dont le code est ".$newchef;
                        $addT->user_action = session("utilisateur")->idUser;
                        $addT->save();

                        // Modification de l'inspection existante 
                        Hierarchie::where("codeH", $existanthierarchie)->update([
                            'managerH' => $newchef
                        ]);

                        // Mise à jour du niveau, equipe, ins, région du nouveau manageur
                        Commerciaux::where('codeCom', $newchef)->update([
                            'Niveau' => $catins,
                            'codeEquipe' =>  "0000",
                            'codeInspection' => "0000",
                            'codeRegion' =>  $existanthierarchie,
                        ]);

                        // L'ancien chef région devient une inspection du région du nouveau chef région

                        // Pour ce fait, on crée une nouvelle inspection et il sera le chef
                        // Fonction pour générer un code pour l'inspection
                        $codeins = Fonction::GenererCode('INS');

                        $addH = new Hierarchie();
                        $addH->codeH = $codeins; 
                        $addH->libelleH =  "";
                        $addH->villeH =  "";
                        $addH->managerH =  $com;
                        $addH->structureH =  "INS";
                        $addH->superieurH =  $existanthierarchie;
                        $addH->user_action = session("utilisateur")->idUser;
                        $addH->save();

                        // Mise à jour de l'ancien commercial
                        Commerciaux::where('codeCom', $com)->update([
                            'Niveau' => "INS",
                            'codeEquipe' => "0000",
                            'codeInspection' => $codeins
                        ]);

                        flash("Le chef région dont le code Commercial ".$com." est rétrogradé et remplacé par le Commercial dont le code est ".$newchef);    
                }
                
                return redirect('/listcommerciaux');

            }else{
                flash('Aucun fichier importé');
                return Back();
            }
    }

    public static function getexistant()
    {
        $affcom = AllTable::table('commerciauxes')->where('codeCom', request('mag'))->first();
        $all = "";
        $type = $affcom->Niveau;
        switch ($type) {
            case 'CEQP':
                $all = AllTable::table('hierarchies')->whereIn('structureH', trans('var.ins'))->get();
                break;
            case 'CONS':
                $all = AllTable::table('hierarchies')->where('structureH', trans('var.equipe'))->get();
                break;
            default:
                break;
        }
        if ($type == "INS" || $type == "BD" || $type == "BDS" || $type == "APL") {
            $all = AllTable::table('hierarchies')->where('structureH', trans('var.rg'))->get();
        }
        
        return view('admin.existantins', compact('affcom', 'all', 'type'));
    }

    public function setexistant(Request $request)
    {
        
            $com = $request->codeC;
            $insexistante = $request->insselect; // Code de l'inspection existante
            
            if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-".date('ymdhis');
                $namefile = $referenceNote.".pdf";
                $upload = "document/upload/";
                $request->file('note')->move($upload, $namefile);
                
                $path = $upload.$namefile;
                $desc = htmlspecialchars(trim($request->desc));
                $ref = htmlspecialchars(trim($request->ref));
                $dateeffet = htmlspecialchars(trim($request->dateeffet));

                $codeComIns = Hierarchie::where("codeH", $insexistante)->first()->managerH; // Code du commercial du manageur de l'inspection existante
                $catins = Hierarchie::where("codeH", $insexistante)->first()->structureH;
                    

                // Enregistrement du motif de la modification de l'inspection existante
                $addA = new Avenant();
                $addA->codeHerarchieModifier = $insexistante; // La ligne d'occurence qui a connu de changement
                $addA->path = $path;
                $addA->referenceNoteSave = $referenceNote;
                $addA->description = $desc;
                $addA->reference = $ref;
                $addA->dateeffet = $dateeffet;
                $addA->existantManageur = $codeComIns;
                $addA->nouveauManageur = $com;
                $addA->structure = $catins;
                $addA->user_action = session("utilisateur")->idUser;
                $addA->save();
                
                if ($request->type == "CEQP") {
                    $addT = new Trace();
                    $addT->libelleTrace = "Un chef d'équipe est devenu Chef d'une inspection existante dont le code hiérarchie est ".$insexistante;
                    $addT->user_action = session("utilisateur")->idUser;
                    $addT->save();

                    $supsup = Hierarchie::where("codeH", $insexistante)->first()->superieurH;

                    // Modification de l'inspection existante 
                    Hierarchie::where("codeH", $insexistante)->update([
                        'managerH' => $com
                    ]);

                    // Mise à jour du niveau, equipe, ins, région du nouveau manageur
                    Commerciaux::where('codeCom', $com)->update([
                        'Niveau' => $catins,
                        'codeEquipe' =>  "0000",
                        'codeInspection' => $insexistante,
                        'codeRegion' =>  $supsup,
                    ]);

                    flash('Changement effectué avec succès');
                    return redirect('/listcommerciaux');
                }

                if ($request->type == "CONS") {

                    $addT = new Trace();
                    $addT->libelleTrace = "Un conseiller est devenu Chef d'une équipe existante dont le code hiérarchie est ".$insexistante;
                    $addT->user_action = session("utilisateur")->idUser;
                    $addT->save();

                    $sup = Hierarchie::where('codeH', $insexistante)->first()->superieurH;
                    $supsup = Hierarchie::where("codeH", $sup)->first()->superieurH;

                    // Modification de l'inspection existante 
                    Hierarchie::where("codeH", $insexistante)->update([
                        'managerH' => $com
                    ]);

                    // Mise à jour du niveau, equipe, ins, région du nouveau manageur
                    Commerciaux::where('codeCom', $com)->update([
                        'Niveau' => $catins,
                        'codeEquipe' =>  $insexistante,
                        'codeInspection' => $sup,
                        'codeRegion' =>  $supsup,
                    ]);

                    flash('Changement effectué avec succès.');
                    return redirect('/listcommerciaux');
                }

                if ($request->type == "INS" ){

                    $addT = new Trace();
                    $addT->libelleTrace = "Un Chef d'inspection est devenu Chef d'une région existante dont le code hiérarchie est ".$insexistante;
                    $addT->user_action = session("utilisateur")->idUser;
                    $addT->save();

                    // Modification de l'inspection existante 
                    Hierarchie::where("codeH", $insexistante)->update([
                        'managerH' => $com
                    ]);

                    // Mise à jour du niveau, equipe, ins, région du nouveau manageur
                    Commerciaux::where('codeCom', $com)->update([
                        'Niveau' => $catins,
                        'codeEquipe' =>  "0000",
                        'codeInspection' => "0000",
                        'codeRegion' =>  $insexistante,
                    ]);

                    flash('Changement effectué avec succès.');
                    return redirect('/listcommerciaux');
                }

            }else{
                flash('Aucun fichier importer');
                return Back();
            }    
           
    }

    public static function getadhcom()
    {
        $affcom = AllTable::table('commerciauxes')->where('codeCom', request('id'))->first();
        $allequipe = AllTable::table('hierarchies')->where('structureH', trans('var.equipe'))->where('managerH', "!=", 0)->get();

        return view('admin.adheqp', compact('affcom', 'allequipe'));
    }

    public static function getadhcomc()
    {
        $affcom = AllTable::table('commerciauxes')->where('codeCom', request('id'))->first();
        $allequipe = AllTable::table('hierarchies')->where('structureH', trans('var.equipe'))->where('managerH', "!=", 0)->get();

        return view('admin.adheqp', compact('affcom', 'allequipe'));
    }

    public function setadhcom(Request $request)
    {
        if ($request->hasFile('note')) {
                $referenceNote = "REF-".str_replace(" ", "", $request->ref)."-ADHEREEQUIPE-".date('ymdhis');
                $namefile = $referenceNote.".pdf";
                $upload = "document/upload/";
                $request->file('note')->move($upload, $namefile);
                
                $path = $upload.$namefile;
                $desc = htmlspecialchars(trim($request->desc));
                $ref = htmlspecialchars(trim($request->ref));
                $dateeffet = htmlspecialchars(trim($request->dateeffet));

                // Enregistrement du motif de la modification de l'inspection existante
                $addA = new Avenant();
                $addA->codeHerarchieModifier = $request->codeC; // La ligne d'occurence qui a connu de changement
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

                // Code de l'équipe est $request->equipeselect

                // Recherche du supérieure
                $sup = Hierarchie::where('codeH', $request->equipeselect)->first()->superieurH;

                // Recherche du supérieure du supérieure
                $supsup = Hierarchie::where('codeH', $sup)->first()->superieurH;
                
                $supsupsup = Hierarchie::where('codeH', $supsup)->first()->superieurH;

                if($request->codeeexistant == ""){
                   Commerciaux::where('codeCom', $request->codeC)->update(
                        [
                            'codeEquipe' =>  $request->equipeselect,
                            'codeInspection' => $sup,
                            'codeRegion' =>  $supsup,
                            'codeCD' =>  $supsupsup,
                        ]);
                   flash(trans("flash.validaffectequipe"));
                   return Back();
                }else{
                    Commerciaux::where('codeCom', $request->codeC)->update(
                        [
                            'codeEquipe' =>  $request->equipeselect,
                            'codeInspection' => $sup,
                            'codeRegion' =>  $supsup,
                            'codeCD' =>  $supsupsup,
                        ]);
                    flash(trans("flash.validchangeequipe"));
                    return Back();
                }
            }else{
                flash('Aucun fichier importé')->error();
                return Back();
            }

    }

    public static function getlistcommerciaux()
    {
        
        $list = AllTable::getData('commerciauxes')->leftJoin('compteagents', 'commerciauxes.codeCom', '=', 'compteagents.Agent')->orderby("codeCom", "DESC");
        $allNiveau = AllTable::gettable('niveaux');
        $listPayement = DB::table('structures')->get();
        $exemple = "document/exemple/Exemplaire_fichier_commerciaux.xlsx";
        $search = "";

        if(request('rec') == 1){
            if(request('check') != "" && request('check') != null){
                $search = request('check');
                $list = $list->where('nomCom', 'like', '%'.request('check').'%')
                ->orwhere('prenomCom', 'like', '%'.request('check').'%')
                ->orwhere('codeCom', 'like', '%'.request('check').'%')->paginate(20);
                return view("admin.listcommerciaux", compact('list', 'allNiveau', 'exemple', 'search', 'listPayement'));
            }else{
                $list = $list->paginate(20);
                return view("admin.listcommerciaux", compact('list', 'allNiveau', 'exemple', 'search', 'listPayement'));
            }
        }

        $list = $list->paginate(20);
        return view('admin.listcommerciaux', compact('list', 'allNiveau', 'exemple', 'search', 'listPayement'));
    }

    public static function setfixe(Request $request)
    {
        $codeAgent = $request->codeagent;

        DB::table('compteagents')->where('Agent', $codeAgent)->update([
            'fixe' => $request->fixenew
        ]);
        TraceController::setTrace("Vous avez modifié le fixe du commercial dont le code est ".$codeAgent." de ".$request->fixenew." F CFA", session("utilisateur")->idUser);
        flash('Fixe modifié avec succès.');
        return Back();
    }

    public static function addcommerciaux(Request $request)
    {
        //dd(htmlspecialchars(trim($request->niv)));
        if (CommerciauxController::ExisteCommerciaux(htmlspecialchars(trim($request->mail)))) {
            flash("Le Commercial existe déjà!! ")->error();
            return Back();
        }
        else{
            $add = new Commerciaux();
            $add->codeCom = htmlspecialchars(trim($request->code));
            $add->nomCom = htmlspecialchars(trim($request->nom));
            $add->prenomCom =  htmlspecialchars(trim($request->prenom));
            $add->telCom = htmlspecialchars(trim($request->tel));
            $add->sexeCom = htmlspecialchars(trim($request->sexe));
            $add->adresseCom = htmlspecialchars(trim($request->adress));
            $add->mail = htmlspecialchars(trim($request->mail));
            $add->AIB = htmlspecialchars(trim($request->aib));
            $add->Niveau = htmlspecialchars(trim($request->niv));
            $add->action_save = 's';
            $add->user_action = session("utilisateur")->idUser;
            $add->save();
            
            $libBan  = htmlspecialchars(trim($request->compte));
            $modereglement = $request->mode;
            // Traitement compte
                        if(strtoupper($request->mode) != "")
                        {
                            if(strtoupper($request->mode) == "MOMO")
                            {
                                if(strtoupper($libBan) == "MTN" || strtoupper($libBan) == "MOOV"){
                                    $id_num = substr($request->numcompte, -8, 2);
                                    if(CommerciauxController::verifie_mtn($id_num) == 0)
                                        $libBan = "MTN";
                                    else
                                        if(CommerciauxController::verifie_moov($id_num) == 0)
                                            $libBan = "MOOV";
                                }else{
                                    flash("Règlement doit être MTN OU MOOV ")->error();
                                    return Back();
                                }
                            }else{
                                if(strtoupper($modereglement) == "BANQUE" || strtoupper($modereglement) == "VIREMENT" || strtoupper($modereglement) == "CHEQUE"){
                                    if($libBan == "DBB") $libBan = "NBB";
                                    if($libBan == "BAIC") $libBan = "BIIC";
                                    if($libBan == "BIBE") $libBan = "BIIC";
                                    if($libBan == "BHB") $libBan = "BOA";
                                    $sigle = DB::table('structures')->where('sigle', $libBan)->first();
                                    if(!isset($sigle->sigle))
                                    {
                                        flash("Le sigle du moyen de reglèment renseigner n'existe pas ")->error();
                                        return Back();
                                    }
                                }
                                else{
                                    flash("Règlement incorrect ")->error();
                                    return Back();
                                }
                            }
                        }
                        else{
                            flash("Mode de règlement obligatoire. ")->error();
                            return Back();
                        }

            // Créer un compte commercial
            $addC = new Compteagent();
            $addC->Agent = $add->id;
            $addC->libCompte = $libBan;
            $addC->numCompte = htmlspecialchars(trim($request->numcompte));
            $addC->ModePayement = $request->mode;
            $addC->save();

            if($request->agence == "AGENCE")
            flash("L'agence est enregistré avec succès. ")->success();
            else{
                TraceController::setTrace("Vous avez ajouté un commercial dont le code est ".$add->id, session("utilisateur")->idUser);
                flash("Le Commercial est enregistré avec succès. ")->success();
            }
            
            return Back();
        }
    }

    public static function setagence(Request $request)
    {
        flash("En cours de développement. ")->success();
        return Back();
    }

    public static function ExisteCommerciaux($libelle){
        $com = Commerciaux::where('mail', $libelle)->first();
        if (isset($com) && $com->mail!= 0) return true; else return false;
    }

    public function deletecommerciaux(Request $request)
    {
        $occurence = json_encode(Commerciaux::where('codeCom', request('id'))->first());
        $addt = new Trace();
        $addt->libelleTrace = "Commercial supprimé : ".$occurence;
        $addt->user_action = session("utilisateur")->idUser;
        $addt->save();

        Commerciaux::where('codeCom', request('id'))->delete();
        $info = "Le Commercial est supprimé avec succès."; flash($info); return Back();
    }

    public function getmodifcommerciaux(Request $request)
    {   
        //$allEquipe = AllTable::table('hierarchies')->where('structureH', trans("var.equipe"))->get();
        //$allIns = AllTable::table('hierarchies')->whereIn('structureH', trans("var.ins"));
        $modifiercom = AllTable::table('commerciauxes')->where('codeCom', request('id'))->first();
        //$allNiveau = AllTable::gettable('niveaux');
        $listPayement = DB::table('structures')->get();
        $compte = DB::table('compteagents')->select('libCompte', 'numCompte', 'ModePayement')->where('Agent', request('id'))->first();
        return view('admin.modifcommercial', compact('modifiercom', 'compte', 'listPayement'));
    }

    public function modifcommerciaux(Request $request)
    {
        $dataInfo = json_encode(Commerciaux::where('codeCom', $request->id)->first());
        $dataCompte = json_encode(Compteagent::where('Agent', $request->id)->first());
        $data = json_encode(array($dataInfo, $dataCompte));
        TraceController::setTrace("Existing sales (commercial) data : ".$data, session("utilisateur")->idUser);
                

        Commerciaux::where('codeCom', $request->id)->update(
                [
                    'nomCom' =>  htmlspecialchars(trim($request->nom)),
                    'prenomCom' =>  htmlspecialchars(trim($request->prenom)),
                    'telCom' =>  htmlspecialchars(trim($request->tel)),
                    'sexeCom' =>  htmlspecialchars(trim($request->sexe)),
                    'adresseCom' =>  htmlspecialchars(trim($request->adress)),
                    'mail' =>  htmlspecialchars(trim($request->mail)),
                    'AIB' =>  htmlspecialchars(trim($request->aib)),
                    'user_action' => session("utilisateur")->idUser,
                ]);
            $libBan = $request->ban;
            $modereglement = $request->mode;
        // Traitement compte
                        if(strtoupper($request->mode) != "")
                        {
                            if(strtoupper($request->mode) == "MOMO")
                            {
                                if(strtoupper($libBan) == "MTN" || strtoupper($libBan) == "MOOV"){
                                    $id_num = substr($request->numban, -8, 2);
                                    if(CommerciauxController::verifie_mtn($id_num) == 0)
                                        $libBan = "MTN";
                                    else
                                        if(CommerciauxController::verifie_moov($id_num) == 0)
                                            $libBan = "MOOV";
                                }else{
                                    flash("Règlement doit être MTN OU MOOV ")->error();
                                    return Back();
                                }
                            }else{
                                if(strtoupper($modereglement) == "BANQUE" || strtoupper($modereglement) == "VIREMENT" || strtoupper($modereglement) == "CHEQUE"){
                                    if($libBan == "DBB") $libBan = "NBB";
                                    if($libBan == "BAIC") $libBan = "BIIC";
                                    if($libBan == "BIBE") $libBan = "BIIC";
                                    if($libBan == "BHB") $libBan = "BOA";
                                    $sigle = DB::table('structures')->where('sigle', $libBan)->first();
                                    if(!isset($sigle->sigle))
                                    {
                                        flash("Le sigle du moyen de reglèment renseigner n'existe pas ")->error();
                                        return Back();
                                    }
                                }
                                else{
                                    flash("Mode de règlement incorrect ")->error();
                                    return Back();
                                }
                            }
                        }
                        else{
                            flash("Mode de règlement obligatoire. ")->error();
                            return Back();
                        }
                        
                                Compteagent::where('Agent', $request->id)->update([
                                    "libCompte" => $libBan,
                                    "numCompte" => htmlspecialchars(trim($request->numban)),
                                    "Modepayement" => $modereglement
                                ]);
            
        flash("Le Commercial est modifié avec succès. ")->success();
        TraceController::setTrace("Le Commercial est modifié avec succès.", session("utilisateur")->idUser);

        return Back();
        
    }

    public function setimputer(Request $request)
    {
        if($request->actions == "add"){
            Compteagent::where('Agent', $request->codeagent)->update([
                'avances' => $request->avancenew,
                'duree' => $request->avancenombr
            ]);
             
            flash("Avance imputé avec succès. ")->success();
            TraceController::setTrace("Vous avez ajouté une avance de ".$request->avancenew." au code commercial ".$request->codeagent, session("utilisateur")->idUser);
        }
        
        if($request->actions == "update"){
            $compteag = Compteagent::where('Agent', $request->codeagent)->first();
            $avanc = $compteag->avances + $request->avancenew;
            $dure = $compteag->duree + $request->avancenombr;
            Compteagent::where('Agent', $request->codeagent)->update([
                'avances' => $avanc,
                'duree' => $dure
            ]);
            $message = "Ancien avance : ".$compteag->avances.". Nouveau avance : ".$request->avancenew.". Total avances en cours : ".$avanc.". Avance modifier avec succès au code commercial ".$request->codeagent;
            flash($message)->success();
            TraceController::setTrace($message, session("utilisateur")->idUser);
        }
        return Back();
        
    }
    
    public function setannuleravance(Request $request){
        $compteag = Compteagent::where('Agent', $request->codeagent)->first();
        Compteagent::where('Agent', $request->codeagent)->update([
                'avances' => 0,
                'duree' => 0,
                'impayeravances' => 0,
                'anticiper' => 0
            ]);
        $message = "Ancien avance : ".json_encode($compteag);
        flash("Avance de ".$request->codeagent." annuler avec succès.")->success();
        TraceController::setTrace($message, session("utilisateur")->idUser);
        return Back();
    }

    public function settel(Request $request)
    {
        $tel = Compteagent::where('Agent', $request->codeagent)->first()->dotationTelephonie + $request->addnew;
        Compteagent::where('Agent', $request->codeagent)->update([
            'dotationTelephonie' => $tel
        ]);
         
        flash("Dotation Téléphonie ajouté avec succès. ")->success();
        TraceController::setTrace("Vous avez ajouté une dotation Téléphonie de ".$request->addnew, session("utilisateur")->idUser);
        return Back();
        
    }

    public function setcarb(Request $request)
    {
        $carb = Compteagent::where('Agent', $request->codeagent)->first()->dotationCarburant + $request->addnew;
        Compteagent::where('Agent', $request->codeagent)->update([
            'dotationCarburant' => $carb
        ]);
         
        flash("Dotation Carburant ajouté avec succès. ")->success();
        TraceController::setTrace("Vous avez ajouté une dotation Carburant de ".$request->addnew, session("utilisateur")->idUser);
        return Back();
        
    }
}
