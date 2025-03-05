<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\Client;
use App\Models\Coiffure;
use Illuminate\Http\Request;
use App\Models\Type_coiffure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Type_coiffureRequest;

class TypeCoiffureController extends Controller
{
    public function index()
    {
        $hairstyleTypes = Type_coiffure::all();
        $salonData = [];

        foreach ($hairstyleTypes as $hairstyleType) {
            $salonname = $hairstyleType->salons->salon_name;

            // Vérifier si le salon_id existe déjà dans le tableau salonData
            if (!isset($salonData[$salonname])) {
                $salonData[$salonname] = [
                    'salon_name' => $salonname,
                    'hairstyles' => []
                ];
            }

            // Ajouter les informations de type de coiffure au salon correspondant
            $salonData[$salonname]['hairstyles'][] = [
                'hairstyle_id' => $hairstyleType->id_hairstyle_type,
                'hairstyle_name' => $hairstyleType->name,
                'hairstyle_description' => $hairstyleType->description,
                'duration' =>$hairstyleType->duration,
                'price'=>$hairstyleType->price

            ];
        }

        // Transformer le tableau associatif en tableau indexé pour avoir des données directement sous forme de tableau JSON
        $salonData = array_values($salonData);

        return response()->json(['data' => $salonData]);
    }

    public function salon_coiffures()
    {
        // Récupérer tous les types de coiffure du salon connecté
        $user=Auth::user();
        $salon=Salon::where('user_id' , $user->id_user)->first();
        $hairstyles = Type_coiffure::where('salon_id', $salon->id_salon)->get();
        return response()->json(['data' => $hairstyles]);
    }

    public function store(Type_coiffureRequest $request)
    {

        $hairstyleType = new Type_coiffure();
        $coiffure=Coiffure::where('id_coiffure' , $request->coiffure_id )->first();
        //dd($coiffure);

        $hairstyleType->name = $coiffure->name;
        $hairstyleType->description = $coiffure->description;
        $hairstyleType->category = $coiffure->category;
        $hairstyleType->price= $request->price;
        $hairstyleType->image = $coiffure->image;
        $hairstyleType->image1 = $coiffure->image1;
        $hairstyleType->image2 = $coiffure->image2;
        $hairstyleType->coiffure_id = $request->coiffure_id;
        $hairstyleType->nb_employe=$request->nb_employe;


        $user = Auth::user();

        $salon= Salon::where('user_id', $user->id_user)->first();
        //dd($salon);

        $hairstyleType->salon_id = $salon->id_salon;


        $hairstyleType->save();


        // $hairstyleType = new Type_coiffure();
        // $hairstyleType->name = $request->name;
        // $hairstyleType->description = $request->description;
        // $hairstyleType->duration = $request->duration;
        // $hairstyleType->category = $request->category;
        // $hairstyleType->price= $request->price;
        // $hairstyleType->tout_fourni_price= $request->tout_fourni_price;
        // $hairstyleType->image= $request->image;

        // $user = Auth::user();

        // $salon= Salon::where('user_id', $user->id_user)->first();
        // //dd($salon);

        // $hairstyleType->salon_id = $salon->id_salon;

        // if ($request->hasFile('image')) {
        //     $filename = time().'.'.$request->image->extension();
        //     $path = $request->file('image')->storeAs(
        //         'type_coiffure_store',
        //         $filename,
        //         'public'
        //     );
        //     $hairstyleType->image = $path;
        // }

        // $hairstyleType->save();

        return response()->json(['data' => $hairstyleType], 201);
    }


