<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendMailCommission extends Mailable
{
    use Queueable, SerializesModels;
 
    public $data;
    public $subject;
    public $view;
   
    public function __construct($data, $subject, $view)
    {

        //
        $this->data = $data;
        $this->subject = $subject;
        $this->view = $view;
    }

    public function build()
    {
        return $this->subject($this->subject)
        	    ->view($this->view, $this->data)
        	    ->from("sendfees@nsiaviebenin.com", "NSIA VIE ASSURANCES")
        	    ->replyTo("nviesoft@gmail.com");
    }
}
