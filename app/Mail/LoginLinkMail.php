<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $loginLink;

    /**
     * Create a new message instance.
     *
     * @param string $loginLink
     * @return void
     */
    public function __construct($loginLink)
    {
        $this->loginLink = $loginLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.login_link_plain')
                    ->with(['loginLink' => $this->loginLink]);
    }
}

