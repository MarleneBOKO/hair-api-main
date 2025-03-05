<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Ramsey\Uuid\Uuid;
use App\Models\Accessoire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Type_coiffure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AccessoireRequest;
use Illuminate\Support\Facades\Validator;

class AccessoireController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id_user;
        $salon = Salon::where('user_id', $user)->first();

        if (!$salon) {
            return response()->json(['error' => 'Salon non trouvé'], 404);
        }

        $accessoires = Accessoire::where('salon_id', $salon->id_salon)->get();

        return response()->json(['accessoires' => $accessoires], 200);
    }

    public function show($id)
    {
        $accessoire = Accessoire::findOrFail($id);
        return response()->json(['data' => $accessoire], 200);
    }

    public function store(AccessoireRequest $request)
    {
        $accessoire = new Accessoire();
        $accessoire->name = $request->name;
        $accessoire->description = $request->description;
        $accessoire->price = $request->price;
        $user = Auth::user();
        $salon= Salon::where('user_id', $user->id_user)->first();
        $accessoire->salon_id= $salon->id_salon;

        $accessoire->save();

        return response()->json(['data' => $accessoire], 201);
    }

    public function update(Request $request, $id_accessory)
        {
        // Définir les règles de validation
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ];

        // Valider les données du formulaire
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Trouver l'accessoire à mettre à jour
        $accessoire = Accessoire::findOrFail($id_accessory);

        // Mettre à jour les informations de l'accessoire
        $accessoire->name = $request->name;
        $accessoire->price = $request->price;
        $accessoire->description = $request->description;


        $accessoire->save();

        return response()->json(['message' => 'Accessoire mis à jour avec succès', 'data' => $accessoire], 200);
        }


    public function destroy($id)
    {
        $accessoire = Accessoire::findOrFail($id);
        $accessoire->delete();

        return response()->json(null, 204);
    }

    public function accessoirehairstyle(Request $request)
    {
        $request->validate([
            'hairstyle_type_id' => 'required|exists:type_coiffures,id_hairstyle_type',
            'accessory_id' => 'required|exists:accessoires,id_accessory',
            'nb_accessory' => 'required|integer'
        ]);

        //dd($request);
        $user = Auth::user();
        $salon = Salon::where('user_id', $user->id_user)->first();

        // Vérifier si l'association existe déjà
        $existingAssociation = DB::table('accessoire_type_coiffures')
            ->where('hairstyle_type_id', $request->hairstyle_type_id)
            ->where('accessory_id', $request->accessory_id)
            ->where('salon_id', $salon->id_salon)
            ->first();

        if ($existingAssociation) {
            return response()->json(['message' => 'This accessory is already associated with this hairstyle type in this salon'], 409);
        }

        $data = $request->only(['hairstyle_type_id', 'accessory_id', 'nb_accessory']);
        $data['uuid'] = Str::uuid()->toString();
        $data['salon_id'] = $salon->id_salon;
{
    $request->validate([
        'hairstyle_type_id' => 'required|exists:type_coiffures,id_hairstyle_type',
        'accessory_id' => 'required|exists:accessoires,id_accessory',
        'nb_accessory' => 'required|integer'
    ]);

    //dd($request);
    $user = Auth::user();
    $salon = Salon::where('user_id', $user->id_user)->first();

    // Vérifier si l'association existe déjà
    $existingAssociation = DB::table('accessoire_type_coiffures')
        ->where('hairstyle_type_id', $request->hairstyle_type_id)
        ->where('accessory_id', $request->accessory_id)
        ->where('salon_id', $salon->id_salon)
        ->first();

    if ($existingAssociation) {
        return response()->json(['message' => 'This accessory is already associated with this hairstyle type in this salon'], 409);
    }

    $data = $request->only(['hairstyle_type_id', 'accessory_id', 'nb_accessory']);
    $data['uuid'] = Str::uuid()->toString();
    $data['salon_id'] = $salon->id_salon;

      DB::table('accessoire_type_coiffures')->insert($data);


    return response()->json(['message' => 'Create successfully',  'data'  => $data], 201);
}
    }

    public function getaccessory($hairstyle_type_id , $salon_id)
    {

        $accessoires = DB::table('accessoire_type_coiffures')
        ->where('hairstyle_type_id', $hairstyle_type_id)
        ->where('salon_id' , $salon_id)
        ->get();

        $data = [];

        foreach ($accessoires as $accessoire) {
            $accessory = Accessoire::where('id_accessory', $accessoire->accessory_id)->first();
            if ($accessory) {
                $data[] = $accessory;
            }
        }

        return response()->json(['data' => $data], 200);

    }


    public function salonhairstyleandaccesssory()
    {
        $user = Auth::user()->id_user;
        $salon = Salon::where('user_id', $user)->first();

        if (!$salon) {
            return response()->json(['error' => 'Salon non trouvé'], 404);
        }

        $accessoires = Accessoire::where('salon_id', $salon->id_salon)->get();
        $coiffures = Type_coiffure::where('salon_id', $salon->id_salon)->get();

        return response()->json(['accessoires' => $accessoires , 'coiffures' => $coiffures], 200);
    }

}
