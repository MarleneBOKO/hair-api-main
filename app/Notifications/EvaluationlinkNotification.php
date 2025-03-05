<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvaluationlinkNotification extends Notification
{
    use Queueable;
    protected $link;
    protected $client_name;
    protected $date_rdv;
    protected $hairstyle_name;
    protected $salon_name;





    /**
     * Create a new notification instance.
     */
    public function __construct($link,$client_name,$date_rdv,$hairstyle_name,$salon_name)
    {
        $this->link = $link;
        $this->client_name= $client_name;
        $this->date_rdv = $date_rdv;
        $this->hairstyle_name = $hairstyle_name;
        $this->salon_name = $salon_name;




    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                     ->greeting("Bonjour $this->client_name")
                    ->subject("Evaluation")
                    ->line("Nous voudrions que vous examiniez le salon  : $this->salon_name que vous avez visité le $this->date_rdv pour la coiffure $this->hairstyle_name")
                    ->action('Click here', url($this->link))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
