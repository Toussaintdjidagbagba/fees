<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\APIBaseController as APIBaseController;
use App\Http\Model\Tracecompte; 
use Validator;
use DB;
use App\Http\Import\ImportExcel;
use App\Providers\InterfaceServiceProvider;
use App\Http\FonctionControllers\Fonction;
use App\Http\Model\Commission;
use App\Http\Model\Contrat;
use App\Http\Model\Client;
use App\Http\Model\Commerciaux;
use App\Http\Model\Trace;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\SendMail;


class APIImportationCommission extends APIBaseController
{
    
    public function __construct()
    {
        set_time_limit(102400000);
        ini_set("memory_limit", "1024M");
    }
    
    public function uploadfilecommission(Request $request){
        if ($request->hasFile('data')) {
            $referenceNote = "ImporterCommissionHorsSunShine-".date('ymdhis');
            $namefile = $referenceNote.".xlsx";
            $upload = "document/upload/";
            $request->file('data')->move($upload, $namefile);
               
            $path = $upload.$namefile;

            $tab = Excel::toArray( new ImportExcel, $path);
            $commission = $tab[0];
            
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
            SendMail::sendnotification("emmanueldjidagbagba@gmail.com", [], "Importation des commissions hors sunshine avec succès.", [], "i");
            $message = "";
            return $this->sendResponse($message, 'Fichier Commission hors Sunshine importé avec succès.');
        }else{
            return $this->sendError("Le fichier n'existe pas.");
        }
    }
    
    public function index(Request $request)
    {
        $input = $request->all();

        /*$validator = Validator::make($input, [
            'data' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }*/
        
        $data = $request->data;
        
            $contrat = json_decode($data);
            $i=0;
            if ($request->hasFile('multipart')) 
                return $this->sendResponse("41", 'Update completed successfully.');
            else
                return $this->sendResponse($input, 'Update completed successfully.');
            
            $message = "";
            for ($i=0; $i < count($contrat); $i++) { 
                $contr = $contrat[$i];
                $contr_police = $contr->Police;
                $contr_produit = $contr->Produit;
                $contr_statut = $contr->Statut; // 
                $contr_nomassur = $contr->jaidenp_nomad; 
                $contr_prenomassur = $contr->jaidenp_pread;
                $contr_num_assur = $contr->N_assure; 
                $contr_payeur = $contr->N_Payeur; 
                
                $contr_dateffetdbut = ((strlen($contr->Date_Effet_Police)!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($contr->Date_Effet_Police - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($contr->Date_Effet_Police));
                $contr_dateffetfin = ((strlen($contr->Date_Fin_effet_Police)!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($contr->Date_Fin_effet_Police - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($contr->Date_Fin_effet_Police));
                $contr_agent = $contr->Jaagenp_winag;
                $contr_nomagent = $contr->Nom_Point_de_vente;
                $contr_codeinspection = $contr->jaagenp_winit;
                $contr_typeinspection = $contr->typeapporteur;
                $contr_fractionnement = $contr->Fraction;
                $contr_dateecheance = $contr_dateffetfin;

                // récuperer le libelle à partir du sigle fractionnement
                $codeFrac = Fonction::LibelleFractionnement($contr_fractionnement);

                // Vérification de l'unicité de la police
                if(Fonction::VérificationPolice($contr_police)){

                    // Créer une inspection en utilisant le code de structure
                    //Fonction::updatetypeStructure($contr_codeinspection, $contr_typeinspection);

                    // Nourri la table Produit 
                   // Fonction::saveProduit($contr_produit, $contr[2]);

                    // Enregistrer le client
                    Fonction::saveClient($contr_nomassur, $contr_prenomassur, $contr_num_assur, $contr_payeur);

                    // Vérification pour éliminer les valeurs null remarquer dans le fichier
                    if($contr_agent != "NULL" && $contr_produit != "NULL" && $contr_num_assur != "NULL" && $contr_agent != "" && $contr_produit != "" && $contr_num_assur != "")
                    {
                        $addContrat = new Contrat();
                        $addContrat->police = $contr_police;
                        $addContrat->Produit = $contr_produit;
                        $addContrat->Client = $contr_num_assur;
                        $addContrat->Agent = $contr_agent;
                        $addContrat->statutSunshine = $contr_statut;
                        $addContrat->DateDebutEffet = $contr_dateffetdbut;
                        $addContrat->DateFinEffet = $contr_dateffetfin;
                        $addContrat->DateEcheance = $contr_dateecheance;
                        $addContrat->fractionnement = $codeFrac;
                        $addContrat->user_action = 1;
                        $addContrat->save();
                    }
                // Les variables tels que agent, produit, nom agent ne pas utiliser ici et provienne de police. 
                //Police qui n'est d'autre que la clé de la table contrat.
                }else
                {
                    // Vérification des informations du contrat existant
                    //$message = "";
                    $ecap = Contrat::where('police', $contr_police)
                    ->where('Produit', $contr_produit)
                    ->where('Client', "!=", $contr_num_assur)
                    ->where('Agent', "!=", $contr_agent)
                    ->where('DateDebutEffet', "!=", $contr_dateffetdbut)
                    ->where('DateFinEffet', "!=", $contr_dateffetfin)
                    ->where('DateEcheance', "!=", $contr_dateecheance)
                    ->first();
                    if(isset($ecap->police)){
                        $message .= "Ce contrat existe déjà, mais les informations sont différentes par rapport au nouveau. Veuillez vérifier et mettre à jour les informations depuis l'interface contrat.";
                        $anc = Contrat::where('police', $contr_police)->first();
                        $message .= "Ancien [ Police : ".$anc->police." Code Produit : ".$anc->Produit." Code Client : ".$anc->Client." Code Agent : ".$anc->Agent." 
                        Date début Efet : ".$anc->DateDebutEffet." Date fin Effet : ".$anc->DateFinEffet." Date Echeance : ".$anc->DateEcheance." ] ";
                        
                        $message .= ". Nouveau [ Police : ".$contr_police." Code Produit : ".$contr_produit." Code Client : ".$contr_num_assur." 
                        Code Agent : ".$contr_agent." Date début Efet : ".$contr_dateffetdbut." Date fin Effet : ".$contr_dateffetfin." Date Echeance : ".$contr_dateecheance." ] ";
                    }
                    
                    
                    // Mettre à jour Contrat 
                    Contrat::where('police', $contr_police)->update([
                        "fractionnement" => $codeFrac,
                        "statutSunshine" => $contr_statut
                    ]);
                }
            }
        if($message == ""){
            return $this->sendResponse($message, 'Update completed successfully.');
        }else{
            return $this->sendError($message);
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