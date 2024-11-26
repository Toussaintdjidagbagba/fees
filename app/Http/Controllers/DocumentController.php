<?php

namespace App\Http\Controllers;

use App\Http\Model\Document;
use Illuminate\Http\Request;
use DB;

class DocumentController extends Controller
{
    //
    public function getlistDocument() 
    {
        $list = Document::orderBy('id', "DESC")->paginate(100);
        
        return view('document.listdoc', compact('list'));
    }
    
    public function setlistDocument(Request $request){
        $check = implode('-',array_reverse  (explode('-',request('check'))));
        $list = Document::orderBy('id', "DESC");
        
            if(request('agentcheck') != "" && request('agentcheck') != null && $check != 0 && $check != null){
                $list = $list->where('periode', $check)->where('documents.Agent', request('agentcheck'))->paginate(100);
                return view("document.listdoc", compact('list'));
            }else
                //dd($check);
                if($check != "" && $check != null){
                    $list = $list->where('periode', $check)->paginate(100);
                    return view("document.listdoc", compact('list'));
                }else
                    if(request('agentcheck') != "" && request('agentcheck') != null){
                        $list = $list->where('documents.Agent', request('agentcheck'))->paginate(100);
                        return view("document.listdoc", compact('list'));
                    }else{
                        $list = $list->paginate(100);
                        return view("document.listdoc", compact('list'));
                    }
    }
    
    public function renvoiefiche(Request $request){
                $agents = DB::table('documents')->where('periode', $request->periode)->where('Agent', $request->codeagent)->first();
                 //dd($agents);
                if(isset($agents->Agent)){
                    $fiche_O = $agents->path;
                    $detail_O = $agents->pathD;
                    $email = DB::table("commerciauxes")->where('codeCom', $agents->Agent)->first()->mail;
                    $email = strtolower(str_replace(' ', '', htmlspecialchars(trim($email))));
                    if (isset($email) && $email != "") {
                        $data = ["fiche" => $fiche_O, "detail" => $detail_O, 'periode' => $request->periode];
						//$email = "emmanueldjidagbagba@gmail.com";
                        SendMail::resendFicheCommission($email, "fiche de paie", $data);
                        
                        DB::table('documents')->where('Agent', $agents->Agent)->where('path', $fiche_O)->update(['statut' => "true"]);
                    }  
                }
                $comm = DB::table("commerciauxes")->where('codeCom', $agents->Agent)->first();
        flash('Fiche de paie de la période '.$request->periode.' du commercial '.$comm->nomCom.' '.$comm->prenomCom.' renvoyé avec succès.');
        return Back();
    }
} 
