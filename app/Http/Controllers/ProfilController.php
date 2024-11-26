<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Model\User as Users;
use Illuminate\Support\Facades\Session;

class ProfilController extends Controller
{
    //

    public function getprofil()
    {
        return view('profil.afficheprofil');
    }

    public function setprofil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'log' => 'required|string',
            'nom' => 'required|string',
            'prenom' => 'required|string',
        ]);
        if($validator->fails()){
            $err ='<ul>';
            foreach($validator->errors()->all() as $e){$err .="<li>$e</li>";}
            $err .='<ul>';
            flash($err)->error();
            return Back();
        }


        if ($request->hasFile('photoavatar')) {
            $extavatar  = $request->file('photoavatar')->getClientOriginalExtension();
            $namefile = "PHOTO".session("utilisateur")->nom."_".session("utilisateur")->idUser.".".$extavatar;
            $upload = "document/profil/";
            $request->file('photoavatar')->move($upload, $namefile);
            $pathprofil = $upload.$namefile;

            Users::where('idUser', session("utilisateur")->idUser)->update([
                "image" => $pathprofil
                ]);
        }

        if ($request->hasFile('photosignature')) {
            $extavatar  = $request->file('photosignature')->getClientOriginalExtension();
            $namefile = "SIGNATURE_".session("utilisateur")->nom."_".session("utilisateur")->idUser.".".$extavatar;
            $upload = "document/profil/";
            $request->file('photosignature')->move($upload, $namefile);
            $pathsignature = $upload.$namefile;

            Users::where('idUser', session("utilisateur")->idUser)->update([
                "signature" => $pathsignature
                ]);
        }

        Users::where('idUser', session("utilisateur")->idUser)->update([
            "login" => request('log'),
            "nom" => request('nom'),
            "prenom" => request('prenom'),
            "adresse" => request('adr'),
            "tel" => request('tel'),
            "mail" => request('email'),
            "other" => request('autr')
        ]);

        $user = Users::where('idUser',session("utilisateur")->idUser)->first();

        Session::put('utilisateur', $user);

        flash('Profil mise à jour avec succès.');
        return Back();
    }
}
