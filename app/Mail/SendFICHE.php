<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendFICHE extends Mailable
{
    use Queueable, SerializesModels;
 
    public $data;
    public $subject;
    public $view;
    public $files;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject, $view, $files)
    {

        //
        $this->data = $data;
        $this->subject = $subject;
        $this->view = $view;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //dd($this->files[0]["path"]);
        return $this->subject($this->subject)
        	    ->view($this->view, $this->data)
        	    ->from("sendfees@nsiaviebenin.com", "NSIA VIE ASSURANCES")
        	    ->replyTo("nviesoft@gmail.com")
                ->attach(public_path().$this->data["pat"], [
                         'as' => 'Com.xlsx',
                         'mime' => 'application/xlsx',
                    ]);
    }
}
