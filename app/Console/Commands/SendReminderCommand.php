<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Employe;
use App\Models\Rendez_vou;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         // Récupérer les rendez-vous à notifier
         $appointments = Rendez_vou::where('date_and_time', '>=', now())
         ->where('status', 'confirmed') // Suppose que les rendez-vous confirmés doivent recevoir des rappels
         ->get();

        foreach ($appointments as $appointment) {
            // Obtenir les informations du client associé au rendez-vous
            $client = Client::find($appointment->client_id);
           $employe = Employe::find($appointment->employe_id);

            if ($client) {
                
                Mail::raw("Rappel de rendez-vous pris pour : $appointment->date_and_time", function ($message) use ($client) {
                    $message->to($client->email)
                            ->subject('Rappel de rdv');
                });
                     
            }

           


     //return response()->json(['message' => 'Reminders sent successfully']);
    }
}

}
