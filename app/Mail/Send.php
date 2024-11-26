<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class Send extends Mailable
{
    use Queueable, SerializesModels;
 
    public $data;
    public $subject;
    public $view;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject, $view)
    {
        //
        $this->data = $data;
        $this->subject = $subject;
        $this->view = $view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dat = json_encode($this->data);
        return $this->subject($this->subject)
        	    ->view($this->view, compact("dat"))
        	    ->from("sendfees@nsiaviebenin.com", "NSIA VIE ASSURANCES")
        	    ->replyTo("nviesoft@gmail.com");
    }
}
