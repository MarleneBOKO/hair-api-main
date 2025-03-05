<?php

namespace App\Http\Controllers;

use App\Http\Requests\Historique_serviceRequest;
use App\Models\Client;
use App\Models\Historique_service;
use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoriqueServiceController extends Controller
{
        public function index()
        {
            $historiqueServices = Historique_service::all();
            return response()->json(['data' => $historiqueServices], 200);
        }

        public function show($id)
        {
            $historiqueService = Historique_service::findOrFail($id);
            return response()->json(['data' => $historiqueService], 200);
        }

        public function store(Historique_serviceRequest $request)
        {
           /* // Valider les données de la requête
            $validatedData = $request->validated();
        
            // Créer une nouvelle instance d'historique de service
            $historiqueService = new Historique_service();
        
            // Remplir les propriétés de l'historique de service avec les données validées provenant de la requête
            $historiqueService->notes = $validatedData['notes'];
            $historiqueService->date = $validatedData['date'];
            $historiqueService->amount_paid = $validatedData['amount_paid'];
            $historiqueService->hairstyle_type_id = $validatedData['hairstyle_type_id'];
            $historiqueService->employe_id = $validatedData['employe_id'];
            $historiqueService->salon_id = $validatedData['salon_id'];
            $historiqueService->heure_debut = $validatedData['heure_debut'];
            $historiqueService->heure_fin = $validatedData['heure_fin'];
            $user=Auth::user();
            $client=Client::where('user_id' , $user->id_user)->first();
            $historiqueService->client_id = $client->id_client;



        
            // Enregistrer l'historique de service dans la base de données
            $historiqueService->save();

            // Enregistrer les performances de l'employé
            $performance = new Performance();
            $performance->date = now()->toDateString();
            $performance->clients_served = 1; // Un client a été servi pour ce service
            $performance->revenue_generated = $validatedData['amount_paid']; // Le revenu généré par ce service
            $performance->employe_id = $validatedData['employe_id'];
            $performance->service_history_id = $historiqueService->id_service_history;

            $performance->save();
        
            // Retourner une réponse JSON avec l'historique de service créé et le code de statut HTTP 201 (Created)
            return response()->json(['data' => $historiqueService], 201);
        }

        public function update(Historique_serviceRequest $request, $id)
        {
            $historiqueService = Historique_service::findOrFail($id);

            $validatedData = $request->validated();

            $historiqueService->fill($validatedData);
            $historiqueService->save();

            return response()->json(['data' => $historiqueService], 200);
        }

        public function destroy($id)
        {
            $historiqueService = Historique_service::findOrFail($id);
            $historiqueService->delete();

            return response()->json(null, 204);*/
        }

        public function getServices()
        {
            $clients = Client::all();
            $datas = [];
    
            foreach ($clients as $client)
            {
                $servicesHistory = Historique_service::where('client_id' , $client->id_client)->get(); 
    
                foreach ($servicesHistory as $service)
                {
                    $datas[] = [
                        'client' => $client->name, 
                        'nom_coiffure' => $service->hairstyle_name, 
                        'montant' => $service->amount_paid,
                        'date' => $service->date, 
                        //'heure_debut' => $service->heure_debut, 
                        //'heure_fin' => $service->heure_fin, 
    
    
     
                    ];
                }
            }
            
            return response()->json(['servicedata' => $datas]);
        }
}
