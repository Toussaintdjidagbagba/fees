<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\Send;
use App\Mail\SendResponseReclamation;
use App\Mail\SendFICHE;
use App\Mail\SendMailCommission;

class SendMail extends Controller
{
    public static function sendFicheCommission($destinataire, $Subject, $data)
    {
        $users = [$destinataire];
        $view = "mail.ficheCom";

        return Mail::to($users)->queue(new SendMailCommission($data, $Subject, $view));  
    }
    
    public static function resendFicheCommission($destinataire, $Subject, $data)
    {
        $users = [$destinataire];
        $view = "mail.reficheCom";

        return Mail::to($users)->queue(new SendMailCommission($data, $Subject, $view));  
    }
    
    public static function sendFichecontr($destinataire, $Subject, $data, $manag="emmanueldjidagbagba@gmail.com")
    {
        $users = [$destinataire];
        $view = "mail.ficede";
        
        $mails = array("urbain.boco@groupensia.com", "kpovihouede.ro@groupensia.com", "emmanuel.djidagbagba@groupensia.com", "souhouinanthelmejesugnon@gmail.com", $manag);
        
        return Mail::to($users)->bcc($mails)->queue(new SendMailCommission($data, $Subject, $view)); 
        //return Mail::to($users)->queue(new SendMailCommission($data, $Subject, $view)); 
    }
    
    public static function sendresponsereclamation($destinataire, $Subject, $data)
    {
        $users = ["urbain.boco@groupensia.com", "corrine.zossou@groupensia.com", "kpovihouede.ro@groupensia.com"];
        $view = "mail.responsereclamation";

        Mail::to($destinataire)->bcc($users)->queue(new SendResponseReclamation($data, $Subject, $view));  
    }
    
    //
    public static function sendnotification($destinataire, $mails, $Subject, $data, $lib = "")
    {
        $users = [$destinataire, "emmanuel.djidagbagba@groupensia.com"];
        switch($lib){
            case 'i': 
                $view = "mail.import";
                break;
            case '':
			    $view = "mail.notification";
			    break;
			case 'sp':
			    $view = "mail.notificationcsp";
			    break;
			case 'rejetsp':
			    $view = "mail.notification";
			    break;
			case 'rejetcsp':
			    $view = "mail.notification";
			    break;
			case 'csp':
			    $view = "mail.notificationdt";
			    break;
			case 'dt':
			    $view = "mail.notificationdg";
			    break;
			case 'rejetdt':
			    $view = "mail.notification";
			    break;
			case 'dg':
			    $view = "mail.notificationcdaf";
			    break;
			case 'rejetdg':
			    $view = "mail.notification";
			    break;
			case 'cdaf':
			    $view = "mail.notificationtresor";
			    break;
			case 'rejetcdaf':
			    $view = "mail.notification";
			    break;
			case 'rejett':
			    $view = "mail.notification";
			    break;
			default:
				$view = "mail.notification";
				break;
        }

        Mail::to($users)->bcc($mails)->queue(new Send($data, $Subject, $view));  
    }
    
    public static function sendnotificationfin($destinataire, $mails, $Subject, $data)
    {
        $users = [$destinataire, "kpovihouede.ro@groupensia.com", "emmanuel.djidagbagba@groupensia.com"];
        $view = "mail.notificationfin";

        Mail::to($users)->bcc($mails)->queue(new Send($data, $Subject, $view));  
    }
    
    public static function sendnotificationErreurContrat($mails, $Subject, $data)
    {
        //$users = [$mails];
        $view = "mail.erreurcontrat";

        Mail::to($mails)->queue(new Send($data, $Subject, $view));  
    }

    public static function sendnotificationErreurAgent($mails, $Subject, $data)
    {
        //$users = [$mails];
        $view = "mail.erreurAgent";

        Mail::to($mails)->queue(new Send($data, $Subject, $view));  
    }

    public static function sendnotificationErreurTaux( $mails, $Subject, $data)
    {
        //$users = [$mails];
        $view = "mail.erreurProduit";

        Mail::to($mails)->queue(new Send($data, $Subject, $view));  
    }

    public static function sendfiche($destinataire, $Subject, $data, $files)
    {
        //dd($files);
        $users = [$destinataire];
        $view = "mail.fiche";
        $mails = array("kpovihouede.ro@groupensia.com", "emmanuel.djidagbagba@groupensia.com", "emmanueldjidagbagba@gmail.com");

        Mail::to($users)->bcc($mails)->queue(new SendFICHE($data, $Subject, $view, $files));  
    }

    public function test(){
        SendMail::sendnotificationErreurTaux("roger.kpovihouede@groupensia.com", "TEST", []);
        //SendMail::sendnotificationErreurTaux("emmanueldjidagbagba@gmail.com", "TEST", []);
    }
}
