<?php

namespace App\Http\Controllers;

use App\Http\Requests\FournisseurRequest;
use App\Models\Fournisseur;
use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FournisseurController extends Controller
{
  public function index()
    {
        $fournisseurs = Fournisseur::all();
        return response()->json(['data' => $fournisseurs], 200);
    }

    public function show($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return response()->json(['data' => $fournisseur], 200);
    }

    public function store(FournisseurRequest $request)
    {
        // Valider les données de la requête
        $validatedData = $request->validated();

        // Créer une nouvelle instance de Fournisseur
        $fournisseur = new Fournisseur();

        // Remplir les propriétés du fournisseur avec les données validées
        $fournisseur->supplier_name = $validatedData['supplier_name'];
        $fournisseur->address = $validatedData['address'];
        $fournisseur->phone_number = $validatedData['phone_number'];
        $fournisseur->email = $validatedData['email'];
        $fournisseur->website = $validatedData['website'];
        $fournisseur->contact = $validatedData['contact'];
        $fournisseur->notes = $validatedData['notes'];
        $user = Auth::user();     
        $salon= Salon::where('user_id', $user->id_user)->first(); 
        $fournisseur->salon_id = $salon->id_salon;

        // Enregistrer le fournisseur dans la base de données
        $fournisseur->save();

        // Retourner une réponse JSON avec le fournisseur créé et le code de statut HTTP 201 (Created)
        return response()->json(['data' => $fournisseur], 201);
    }


    public function update(FournisseurRequest $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $validatedData = $request->validated();

        $fournisseur->fill($validatedData);
        $fournisseur->save();

        return response()->json(['data' => $fournisseur], 200);
    }

    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();

        return response()->json(null, 204);
    }
}
