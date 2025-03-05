<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatistiqueRequest;
use App\Models\Statistique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatistiqueController extends Controller
{
    public function index()
    {
        $statistics = Statistique::all();
        return response()->json(['data' => $statistics], 200);
    }

    public function show($id)
    {
        $statistic = Statistique::findOrFail($id);
        return response()->json(['data' => $statistic], 200);
    }

    public function store(StatistiqueRequest $request)
    {
        // Valider les données de la requête
        $validatedData = $request->validated();

        // Créer une nouvelle instance de statistique
        $statistic = new Statistique();

        // Remplir les propriétés de la statistique avec les données validées provenant de la requête
        $statistic->type = $validatedData['type'];
        $statistic->value = $validatedData['value'];
        $statistic->date = $validatedData['date'];
        $statistic->salon_id = $validatedData['salon_id'];
        $statistic->user_id = Auth::user()->id_user;

        // Enregistrer la statistique dans la base de données
        $statistic->save();

        // Retourner une réponse JSON avec la statistique créée et le code de statut HTTP 201 (Created)
        return response()->json(['data' => $statistic], 201);
    }
    public function update(StatistiqueRequest $request, $id)
    {
        $statistic = Statistique::findOrFail($id);

        $validatedData = $request->validated();

        $statistic->update($validatedData);

        return response()->json(['data' => $statistic], 200);
    }

    public function destroy($id)
    {
        $statistic = Statistique::findOrFail($id);
        $statistic->delete();

        return response()->json(null, 204);
    }
}
