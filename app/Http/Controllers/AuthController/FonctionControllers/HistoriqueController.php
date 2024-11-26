<?php

namespace App\Http\FonctionControllers;

use Illuminate\Http\Request;
use App\Http\Model\Hierarchie;
use App\Http\Model\Trace;
use DB;

/**
 * 
 */
class HistoriqueController
{
	
	function __construct()
	{
		
	}

	public static function saveTrace($message)
	{
		$addT = new Trace();
        $addT->libelleTrace = $message;
        $addT->user_action = session("utilisateur")->idUser;
        $addT->save();
	}

}