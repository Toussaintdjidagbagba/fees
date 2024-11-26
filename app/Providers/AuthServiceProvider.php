<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use DB;
use Carbon\Carbon;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
		$perd = DB::table('societes')->first()->periode;
        view()->share('periode', $perd);
		
		$conversionperiodeenlettre = Carbon::createFromFormat('m-Y', $perd);
        $moisEnTexte = $conversionperiodeenlettre->formatLocalized('%B');
        $periodeenlettre = InterfaceServiceProvider::convertirMoisAnglaisEnFrancais($moisEnTexte) . ' ' . $conversionperiodeenlettre->year; 
        view()->share('periodelettre', $periodeenlettre);
    }
}
