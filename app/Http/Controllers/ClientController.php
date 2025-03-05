<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Salon;
use Ramsey\Uuid\Uuid;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Historique_service;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ClientRequest;
use App\Models\Rendez_vou;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{ public function index()
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id_user)->first();

        if (!$client) {
            return response()->json(['message' => 'Client non trouvé'], 404);
        }

        return response()->json(['data' => $client->id_client]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'address' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'first_visit_date' => 'nullable|date',
            'last_visit_date' => 'nullable|date',

        ]);

       $user=Auth::user();
       $client=Client::where('user_id' , $user->id_user)->first();

        // Remplir les propriétés de la statistique avec les données validées provenant de la requête
        $client->address= $request->address;
        $client->phone_number = $request->phone_number;
        $client->birth_date = $request->birth_date;
        $client->gender = $request->gender;
        $client->notes = $request->notes;
        $client->last_visit_date =$request->last_visit_date;
        $client->first_visit_date = $request->first_visit_date;
        $client->save();
        return response()->json(['data' => $client], 201);
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json(['data' => $client]);
    }

    public function update(ClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->validated());
        return response()->json(['data' => $client]);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }

    public function getClientServices()
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id_user)->first(); // Utiliser first() pour obtenir un seul enregistrement
        //dd($client->id_client);


        if ($client) {
            $services = Historique_service::where('client_id', $client->id_client)->get();
                      // dd($services);


            foreach($services as $service)
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

        } else {
            return response()->json('client non trouvé');


        }

        return response()->json(['servicedata' => $datas]);

    }



    public function register_client(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'domain' => 'required|string|exists:salons,subdomain',
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->user_type = "Client";
            $user->save();

            $salon = Salon::where('subdomain', $request->domain)->first();

            if ($salon) {
                $client = new Client();
                $client->name = $request->name;
                $client->email = $request->email;
                $client->user_id = $user->id_user;
                $client->salon_id = $salon->id_salon;
                $client->save();

            } else {
                return response()->json(['message' => 'Salon not found'], 404);
            }

            return response()->json(['message' => 'User registered successfully', 'data' => $user], 201);
        }

        public function getClientrdv()
        {
            $user = Auth::user();
            $client = Client::where('user_id', $user->id_user)->first();
            $count_rdv=Rendez_vou::where('client_id' , $client->id_client)->count();

            $compte = DB::table('virtual_comptes')
            ->where('client_id' , $client->id_client)
            ->first();
            if($compte)
            {
                $somme=$compte->somme;
                return response()->json([
                    'count_rdv' => $count_rdv ,
                    'somme' => $somme
                ]);
            }else{return response()->json([
                'count_rdv' => $count_rdv ,
                'somme' => 0
            ]);}

        }

        public function getRendezVousForClient()
        {
            $user= Auth::user();
            $client=Client::where('user_id' , $user->id_user)->first();
            // Récupérer les rendez-vous du salon
            $rendezVousList = Rendez_vou::with(['client', 'hairstyle', 'employes'])
                ->where('client_id', $client->id_client)
                ->get();

            // Transformer les données pour l'API
            $data = $rendezVousList->map(function($rendezVous) {
                return [
                    'id_appointment' => $rendezVous->id_appointment,
                    'date_and_time' => $rendezVous->date_and_time,
                    'status' => $rendezVous->status,
                    'total_amount' => $rendezVous->total_amount,
                    'payment_method' => $rendezVous->payment_method,
                    'client' => $rendezVous->client ? $rendezVous->client->name : null,
                    'coiffure' => $rendezVous->hairstyle ? $rendezVous->hairstyle->name : null,
                    'employes' => $rendezVous->employes->pluck('name')->toArray(),
                ];
            });

            return response()->json($data);
        }
        public function getClientId()
        {
            $user = Auth::user();
            $client = Client::where('user_id', $user->id_user)->first();
    
            if (!$client) {
                return response()->json(['message' => 'Client non trouvé'], 404);
            }
    
            return response()->json(['data' => $client]);
        }



}
