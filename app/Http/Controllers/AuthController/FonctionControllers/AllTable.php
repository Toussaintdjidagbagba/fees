<?php

namespace App\Http\FonctionControllers;

use Illuminate\Http\Request;
use DB;

/**
 * 
 */
class AllTable
{
	
	function __construct()
	{
		
	}

	public static function table($value)
	{
		return DB::table($value)->where("statut", "!=", "sup");
	}

	public static function getData($value)
	{
		return DB::table($value);
	}

	public static function gettable($value)
	{
		return DB::table($value)->where("statut", "!=", "sup")->get();
	}
}