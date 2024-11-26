<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Trace;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Providers\InterfaceServiceProvider;


class APIImportationCommissionGroupe extends APIBaseController
{
    public function index(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'data' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $data = $request->data;
    
            $commission = $data;
            
            for ($i=1; $i < count($commission); $i++) { 
                $comm = $commission[$i];
                $num_commission = Fonction::genererNumCommission();
                $num_agent = $comm[0];
                $structure = $comm[1]; // code d'inspection
                $niveau = $comm[2]; // Niveau par défaut ici sera CONS
                $police = $comm[3]; // numéro contrat
                $num_produit = $comm[4]; // numéro du produit
                $num_quittance = $comm[5]; // quittance
                $date_production = $comm[6];
                $base_commission = $comm[7];
                $index = $comm[8];
                $DateDebutQuittance = ((strlen($comm[9])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($comm[9] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($comm[9]));
                $DateFinQuittance = ((strlen($comm[10])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($comm[10] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($comm[10]));

                if ($index == "S") {

                    // Créer une inspection en utilisant le code de structure
                    if($structure != "" && $structure != null)
                    Fonction::saveStructure($structure, "");

                    if(isset(Commission::where("NumCommission", $num_commission)->first()->NumCommission)){
                        $newBaseCom = Commission::where("NumCommission", $num_commission)->first()->BaseCommission + $base_commission;
                        $newmontSunshine = Commission::where("NumCommission", $num_commission)->first()->MontantSunShine + $montant_commission;

                        Commission::where("NumCommission", $num_commission)->update([
                            "BaseCommission" => $newBaseCom,
                            "MontantSunShine" => $newmontSunshine
                        ]);
                    }else{
                        if(!isset(Commission::where("NumQuittance", $num_quittance)->first()->NumCommission)){
                            $add = new Commission();
                            $add->Apporteur = $num_agent;
                            $add->NumCommission = $num_commission;
                            $add->NumPolice = $police;
                            $add->DateCreation = date('d-m-Y');
                            $add->BaseCommission = $base_commission;
                            $add->NumQuittance = $num_quittance;
                            $add->DateDebutQuittance = $DateDebutQuittance;
                            $add->DateFinQuittance = $DateFinQuittance;
                            $add->IndexQuittance = $index;
                            $add->DateProduction = $date_production;
                            $add->ncom = 0;
                            $add->ctrl = 0;
                            $add->TypeCommission = 'i';
                            $add->Statut = date('m-Y');
                            $add->moiscalculer = date('m-Y');
                            $add->save();
                        }
                    }
                }
                // Les variables tels que agent, produit, nom agent ne pas utiliser ici et provienne de police. Police qui n'est d'autre que la clé de la table contrat.
            }
            
        
        if(0){
            return $this->sendResponse($tabcomm, 'Update completed successfully.');
        }else{
            return $this->sendError('Update not done.');
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