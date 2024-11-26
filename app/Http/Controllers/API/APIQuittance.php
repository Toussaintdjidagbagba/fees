<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Providers\InterfaceServiceProvider;


class APIQuittance extends APIBaseController
{
    public function index(Request $request)
    {
        
        $datas = DB::table('commissions')->join("contrats", "contrats.police", "=", "commissions.NumPolice")->select('NumQuittance as quittance', 'contrats.Produit as produit')->get();
            
            
        return $this->sendResponse($datas, 'The information is successfully retrieved.');
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