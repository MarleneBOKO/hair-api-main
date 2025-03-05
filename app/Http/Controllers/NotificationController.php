<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Rendez_vou;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::all();
        return response()->json(['data' => $notifications], 200);
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        return response()->json(['data' => $notification], 200);
    }

    public function store(NotificationRequest $request)
{
    // Valider les données de la requête
    $validatedData = $request->validated();

    // Créer une nouvelle instance de notification
    $notification = new Notification();

    // Remplir les propriétés de la notification avec les données validées provenant de la requête
    $notification->type = $validatedData['type'];
    $notification->content = $validatedData['content'];
    $notification->date = $validatedData['date'];
    $notification->salon_id = $validatedData['salon_id'];
    $notification->user_id = Auth::user()->id_user;

    // Enregistrer la notification dans la base de données
    $notification->save();

    // Retourner une réponse JSON avec la notification créée et le code de statut HTTP 201 (Created)
    return response()->json(['data' => $notification], 201);
}

    public function update(NotificationRequest $request, $id)
    {
        $notification = Notification::findOrFail($id);

        $validatedData = $request->validated();

        $notification->update($validatedData);

        return response()->json(['data' => $notification], 200);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(null, 204);
    }


    public function sendReminders()
    {
        // Récupérer les rendez-vous à notifier
        $appointments = Rendez_vou::where('date_and_time', '>=', now())
            ->where('status', 'confirmed') // Suppose que les rendez-vous confirmés doivent recevoir des rappels
            ->get();

        foreach ($appointments as $appointment) {
            // Obtenir les informations du client associé au rendez-vous
            $client = Client::find($appointment->client_id);

            if ($client) {
               
                Mail::raw("Rappel de rendez-vous pris pour : $appointment->date_and_time", function ($message) use ($client) {
                    $message->to($client->email)
                            ->subject('Rappel de rdv');
                });
                

               
                //$appointment->update(['reminder_sent' => true]);
            }
        }

        return response()->json(['message' => 'Reminders sent successfully']);
    }
}
