<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationRequest;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Historique_service;
use App\Models\Rendez_vou;
use App\Models\Salon;
use App\Notifications\EvaluationlinkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::all();
        return response()->json(['data' => $evaluations], 200);
    }

    public function show($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        return response()->json(['data' => $evaluation], 200);
    }

    public function store(EvaluationRequest $request)
    {
        //dd(now());
       //dd($request->all());
        // Valider les données de la requête
        $validatedData = $request->validated();

        // Créer une nouvelle instance d'évaluation
        $evaluation = new Evaluation();

        // Remplir les propriétés de l'évaluation avec les données validées provenant de la requête
        $evaluation->comment = $validatedData['comment'];
        $evaluation->note = $validatedData['note'];
        $evaluation->date =  now();
        $evaluation->service_history_id= $request->id_service_history;
        //dd($evaluation);
        $service=Historique_service::where('id_service_history' ,  $request->id_service_history)->first();
        //dd($service);

        // Enregistrer l'évaluation dans la base de données
        $evaluation->save();

        // Retourner une réponse JSON avec l'évaluation créée et le code de statut HTTP 201 (Created)
        return response()->json(['data' => $evaluation], 201);
    }

    public function update(EvaluationRequest $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);

        $validatedData = $request->validated();

        $evaluation->update($validatedData);

        return response()->json(['data' => $evaluation], 200);
    }

    public function destroy($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->delete();

        return response()->json(null, 204);
    }

       public function sendEvaluationLink()
        {
        $services = Historique_service::where('status', 'terminé')->where('review_send', 'False')->get();

            if ($services->isEmpty()) {
                return response()->json('No services found');
            }

            $noClientFound = true;

            foreach ($services as $service) {
                $rdvId=$service->appointment_id;
                $rdv=Rendez_vou::where('id_appointment' , $rdvId)->first();
                $client = Client::where('id_client' , $rdv->client_id)->first();
                if ($client) {
                    $noClientFound = false;
                    $client_name = $client->name;
                    $date_rdv = $service->date_rdv;
                    $hairstyle_name = $service->hairstyle_name;
                    $salon = Salon::where('id_salon', $service->salon_id)->first();
                    $salon_name = $salon->salon_name;
                    $service_id = $service->id_service_history;
                    $user_id = $client->user_id;

                    $link = "http://localhost:5173/evaluation-form?id_service_history=$service_id&user_id=$user_id";

                    $client->notify(new EvaluationlinkNotification($link, $client_name, $date_rdv, $hairstyle_name, $salon_name));
                    $service->review_send = "True";
                    $service->save();
                }
            }

            if ($noClientFound) {
                return response()->json('No client found');
            } else {
                return response()->json('Evaluation sending');
            }
        }



}
