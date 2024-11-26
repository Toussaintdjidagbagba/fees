<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use App\Http\Model\Societe;
use App\Http\Model\Trace;

class SocieteController extends Controller
{
    //
    public function getsoc()
    {
        $soc = DB::table('societes')->first();
        $allpersonnel = DB::table('users')->get();
        return view('societe.listsoc', compact('soc', 'allpersonnel'));
    }

    public function setsoc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            /*'libelleSociete' => 'required|string',
            'email' => 'required|string',
            'adresse' => 'required|string', 
            'contact' => 'required|string', 
            'pied' => 'required|string',  */
            'aib' => 'required',
            'nonaib' => 'required',
			'mois' => 'required', 
        ]);
        if($validator->fails()){
            $err ='<ul>';
            foreach($validator->errors()->all() as $e){$err .="<li>$e</li>";}
            $err .='<ul>';
            flash($err)->error();
            return Back();
        }

        if ($request->hasFile('photologo')) {
            $extavatar  = $request->file('photologo')->getClientOriginalExtension();
            $namefile = "logo.".$extavatar;
            $upload = "document/profil/";
            $request->file('photologo')->move($upload, $namefile);
            $pathprofil = $upload.$namefile;

            Societe::where('id', 1)->update([
                "logo" => $pathprofil
                ]);
        }

        /*if ($request->hasFile('photosignature')) {
            $extavatar  = $request->file('photosignature')->getClientOriginalExtension();
            $namefile = "signature.".$extavatar;
            $upload = "document/profil/";
            $request->file('photosignature')->move($upload, $namefile);
            $pathsignature = $upload.$namefile;

            Societe::where('id', 1)->update([
                "signature" => $pathsignature
                ]);
        }*/

        if ($request->hasFile('aide')) {
            $extavatar  = $request->file('aide')->getClientOriginalExtension();
            $namefile = "Aide.".$extavatar;
            $upload = "document/profil/";
            $request->file('aide')->move($upload, $namefile);
            $pathaide = $upload.$namefile;

            Societe::where('id', 1)->update([
                "aidemanuel" => $pathaide
                ]);
        }

        if (request('sin') != 0) {
            $recupsignature = DB::table('users')->where('idUser', request('sin'))->first()->signature;
            Societe::where('id', 1)->update([
                "signature" => $recupsignature
                ]);
        }

        Societe::where('id', 1)->update([
            /*"libelleSociete" => request('libelleSociete'),
            "email" => request('email'),
            "contact" => request('contact'),
            "adresse" => request('adresse'),
            "piedpage" => request('pied'), */
            "tauxNonAIB" => request('nonaib'),
            "tauxAIB" => request('aib'),
			"periode" => date('m-Y', strtotime(request('mois').'-01'))
        ]);

        flash('Information de société mise à jour avec succès.');
        return Back();
    }

}

