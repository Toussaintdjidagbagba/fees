<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected $periode;
	
	public function __construct()
    {
        set_time_limit(720000000);
        ini_set('memory_limit', '5024M');
		ini_set('upload_max_filesize', '20M');
		$perd = DB::table('societes')->first()->periode;
		$this->periode = $perd;
    } 
}
