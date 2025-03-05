<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenteRequest;
use App\Models\Client;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::all();
        return response()->json(['data' => $ventes], 200);
    }

    public function show($id)
    {
        $vente = Vente::findOrFail($id);
        return response()->json(['data' => $vente], 200);
    }

    public function store(VenteRequest $request)
    {
        // Valider les données de la requête
        $validatedData = $request->validated();

        // Créer une nouvelle instance de vente
        $vente = new Vente();

        // Remplir les propriétés de la vente avec les données validées provenant de la requête
        $vente->notes = $validatedData['notes'];
        $vente->date = $validatedData['date'];
        $vente->total_amount = $validatedData['total_amount'];
        $vente->payment_method = $validatedData['payment_method'];
        $vente->hairstyle_type_id = $validatedData['hairstyle_type_id'];
        $vente->salon_id = $validatedData['salon_id'];
        $vente->user_id = Auth::user()->id_user;
        $client=Client::where('user_id' , $vente->user_id )->first();
        $vente->client_id = $client->id_client;

        // Enregistrer la vente dans la base de données
        $vente->save();

        // Retourner une réponse JSON avec la vente créée et le code de statut HTTP 201 (Created)
        return response()->json(['data' => $vente], 201);
    }

    public function update(VenteRequest $request, $id)
    {
        $vente = Vente::findOrFail($id);

        $validatedData = $request->validated();

        $vente->update($validatedData);

        return response()->json(['data' => $vente], 200);
    }

    public function destroy($id)
    {
        $vente = Vente::findOrFail($id);
        $vente->delete();

        return response()->json(null, 204);
}
}
