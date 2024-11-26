<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use App\Http\Model\Menu;
use App\Http\Model\ActionMenu;
use App\Providers\InterfaceServiceProvider;
use App\Http\Model\Trace;

class MenuController extends Controller
{
    //
    public function getmenu() 
    { 
        
            $list = DB::table('menus');
            $search = "";
            if(request('rec') == 1){
                if(request('check') != "" && request('check') != null){
                    $search = request('check');
                    $list = $list->where('libelleMenu', 'like', '%'.request('check').'%')
                    ->orwhere('titre_page', 'like', '%'.request('check').'%')->paginate(20);
                    return view('menu.listmenu', compact('list', 'search'));
                }else{
                    $list = $list->paginate(20);
                    return view("menu.listmenu", compact('list', 'search'));
                }
            }

            $list = $list->paginate(20);
            return view('menu.listmenu', compact('list', 'search'));

    }

    public function setactionmenu(Request $request)
    {
        if (!in_array("delete_menu", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $addaM = new ActionMenu();
            $addaM->Menu = request('menu');
            $addaM->action = request('actio');
            $addaM->code_dev = request('actiondev');
            $addaM->save();

            flash('Action "'.request('actio').'" ajouté à '.InterfaceServiceProvider::libmenu(request('menu')).' effectuée avec succès.');
            TraceController::setTrace(
                'Action "'.request('actio').'" ajouté à '.InterfaceServiceProvider::libmenu(request('menu')).' effectuée avec succès.',
                session("utilisateur")->idUser);
            return Back();
        }
    }

    public function setmenu(Request $request)
    {
        if (!in_array("add_menu", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $validator = Validator::make($request->all(), [
                'lib' => 'required|string',
                'titre' => 'required|string',
                'parent' => 'required|integer',
                'rout' => 'required|string',
                'pos' => 'required|integer',
            ]);

            if($validator->fails()){
                $err ='<ul>';
                //dd($validator->errors()->all());
                $i = 0;
                foreach($validator->errors()->all() as $e){
                    if ($i == 0) {
                        $err .="<li>Le libellé Menu est important</li>";
                    }
                    if ($i == 1) {
                        $err .="<li>Le titre est important</li>";
                    }
                    //$err .="<li>$e</li>";
                    $i++;
                }
                $err .='<ul>';
                flash($err)->error();
                return Back();
            }

            $addMenu = new Menu();
            $addMenu->libelleMenu = $request->lib;
            $addMenu->titre_page = $request->titre;
            $addMenu->Topmenu_id = $request->parent;
            $addMenu->num_ordre = $request->pos;
            $addMenu->route = $request->rout;
            $addMenu->iconee = $request->icon;
            $addMenu->user_action = session("utilisateur")->idUser;
            $addMenu->save();

            TraceController::setTrace(
                'Vous avez enregistré le menu '.$request->lib.'.',
                session("utilisateur")->idUser);

            flash('Menu enregistré avec succès.');
            return Back();
        }
    }

    public function delmenu(Request $request)
    {
        if (!in_array("delete_menu", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $occurence = json_encode(Menu::where('idMenu', request('id'))->first());
            $addt = new Trace();
            $addt->libelleTrace = "Menu supprimé : ".$occurence;
            $addt->user_action = session("utilisateur")->idUser;
            $addt->save();
            Menu::where('idMenu', request("id"))->delete();
            flash(trans('flash.deletemenusucces')); return Back();
        }
    }

    public function getmodifmenu(Request $request)
    {
        if (!in_array("update_menu", session("auto_action"))) {
            return view("vendor.error.649");
        }else{
            $info = DB::table("menus")->where('idMenu', $request->id)->first();
            $list = Menu::get();
            return view('admin.modifmenu', compact('info', 'list'));
        }
    }

    public function setmodifmenu(Request $request)
    {
        if (!in_array("update_menu", session("auto_action"))) {
            return view("vendor.error.649");
        }else{

            $validator = Validator::make($request->all(), [
                'lib' => 'required|string',
                'titre' => 'required|string',
                'parent' => 'required|integer',
                'rout' => 'required|string',
                
                'pos' => 'required|integer',
            ]);

            if($validator->fails()){
                $err ='<ul>';
                //dd($validator->errors()->all());
                $i = 0;
                foreach($validator->errors()->all() as $e){
                    if ($i == 0) {
                        $err .="<li>Le libellé Menu est important</li>";
                    }
                    if ($i == 1) {
                        $err .="<li>Le titre est important</li>";
                    }
                    //$err .="<li>$e</li>";
                    $i++;
                }
                $err .='<ul>';
                flash($err)->error();
                return Back();
            }

            Menu::where('idMenu', $request->id)->update([
                'libelleMenu' => $request->lib,
                'titre_page' => $request->titre,
                'Topmenu_id' => $request->parent,
                'num_ordre' => $request->pos,
                'route' => $request->rout,
                'iconee' => $request->icon,
            ]);

            TraceController::setTrace(
                'Vous avez modifié le menu '.$request->lib.'.',
                session("utilisateur")->idUser);


            flash('Menu mise à jour avec succès');
            return redirect('/listmenu');
        }
    }
}