    public function show($id)
    {
        $hairstyleType = Type_coiffure::findOrFail($id);
        return response()->json(['data' => $hairstyleType]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'nb_employe' => 'required|numeric',
            'price' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $hairstyleType = Type_coiffure::findOrFail($id);
        $hairstyleType->price=$request->input('price');
        $hairstyleType->nb_employe=$request->input('nb_employe');
        $hairstyleType->save();
        return response()->json(['data' => $hairstyleType]);
    }

    public function destroy($id)
    {
        $hairstyleType = Type_coiffure::findOrFail($id);
        $hairstyleType->delete();
        return response()->json("coiffure delete successfuly", 204);
    }



   public function getImage()
    {
        $user=Auth::user()->id_user;
        $client=Client::where('user_id' ,$user)->first();
        //dd($client);
        // Récupérer tous les noms de types de coiffure disponibles
        $coiffureTypes = Type_coiffure::where('salon_id', $client->salon_id)
        ->distinct()
        ->pluck('name');
        //dd( $coiffureTypes);

        $coiffureImagesByType = [];

        foreach ($coiffureTypes as $coiffureType) {
            $imagesForCoiffureType = DB::table('type_coiffures')
                ->join('salons', 'type_coiffures.salon_id', '=', 'salons.id_salon')
                ->where('type_coiffures.name', $coiffureType)
                ->select('type_coiffures.image', 'type_coiffures.image1','type_coiffures.image2','type_coiffures.name', 'type_coiffures.id_hairstyle_type','salons.id_salon')
                ->get();

            $coiffureImagesByType[$coiffureType] = $imagesForCoiffureType;
        }

        return response()->json(['data' => $coiffureImagesByType]);
    }



    public function getSalonsWithSameCoiffure()
    {
          // Récupérer tous les types de coiffure disponibles
          $typesCoiffure = DB::table('type_coiffures')->where('id_hairstyle_type', request()->query('type_coif_id'))->distinct()->pluck('name');


          $salonsWithSameCoiffure = [];

        foreach ($typesCoiffure as $typeCoiffure) {
            // Récupérer les salons qui proposent ce type de coiffure
            $salons = Salon::join('type_coiffures', 'salons.id_salon', '=', 'type_coiffures.salon_id')
                ->where('type_coiffures.name', $typeCoiffure)
                ->select('salons.salon_name', 'salons.address', 'type_coiffures.id_hairstyle_type','type_coiffures.image1','type_coiffures.image2',  'type_coiffures.price', 'type_coiffures.tout_fourni_price', 'type_coiffures.salon_id')
                ->get();

            $salonsWithSameCoiffure[$typeCoiffure] = $salons;
        }

        return response()->json(['data' => $salonsWithSameCoiffure]);
    }
    public function getSalonhairstyle()
    {
        $user=Auth::user();
        $salon=Salon::where('user_id' , $user->id_user)->first();
        $coiffures=[];
        //dd($salon);
        $hairstyles=Type_coiffure::where('salon_id' , $salon->id_salon)->get();
        //dd($hairstyles);


        return response()->json(['data' =>  $hairstyles]);

    }
    public function getSalonshairstyle($id_salon)
    {
        $salon = Salon::with('hairstyles')->find($id_salon);

        if (!$salon) {
            return response()->json(['error' => 'Salon not found'], 404);
        }

        $data = [
            'id_salon'=>$salon->id_salon,
            'name' => $salon->salon_name,
            'description' => $salon->description,
            'main_image' => $salon->image,
            'hairstyles' => $salon->hairstyles->map(function ($hairstyle) use ($salon){
                return [
                    'id_hairstyle_type' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'id_salon'=>$salon->id_salon,
                    'percent'=>$salon->percent,
                    'image' => $hairstyle->image,
                    'image1' => $hairstyle->image1,
                    'image2' => $hairstyle->image2,
                    'price' => $hairstyle->price,
                ];
            })
        ];

        return response()->json($data);
    }

  public function getSalonwomanHairstyles()
    {
        // Récupérer tous les salons avec leurs coiffures de type enfant
        $salons = Salon::with(['hairstyles' => function ($query) {
            $query->where('category', 'Femme');
        }])->get();

        // Transformer les données pour le format souhaité
        $data = $salons->map(function ($salon) {
            $hairstyles = $salon->hairstyles;

            return [
                'salon_name' => $salon->salon_name,
                'coiffures_inferieure_3000' => $hairstyles->where('price', '<=', 3000)->map(function ($hairstyle) {
                    return [
                        'id' => $hairstyle->id_hairstyle_type,
                        'name' => $hairstyle->name,
                        'description' => $hairstyle->description,
                        'price' => $hairstyle->price,
                        'image' => $hairstyle->image
                    ];
                })->values(),
                'coiffures_entre_3000_et_5000' => $hairstyles->whereBetween('price', [3001, 5000])->map(function ($hairstyle) {
                    return [
                        'id' => $hairstyle->id_hairstyle_type,
                        'name' => $hairstyle->name,
                        'description' => $hairstyle->description,
                        'price' => $hairstyle->price,
                        'image' => $hairstyle->image
                    ];
                })->values(),
                'coiffures_superieure_5000' => $hairstyles->where('price', '>', 5000)->map(function ($hairstyle) {
                    return [
                        'id' => $hairstyle->id_hairstyle_type,
                        'name' => $hairstyle->name,
                        'description' => $hairstyle->description,
                        'price' => $hairstyle->price,
                        'image' => $hairstyle->image
                    ];
                })->values(),
            ];
        });

        // Retourner la réponse en JSON
        return response()->json(['data' => $data]);
    }

public function getSalonchildHairstyles()
{
    // Récupérer tous les salons avec leurs coiffures de type enfant
    $salons = Salon::with(['hairstyles' => function ($query) {
        $query->where('category', 'enfant');
    }])->get();

    // Transformer les données pour le format souhaité
    $data = $salons->map(function ($salon) {
        $hairstyles = $salon->hairstyles;

        return [
            'salon_name' => $salon->salon_name,
            'coiffures_inferieure_3000' => $hairstyles->where('price', '<=', 3000)->map(function ($hairstyle) {
                return [
                    'id' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'image' => $hairstyle->image
                ];
            })->values(),
            'coiffures_entre_3000_et_5000' => $hairstyles->whereBetween('price', [3001, 5000])->map(function ($hairstyle) {
                return [
                    'id' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'image' => $hairstyle->image
                ];
            })->values(),
            'coiffures_superieure_5000' => $hairstyles->where('price', '>', 5000)->map(function ($hairstyle) {
                return [
                    'id' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'image' => $hairstyle->image
                ];
            })->values(),
        ];
    });

    // Retourner la réponse en JSON
    return response()->json(['data' => $data]);
}

public function getSalonmanHairstyles()
{
    // Récupérer tous les salons avec leurs coiffures de type enfant
    $salons = Salon::with(['hairstyles' => function ($query) {
        $query->where('category', 'Homme');
    }])->get();

    // Transformer les données pour le format souhaité
    $data = $salons->map(function ($salon) {
        $hairstyles = $salon->hairstyles;

        return [
            'salon_name' => $salon->salon_name,
            'coiffures_inferieure_3000' => $hairstyles->where('price', '<=', 3000)->map(function ($hairstyle) {
                return [
                    'id' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'image' => $hairstyle->image
                ];
            })->values(),
            'coiffures_entre_3000_et_5000' => $hairstyles->whereBetween('price', [3001, 5000])->map(function ($hairstyle) {
                return [
                    'id' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'image' => $hairstyle->image
                ];
            })->values(),
            'coiffures_superieure_5000' => $hairstyles->where('price', '>', 5000)->map(function ($hairstyle) {
                return [
                    'id' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'image' => $hairstyle->image
                ];
            })->values(),
        ];
    });

    // Retourner la réponse en JSON
    return response()->json(['data' => $data]);
}

public function getAllHairstyles($salonId)
{
    try {
        // Récupérer le salon spécifique avec ses coiffures associées
        $salons = Salon::with('hairstyles')
            ->where('id_salon', $salonId)
            ->get();

        // Vérifier si le salon existe
        if ($salons->isEmpty()) {
            return response()->json(['error' => 'Salon not found'], 404);
        }

        // Transformer les données pour le format souhaité
        $data = $salons->map(function ($salon) {
            return [
                'id_salon' => $salon->id_salon,
                'salon_name' => $salon->salon_name,
                'hairstyles' => $salon->hairstyles->map(function ($hairstyle) use ($salon){
                    return [
                        'id_hairstyle_type' => $hairstyle->id_hairstyle_type,
                        'name' => $hairstyle->name,
                        'id_salon' => $salon->id_salon,
                        'percent' => $salon->percent,
                        'description' => $hairstyle->description,
                        'price' => $hairstyle->price,
                        'category' => $hairstyle->category,
                        'image' => $hairstyle->image,
                        'image1' => $hairstyle->image1,
                        'image2' => $hairstyle->image2,

                    ];
                })
            ];
        });

        // Retourner la réponse en JSON
        return response()->json(['data' => $data]);

    } catch (\Exception $e) {
        // Gérer toute exception et retourner une réponse d'erreur appropriée
        return response()->json(['error' => 'Error fetching hairstyles: ' . $e->getMessage()], 500);
    }
}


public function getAllHairstylesAcc()
{
    $salons = Salon::with('hairstyles')->get();

    $data = $salons->map(function ($salon) {
        return [
            'id_salon' => $salon->id_salon,
            'salon_name' => $salon->salon_name,
            'hairstyles' => $salon->hairstyles->map(function ($hairstyle) {
                return [
                    'id_hairstyle_type' => $hairstyle->id_hairstyle_type,
                    'name' => $hairstyle->name,
                    'description' => $hairstyle->description,
                    'price' => $hairstyle->price,
                    'category' => $hairstyle->category,
                    'image' => $hairstyle->image,

                ];
            }),
            'accessories' => $salon->accessories->map(function ($accessory) {
                        return [
                            'id_accessory' => $accessory->id_accessory,
                            'name' => $accessory->name,
                            'quantity' => $accessory->pivot->quantity // Si vous avez une table pivot
                        ];
                    })
        ];
    });

    return response()->json(['data' => $data]);
}

    public function getImagesBySalon($salon_id)
    {
        try {
            // Vérifier si le salon existe
            $salonExists = Salon::where('id_salon', $salon_id)->exists();

            if (!$salonExists) {
                return response()->json(['error' => 'Salon not found'], 404);
            }

            // Récupérer les types de coiffure pour le salon spécifique
            $coiffureTypes = Type_coiffure::where('salon_id', $salon_id)->distinct()->pluck('name');

            $coiffureImagesByType = [];

            foreach ($coiffureTypes as $coiffureType) {
                // Récupérer les images pour ce type de coiffure pour le salon donné
                $imagesForCoiffureType = DB::table('type_coiffures')
                    ->join('salons', 'type_coiffures.salon_id', '=', 'salons.id_salon')
                    ->where('type_coiffures.salon_id', $salon_id)
                    ->where('type_coiffures.name', $coiffureType)
                    ->select('type_coiffures.image', 'type_coiffures.image1', 'type_coiffures.image2', 'type_coiffures.price', 'salons.percent', 'type_coiffures.name', 'type_coiffures.id_hairstyle_type', 'salons.id_salon')
                    ->get();

                $coiffureImagesByType[$coiffureType] = $imagesForCoiffureType;
            }

        return response()->json(['data' => $coiffureImagesByType]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur lors de la récupération des images: ' . $e->getMessage()], 500);
    }
}


}
