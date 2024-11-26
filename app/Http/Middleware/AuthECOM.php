<?php

namespace App\Http\Middleware;
use App\Http\FonctionControllers\VerificationController;
use App\Http\Model\Hierarchie;
use App\Http\Model\Commerciaux;
use App\Http\Model\Compteagent;
use Closure;

class AuthECOM
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        
        if (!isset(session("utilisateur")->idUser)) {
            return redirect('login');
        }elseif(session('DateConnexion') != date('Y-m-d') ){
            return redirect('login');
        }

        // Vérification par défaut de supérieur hierarchie pour l'équipe.
        // Dans ce cas, nous prenons INS par défaut
        if (VerificationController::ExistanceSupCEQP()) {

            // Par défaut
            $add = new Commerciaux();
            $add->nomCom = "Inspection";
            $add->prenomCom =  "Par défaut";
            $add->telCom = "-";
            $add->sexeCom = "";
            $add->adresseCom = "Cotonou";
            $add->mail = "";
            $add->AIB = "";
            $add->codeEquipe = "0000";
            $add->codeInspection = "8888";
            $add->codeRegion = "9999";
            $add->Niveau = "INS";
            $add->action_save = 's';
            $add->user_action = session("utilisateur")->idUser;
            $add->save();

            $addH = new Hierarchie();
            $addH->codeH = "8888";
            $addH->libelleH =  "";
            $addH->villeH =  "Cotonou";
            $addH->managerH =  $add->id;
            $addH->structureH =  "INS";
            $addH->superieurH =  "8888";
            $addH->user_action = session("utilisateur")->idUser;
            $addH->save();

            // Créer un compte commercial
            $addC = new Compteagent();
            $addC->Agent = $add->id;
            $addC->save();

            // Par défaut
            $add = new Commerciaux();
            $add->nomCom = "Equipe";
            $add->prenomCom =  "Par défaut";
            $add->telCom = "-";
            $add->sexeCom = "";
            $add->adresseCom = "Cotonou";
            $add->mail = "";
            $add->AIB = "";
            $add->codeEquipe = "7777";
            $add->codeInspection = "8888";
            $add->codeRegion = "9999";
            $add->Niveau = "CEQP";
            $add->action_save = 's';
            $add->user_action = session("utilisateur")->idUser;
            $add->save();

            $addH = new Hierarchie();
            $addH->codeH = "7777";
            $addH->libelleH =  "";
            $addH->villeH =  "Cotonou";
            $addH->managerH =  $add->id;
            $addH->structureH =  "CEQP";
            $addH->superieurH =  "7777";
            $addH->user_action = session("utilisateur")->idUser;
            $addH->save();

            // Créer un compte commercial
            $addC = new Compteagent();
            $addC->Agent = $add->id;
            $addC->save();
        }

        // Vérification par défaut de supérieur hierarchie pour l'inspection.
        // Dans ce cas, nous prenons RG par défaut
        if (VerificationController::ExistanceSupIns()) {

            // Par défaut 
            $add = new Commerciaux();
            $add->nomCom = "Région";
            $add->prenomCom =  "Par défaut";
            $add->telCom = "-";
            $add->sexeCom = "";
            $add->adresseCom = "Cotonou";
            $add->mail = "";
            $add->AIB = "";
            $add->codeEquipe = "0000";
            $add->codeInspection = "0000";
            $add->codeRegion = "9999";
            $add->Niveau = "RG";
            $add->action_save = 's';
            $add->user_action = session("utilisateur")->idUser;
            $add->save();

            $addH = new Hierarchie();
            $addH->codeH = "9999";
            $addH->libelleH =  "";
            $addH->villeH =  "Cotonou";
            $addH->managerH =  $add->id;
            $addH->structureH =  "RG";
            $addH->superieurH =  "9999";
            $addH->user_action = session("utilisateur")->idUser;
            $addH->save();

            // Créer un compte commercial
            $addC = new Compteagent();
            $addC->Agent = $add->id;
            $addC->save();
        }

        return $next($request);
    }
}
