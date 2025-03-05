<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeScheduleNotification extends Notification
{
    protected $newSchedule;

    public function __construct($newSchedule)
    {
        $this->newSchedule = $newSchedule;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Horaires de travail modifié")
            ->line('Vos horaires ont été modifiés. Les nouveaux horaires sont : ' . $this->newSchedule)
            ->line('Merci de votre attention.')
            ->greeting('');
            
    }
}
