<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RdvinfoNotification extends Notification
{
    protected $rdvid;

    public function __construct($rdvid)
    {
        $this->rdvid = $rdvid;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $qrCodePath = public_path('qrcodes/rendezvous_' . $this->rdvid . '.png');
        return (new MailMessage)
            ->subject("RDV info")
            ->line('Vous trouverez en pièce jointe les informations de votre rdv')
            ->greeting('')
             ->attach($qrCodePath, [
                    "as" => "Code Qr". ".png",
                    "mime" => "image/png"
                ]);
    }
}
