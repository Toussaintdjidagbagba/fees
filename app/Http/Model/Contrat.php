<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Model\Commission;

class Contrat extends Model
{
    //
    /*
    protected $with = ['commissions'];

	public function commissions()
	{
		return $this->belongsTo(Commission::class)->withTrashed();
	}*/
}
