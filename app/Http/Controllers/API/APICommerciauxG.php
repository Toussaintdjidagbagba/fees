<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Providers\InterfaceServiceProvider;


class APICommerciauxG extends APIBaseController
{
    public function index(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'Commercial' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $dataCom = DB::table('commerciauxes')->where('codeCom', $request->Commercial)->first();
        
        if(isset($dataCom->codeCom))
        {
            $tabcomm = array();
            
            $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $dataCom->codeEquipe)->first();
            if(isset($magEquipe->managerH)) $codechefequipe = $magEquipe->managerH; else $codechefequipe = "";
                
            $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $dataCom->codeInspection)->first();
            if(isset($magIns->managerH)) $codechefins = $magIns->managerH; else $codechefins = "";
            
            $tabmembre = array();
            $libNiveau = "Conseiller";
            
            // Cas d'un chef d'équipe
            if($dataCom->Niveau == "CEQP"){
                $libNiveau = "Chef d'équipe";
                $allmembres = DB::table('commerciauxes')->where('codeEquipe', $dataCom->codeEquipe)->get();
            
                foreach($allmembres as $com)
                {
                    $magEquipeCom = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $com->codeEquipe)->first();
                    if(isset($magEquipeCom->managerH)) $chefequipe = $magEquipeCom->managerH; else $chefequipe = "";
                
                    $tabComM = [
                        'Commercial' => $com->codeCom,
                        'Nom' => $com->nomCom,
                        'Prenom' => $com->prenomCom,
                        'Tel' => $com->telCom,
                        'MagEquipe' => $chefequipe
                        ]; 
                    array_push($tabmembre, $tabComM);
                }
            }
            
            // Cas d'un inspecteur
            if($dataCom->Niveau == "INS"){
                $libNiveau = "Inspecteur";
                $allmembres = DB::table('commerciauxes')->where('Niveau', 'CEQP')->where('codeInspection', $dataCom->codeInspection)->get();
            
                foreach($allmembres as $com)
                {
                    
                    $magCIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $com->codeInspection)->first();
                    if(isset($magCIns->managerH)) $chefins = $magCIns->managerH; else $chefins = "";
                    
                    $magEquipeCom = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $com->codeEquipe)->first();
                    if(isset($magEquipeCom->managerH)) $chefequipe = $magEquipeCom->managerH; else $chefequipe = "";
                    
                    $tabEIC = array();
                    
                    // Cas d'un chef d'équipe
                    if($com->Niveau == "CEQP"){
                        $allmembress = DB::table('commerciauxes')->where('codeEquipe', $com->codeEquipe)->get();
                    
                        foreach($allmembress as $comm)
                        {
                            $magEquipeComEC = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $comm->codeEquipe)->first();
                            if(isset($magEquipeComEC->managerH)) $chefequipeEC = $magEquipeComEC->managerH; else $chefequipeEC = "";
                        
                            $tabComMEIC = [
                                'Commercial' => $comm->codeCom,
                                'Nom' => $comm->nomCom,
                                'Prenom' => $comm->prenomCom,
                                'Tel' => $comm->telCom,
                                'Niveau' => "Conseiller",
                                'MagEquipe' => $chefequipeEC
                                ]; 
                            array_push($tabEIC, $tabComMEIC);
                        }
                    }
                
                    $tabComM = [
                        'Commercial' => $com->codeCom,
                        'Nom' => $com->nomCom,
                        'Prenom' => $com->prenomCom,
                        'Tel' => $com->telCom,
                        'Niveau' => "Chef d'équipe",
                        'MagEquipe' => $chefequipe,
                        'MagIns' => $chefins, 
                        'Membres' => $tabEIC
                        ]; 
                    array_push($tabmembre, $tabComM);
                }
            }
            
            $tabdata = [
                'Commercial' => $dataCom->codeCom, 
                'Nom' => $dataCom->nomCom, 
                'Prenom' => $dataCom->prenomCom,
                'Tel' => $dataCom->telCom,
                'Niveau' => $libNiveau,
                'MagEquipe' => $codechefequipe, 
                'MagIns' => $codechefins,
                'Membres' => $tabmembre
            ];
            
            array_push($tabcomm, $tabdata);
            
            return $this->sendResponse($tabcomm, 'The commercial information is successfully retrieved.');
        }else{
            return $this->sendError('No commercial register under this code.');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //return $this->sendResponse($id, 'Tag deleted successfully.');
    }
}