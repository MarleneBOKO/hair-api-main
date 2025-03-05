<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::all();
        return response()->json(['data' => $stocks], 200);
    }

    public function show($id)
    {
        $stock = Stock::findOrFail($id);
        return response()->json(['data' => $stock], 200);
    }

    public function store(StockRequest $request)
    {
        // Valider les données de la requête
        $validatedData = $request->validated();
    
        // Créer une nouvelle instance de Stock
        $stock = new Stock();
    
        // Remplir les propriétés du stock avec les données validées
        $stock->product_name = $validatedData['product_name'];
        $stock->quantity = $validatedData['quantity'];
        $stock->reorder_level = $validatedData['reorder_level'];
        $stock->description = $validatedData['description'];
        $stock->addition_date = $validatedData['addition_date'];
        $stock->last_modification_date = $validatedData['last_modification_date'];
        $stock->salon_id = $validatedData['salon_id'];
        $stock->fournisseur_id = $validatedData['fournisseur_id'];
    
        // Enregistrer le stock dans la base de données
        $stock->save();
    
        // Retourner une réponse JSON avec le stock créé et le code de statut HTTP 201 (Created)
        return response()->json(['data' => $stock], 201);
    }
    

    public function update(StockRequest $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $validatedData = $request->validated();

        $stock->fill($validatedData);
        $stock->save();

        return response()->json(['data' => $stock], 200);
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return response()->json(null, 204);
    }
}
