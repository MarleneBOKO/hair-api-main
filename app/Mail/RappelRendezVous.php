<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RappelRendezVous extends Mailable
{
    use Queueable, SerializesModels;

    public $dateAndTime;

    /**
     * Create a new message instance.
     *
     * @param  string  $dateAndTime
     * @return void
     */
    public function __construct($dateAndTime)
    {
        $this->dateAndTime = $dateAndTime;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dateAndTime = $this->dateAndTime;
        $subject = 'Rappel de rendez-vous';

        return $this->subject($subject)->view('emails.blank')->with('content', "Le rendez-vous est prévu pour : $dateAndTime");

    }
}
