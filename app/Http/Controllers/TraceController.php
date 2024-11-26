<?php

namespace App\Http\Controllers;

use App\Http\Model\Trace;
use Illuminate\Http\Request;
use DB;

class TraceController extends Controller
{
    //
    public function getlist() 
    {
        $list = Trace::orderBy('codeTrace', "DESC")->paginate(100);            
        return view('traces.listtrace', compact('list'));
    }

    public static function setTrace($contenu , $user)
    {
        $add = new Trace();
        $add->user_action = $user;
        $add->libelleTrace = $contenu;
        $add->save();

        return 0;
    }
} 
