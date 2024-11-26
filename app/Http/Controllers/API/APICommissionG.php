<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Providers\InterfaceServiceProvider;


class APICommissionG extends APIBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     /* 
        public function index(Request $request)
        {
        $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->orderBy("tracecomptes.Commercial", "desc")->get();
        
        $tabcomm = array();
        foreach($list as $com)
        {
            $comp = InterfaceServiceProvider::RecupCompteAncien($com->Commercial, $com->moiscalculer);
            
            $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $com->codeEquipe)->first();
                if(isset($magEquipe->managerH)) $codechefequipe = $magEquipe->managerH; else $codechefequipe = "";
                
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $com->codeInspection)->first();
                if(isset($magIns->managerH)) $codechefins = $magIns->managerH; else $codechefins = "";
                
            
            $tabdata = ['Commercial' => $com->Commercial, 'Name' => $com->nomCom.' '.$com->prenomCom, 'Compte' => $comp['compte'], 'Mois' => $com->moiscalculer, 'MagEquipe' => $codechefequipe, 'MagIns' => $codechefins];
            
            array_push($tabcomm, $tabdata);
        }
        
        return $this->sendResponse($tabcomm, 'All commissions collected successfully recovered.');
    }
     */
    public function index(Request $request)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'Commercial' => 'required',
            'Mois' => 'required'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')
        ->where('tracecomptes.moiscalculer', $request->Mois)->where('tracecomptes.Commercial', $request->Commercial)->orderBy("tracecomptes.Commercial", "desc")->get();
        
        $tabcomm = array();
        foreach($list as $com)
        {
            $comp = InterfaceServiceProvider::RecupCompteAncien($com->Commercial, $com->moiscalculer);
            
            $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $com->codeEquipe)->first();
                if(isset($magEquipe->managerH)) $codechefequipe = $magEquipe->managerH; else $codechefequipe = "";
                
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $com->codeInspection)->first();
                if(isset($magIns->managerH)) $codechefins = $magIns->managerH; else $codechefins = "";
                
            // Tel Commercial
            // Membre d'un equipe 
            // Membre d'une inspection
            $tabdata = ['Commercial' => $com->Commercial, 'Name' => $com->nomCom.' '.$com->prenomCom, 'Compte' => $comp['compte'], 'Mois' => $com->moiscalculer, 'MagEquipe' => $codechefequipe, 'MagIns' => $codechefins];
            
            array_push($tabcomm, $tabdata);
        }
        
        if(sizeof($tabcomm) != 0)
            return $this->sendResponse($tabcomm, 'The commissions of the commercial are recovered successfully.');
        else
            return $this->sendError('No commissions available for this commercial or the commercial does not exist.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        $input = $request->all();


        $validator = Validator::make($input, [
            'Commercial' => 'required',
            'Mois' => 'required'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')
        ->where('tracecomptes.moiscalculer', $request->Mois)->where('tracecomptes.Commercial', $request->Commercial)->orderBy("tracecomptes.Commercial", "desc")->get();
        
        $tabcomm = array();
        foreach($list as $com)
        {
            $comp = InterfaceServiceProvider::RecupCompteAncien($com->Commercial, $com->moiscalculer);
            
            $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $com->codeEquipe)->first();
                if(isset($magEquipe->managerH)) $codechefequipe = $magEquipe->managerH; else $codechefequipe = "";
                
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $com->codeInspection)->first();
                if(isset($magIns->managerH)) $codechefins = $magIns->managerH; else $codechefins = "";
                
            
            $tabdata = ['Commercial' => $com->Commercial, 'Name' => $com->nomCom.' '.$com->prenomCom, 'Compte' => $comp['compte'], 'Mois' => $com->moiscalculer, 'MagEquipe' => $codechefequipe, 'MagIns' => $codechefins];
            
            array_push($tabcomm, $tabdata);
        }
        
        if(sizeof($tabcomm) != 0)
            return $this->sendResponse($tabcomm, 'The commissions of the commercial are recovered successfully.');
        else
            return $this->sendError('No commissions available for this commercial or the commercial does not exist.');
            
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $list = DB::table('tracecomptes')->join('commerciauxes', 'commerciauxes.codeCom', '=', 'tracecomptes.Commercial')->where('tracecomptes.Commercial', $id)->orderBy("tracecomptes.Commercial", "desc")->get();
        
        $tabcomm = array();
        foreach($list as $com)
        {
            $comp = InterfaceServiceProvider::RecupCompteAncien($com->Commercial, $com->moiscalculer);
            
            $magEquipe = DB::table('hierarchies')->where('structureH', 'CEQP')->where('codeH', $com->codeEquipe)->first();
                if(isset($magEquipe->managerH)) $codechefequipe = $magEquipe->managerH; else $codechefequipe = "";
                
                $magIns = DB::table('hierarchies')->whereIn('structureH', trans('var.ins'))->where('codeH', $com->codeInspection)->first();
                if(isset($magIns->managerH)) $codechefins = $magIns->managerH; else $codechefins = "";
                
            
            $tabdata = ['Commercial' => $com->Commercial, 'Name' => $com->nomCom.' '.$com->prenomCom, 'Compte' => $comp['compte'], 'Mois' => $com->moiscalculer, 'MagEquipe' => $codechefequipe, 'MagIns' => $codechefins];
            
            array_push($tabcomm, $tabdata);
        }
        
        if(sizeof($tabcomm) != 0)
            return $this->sendResponse($tabcomm, 'The commissions of the commercial are recovered successfully.');
        else
            return $this->sendError('No commissions available for this commercial or the commercial does not exist.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
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