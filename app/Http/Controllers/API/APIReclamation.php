<?php
namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Providers\InterfaceServiceProvider;


class APIReclamation extends APIBaseController
{
    public function index(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            /*'police' => 'required',
            'client' => 'required',
            'quittance' => 'required',*/
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $dataCom = DB::table('commissions')
                    ->join("contrats", "contrats.police", "=", "commissions.NumPolice")
                    ->join("clients", "clients.idClient", "=", "contrats.Client")
                    ->select('contrats.police as police', 'clients.nom as nom', 'clients.prenom as prenom', 'commissions.NumQuittance as quittance',
                    "commissions.Statut as periode", "commissions.statutcalculer as etat", "commissions.DateDebutQuittance as periodequittance");
        if($request->police != "")
                    $dataCom = $dataCom->where('contrats.police', 'like', '%'.$request->police.'%');
        if($request->quittance != "")
                    $dataCom = $dataCom->where('commissions.NumQuittance', 'like', '%'.$request->quittance.'%');
        if($request->client != "")
                    $dataCom = $dataCom->whereRaw(" (clients.nom like '%".$request->client."%' or clients.prenom like '%".$request->client."%' or clients.idClient like '%".$request->client."%')");
                    
        
        $dataCom = $dataCom->get();
        
        if(isset($dataCom))
        {   
            return $this->sendResponse($dataCom, 'The reclamation information is successfully retrieved.');
        }else{
            return $this->sendError('No reclamation register under this code.');
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