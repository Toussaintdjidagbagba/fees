<?php

namespace App\Http\FonctionControllers;

use Illuminate\Http\Request;
use App\Http\Model\Hierarchie;
use App\Http\Model\Commerciaux;
use DB;

/**
 * 
 */
class VerificationController
{
	
	function __construct()
	{
		
	}

	public static function ExistanceSupIns()
	{
		$bool = DB::table("hierarchies")
			->where("statut", "!=", "sup")
			->where("structureH", "RG")->first();
		if ( !isset($bool) ) { return true;}
		
	}

	public static function ExistanceSupCEQP()
	{
		$bool = DB::table("hierarchies")
			->where("statut", "!=", "sup")
			->whereNotIn("structureH", ["RG", "CONS", "CEQP"])->first();
		if ( !isset($bool) ) { return true;}
		
	}

	public static function gettable($value)
	{
		return DB::table($value)->where("statut", "!=", "sup")->get();
	}

	public static function ExisteInspection($libelle, $manager, $struct){
        $ins = Hierarchie::where('codeH', $libelle)->where("managerH",  $manager)->where('structureH', $struct)->where("statut", "!=", "sup")->first();
        if (isset($ins) && $ins->codeH != 0) return true; else return false;
    }

    public static function ExisteInspectionSansManageur($libelle, $struct){
        $ins = Hierarchie::where('codeH', $libelle)->where('structureH', $struct)->where("statut", "!=", "sup")->first();
        if (isset($ins) && $ins->codeH != 0) return true; else return false;
    }

    public static function ExisteEquipe($libelle, $manager, $struct){
        $qp = Hierarchie::where('codeH', $libelle)
        ->where("managerH",  $manager)
        ->where('structureH', $struct)
        ->where("statut", "!=", "sup")->first();
        if (isset($qp) && $qp->codeH != 0) return true; else return false;
    }

    public static function ExisteEquipeSansManageur($libelle, $struct){
        $qp = Hierarchie::where('codeH', $libelle)
        ->where('structureH', $struct)
        ->where("statut", "!=", "sup")->first();
        if (isset($qp) && $qp->codeH != 0) return true; else return false;
    }

}