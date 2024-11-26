<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class ProcessusComInd extends Model
{
    //
    protected $primaryKey = "idpci";
    protected $table = "processuscominds";
    public $timestamps = true;
    
    public static function ini_ind(){
        $ini = new ProcessusComInd();
        $ini->typ = 'i';
        $ini->mois = view()->shared('periode');
        $ini->save();
    }
    
    public static function ini_groupe(){
        $ini = new ProcessusComInd();
        $ini->typ = 'g';
        $ini->save();
    }
    
    public static function saveprocessus($lib){
        
        switch ($lib) {
            case "imp":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["imp" => 1]);
                break;
            case "calc":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["calc" => 1]);
                break;
            case "valcalc":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["valcalc" => 1]);
                break;
            case "sp":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["sp" => 1]);
                break;
            case "csp":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["csp" => 1]);
                break;
            case "dt":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["dt" => 1]);
                break;
            case "dg":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["dg" => 1]);
                break;
            case "cdaf":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["cdaf" => 1]);
                break;
            case "tres":
                ProcessusComInd::where('typ', 'i')->where('mois', view()->shared('periode'))->update(["tres" => 1]);
                break;
        }
        
        return 0;
        
    }
    
    public static function saveprocessusgroupe($lib){
        
        switch ($lib) {
            case "imp":
                ProcessusComInd::where('typ', 'g')->update(["imp" => 1]);
                break;
            case "calc":
                ProcessusComInd::where('typ', 'g')->update(["calc" => 1]);
                break;
            case "valcalc":
                ProcessusComInd::where('typ', 'g')->update(["valcalc" => 1]);
                break;
            case "sp":
                ProcessusComInd::where('typ', 'g')->update(["sp" => 1]);
                break;
            case "csp":
                ProcessusComInd::where('typ', 'g')->update(["csp" => 1]);
                break;
            case "dt":
                ProcessusComInd::where('typ', 'g')->update(["dt" => 1]);
                break;
            case "dg":
                ProcessusComInd::where('typ', 'g')->update(["dg" => 1]);
                break;
            case "cdaf":
                ProcessusComInd::where('typ', 'g')->update(["cdaf" => 1]);
                break;
            case "tres":
                ProcessusComInd::where('typ', 'g')->update(["tres" => 1]);
                break;
        }
        
    }
}
