<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalonInvitation extends Mailable
{


    use Queueable, SerializesModels;

    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Invitation to Join Salon')
                    ->html("
                        <html>
                        <head>
                            <title>Invitation to Join Salon</title>
                        </head>
                        <body>
                            <p>You have been invited to join our salon. Please click the link below to register:</p>
                            <a href=\"{$this->link}\">{$this->link}</a>
                        </body>
                        </html>
                    ");
    }
}
