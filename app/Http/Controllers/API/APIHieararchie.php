<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Providers\InterfaceServiceProvider;


class APIHieararchie extends APIBaseController
{
    public function index(Request $request)
    {
        $input = $request->all();

        if(isset($request->Commercial)){
            
            $validator = Validator::make($input, [
                'Commercial' => 'integer',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
            
            $dataCom = DB::table('commerciauxes')->where('codeCom', $request->Commercial)->first();
            
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
                
                array_push($tabcomm, $tabdata);
                
                return $this->sendResponse($tabcomm, 'The commercial information is successfully retrieved.');
            }else{
                return $this->sendError('No commercial register under this code.');
            }
        }else{
            $datas = DB::table('commerciauxes')->whereNotIn("codeCom", [1,2,3])->get();
            $tabcomm = array();
            for($i = 0; $i < count($datas); $i++){
                $dataCom = DB::table('commerciauxes')->where('codeCom', $datas[$i]->codeCom)->first();
                $magEquipe = ""; $magIns = ""; $magRg = ""; $magcd = "";
                $codechefequipe = ""; $nomchefequipe = ""; $prenomchefequipe = "";
                $codechefins = ""; $nomchefins = ""; $prenomchefins = "";
                $codechefrg = ""; $nomchefrg = ""; $prenomchefrg = "";
                $codecd = ""; $nomchefcd = ""; $prenomchefcd = "";
                if(isset($dataCom->codeCom))
                {
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
                    
                    array_push($tabcomm, $tabdata);
                }
            }
            
            return $this->sendResponse($tabcomm, 'The commercial information is successfully retrieved.');
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