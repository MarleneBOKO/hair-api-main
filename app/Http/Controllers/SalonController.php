<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Salon;
use App\Models\Client;
use App\Models\Employe;
use App\Models\Coiffure;
use App\Models\Rendez_vou;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Controller\Exception;
use App\Mail\SalonInvitation;
use App\Models\Type_coiffure;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SalonRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SalonController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        $salon=Salon::where('user_id' , $user->id_user)->first();
        $clients = Client::where('salon_id', $salon->id_salon)->get();
        return response()->json(['data' => $clients]);
    }

    public function getSalonId()
    {
        $user = Auth::user();
        $salon = Salon::where('user_id', $user->id_user)->first();

        if (!$salon) {
            return response()->json(['message' => 'Salon non trouvé'], 404);
        }

        return response()->json(['data' => $salon]);
    }

    public function store(SalonRequest $request)
    {
        //dd($request->all());
        $validatedData = $request->validated();
       // dd($validatedData);
        $path = null;

        if ($request->hasFile('image')) {
            $filename = time().'.'.$request->image->extension();
            $path = $request->file('image')->storeAs(
                'coiffure_store',
                $filename,
                'public'
            );
            $validatedData['image'] = $path;
        }
        $baseSubdomain = Str::slug($validatedData['salon_name']);
        $subdomain = $baseSubdomain;
        $counter = 1;

        while (Salon::where('subdomain', $subdomain)->exists()) {
            $subdomain = $baseSubdomain . '-' . $counter++;
        }

        $salon = new Salon();
        $salon->salon_name = $validatedData['salon_name'];
        $salon->address = $validatedData['address'];
        $salon->phone_number = $validatedData['phone_number'];
        $salon->website = $validatedData['website'];
  //dd($salon->website);
        $salon->email = $validatedData['email'];
        $salon->description = $validatedData['description'];
        $salon->creation_date = $validatedData['creation_date'];
        $salon->last_update_date = $validatedData['last_update_date'];
        $salon->image=$path;
        $salon->longitude = $validatedData['longitude'];
        $salon->latitude = $validatedData['latitude'];
        $salon->subdomain = $subdomain;
        $salon->percent = $request->percent;
        $salon->heure_debut = $request->heure_debut;
        $salon->heure_fin = $request->heure_fin;
        $salon->percent_cancel = $request->percent_cancel;


        /*if (isset($validatedData['longitude']) && isset($validatedData['latitude'])) {
            $salon->longitude = $validatedData['longitude'];
            $salon->latitude = $validatedData['latitude'];
        } */
      // dd($salon);
        $salon->user_id = Auth::user()->id_user;
        $salon->save();
        return response()->json(["data" => $salon], 201);
    }

    public function show($id)
    {
        $salon = Salon::findOrFail($id);
        return response()->json(["data" => $salon]);
    }

    public function update(Request $request, $id)
    {
                //dd($request->all());

        $salon = Salon::findOrFail($id);
        $salonId = $id;

        $rules = [
            'salon_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:salons,email,' . $salonId . ',id_salon', // Ignorer l'email actuel
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website' => 'nullable|string|url',
            'creation_date' => 'nullable|date|before_or_equal:today',
            'last_update_date' => 'nullable|date|after_or_equal:creation_date',
            'longitude' =>'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'percent' => 'required|numeric',
            'percent_cancel' => 'required|numeric',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i'

        ];

        // Valider la requête
        $validatedData = $request->validate($rules);

        // Mettre à jour les champs
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $path = $request->file('image')->storeAs(
                'coiffure_store',
                $filename,
                'public'
            );
            $validatedData['image'] = $path;
        }

        if ($request->has('password')) {
            $validatedData['password'] = Hash::make($request->password);
        }
        //dd($validatedData);
        $salon->update($validatedData);

        return response()->json(["data" => $salon]);

    }

    public function destroy($id)
    {
        $salon = Salon::findOrFail($id);
        $salon->delete();
        return response()->json(null, 204);
    }

    public function getAllsalons()
    {
        $salons = Salon::select('id_salon', 'salon_name', 'address', 'image')->get();

        return response()->json(['data' => $salons], 200);
    }

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user=Auth::user();
        $salon=Salon::where('user_id' , $user->id_user)->first();

        if (!$salon) {
            return response()->json(['message' => 'Salon not found'], 404);
        }

        $link = "http://localhost:5173/register_clients?subdomain=$salon->subdomain";

        Mail::to($request->email)->send(new SalonInvitation($link));

        return response()->json(['message' => 'Invitation sent successfully']);
    }

    public function ActiveSalonInfo(Request $request)
    {
        $rules = [
            'photo1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type_photo' => 'required|string',
            'ifu' => 'nullable|string|size:13|unique:salons',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $salon = Salon::where('user_id', $user->id_user)->first();

        if (!$salon) {
            return response()->json(['error' => 'Salon not found'], 404);
        }

        $path1 = null;
        $path2 = null;

        if ($request->hasFile('photo1')) {
            $filename1 = time() . '_photo1.' . $request->photo1->extension();
            $path1 = $request->file('photo1')->storeAs(
                'salon_photos',
                $filename1,
                'public'
            );
        }

        if ($request->hasFile('photo2')) {
            $filename2 = time() . '_photo2.' . $request->photo2->extension();
            $path2 = $request->file('photo2')->storeAs(
                'salon_photos',
                $filename2,
                'public'
            );
        }

        $salon->ifu = $request->ifu;
        $salon->photo1 = $path1;
        $salon->photo2 = $path2;
        $salon->type_photo = $request->type_photo;
        $salon->save();

        return response()->json(['message' => 'Salon registered successfully', 'salon' => $salon], 201);
    }


        public function ActiveSalon($id)
        {
             $salon=Salon::where('id_salon' , $id)->first();
             $salon->status="Actif";
             $salon->save();
             return response()->json(['message' => 'Salon Activate successfully',  'salon' => $salon], 201);

        }
        public function DesActiveSalon($id)
        {
             $salon=Salon::where('id_salon' , $id)->first();
             $salon->status="Non Actif";
             $salon->save();
             return response()->json(['message' => 'Salon Activate successfully',  'salon' => $salon], 201);

        }

        public function VerifyActif($id)
        {
            $salon = Salon::where('user_id', $id)->first();

            if ($salon && $salon->status === "Actif") {
                return response()->json(['data' => 'Actif'], 200);
            } else {
                return response()->json(['data' => 'Non Actif'], 403);
            }

        }

        public function getInfoSalon()
        {
            $user = Auth::user();
            $salon = Salon::where('user_id', $user->id_user)->first();

            if (!$salon) {
                return response()->json(['message' => 'Salon not found'], 404);
            }

            $salonId = $salon->id_salon;

            // Nombre de rendez-vous
            $countRendezVous = Rendez_vou::where('salon_id', $salonId)->count();

            // Nombre d'employés
            $countEmployes = Employe::where('salon_id', $salonId)->count();

            // Nombre de coiffures
            $countCoiffures = Type_coiffure::where('salon_id', $salonId)->count();

            // Nombre de clients
            $countClients = Client::where('salon_id', $salonId)->count();

            return response()->json([
                'count_rendezvous' => $countRendezVous,
                'count_employes' => $countEmployes,
                'count_coiffures' => $countCoiffures,
                'count_clients' => $countClients,
            ]);
        }

        public function getInfoSalons()
        {

           $countsalons=Salon::all()->count();
           $countCoiffures=Coiffure::all()->count();
           $salonsactifs=Salon::where('status' , "Actif")->count();
           $salonsnonactifs=Salon::where('status' , "Non Actif")->count();


            return response()->json([
                'count_salons' => $countsalons,
                'salonsactifs' => $salonsactifs,
                'count_coiffures' => $countCoiffures,
                'salonsnonactifs' => $salonsnonactifs,
            ]);
        }

        public function detailsalon()
        {
            try {
                $salons = Salon::select('id_salon', 'salon_name', 'address', 'status')
                    ->get();

                $salons = $salons->map(function ($salon) {
                    // Compter le nombre d'employés
                    $numEmployees = Employe::where('salon_id', $salon->id_salon)->count();

                    // Compter le nombre de coiffures (type_coiffures)
                    $numHairstyles = Type_coiffure::where('salon_id', $salon->id_salon)->count();

                    // Compter le nombre de clients (client_salons)
                    $numClients = DB::table('clients')
                        ->where('salon_id', $salon->id_salon)
                        ->count();

                    // Compter le nombre de réservations (rendez_vous)
                    $numReservations = DB::table('rendez_vous')
                        ->where('salon_id', $salon->id_salon)
                        ->count();

                    return [
                        'id_salon'=>$salon->id_salon,
                        'name' => $salon->salon_name,
                        'address' => $salon->address,
                        'num_employees' => $numEmployees,
                        'num_hairstyles' => $numHairstyles,
                        'num_clients' => $numClients,
                        'num_reservations' => $numReservations,
                        'status' => $salon->status,
                    ];
                });

        return response()->json($salons);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur lors de la récupération des détails des salons'], 500);
    }
}

public function getsalondata($id)
{
    $salon=Salon::where('id_salon' , $id)->first();
    return response()->json(['data' => $salon]);
}

private function durationToMinutes($duration) {
    $parts = explode(':', $duration);
    return ($parts[0] * 60) + $parts[1] + ($parts[2] / 60);
}

public function getHours(Type_coiffure $hairstyle, $date)
{
    $user = Auth::user();
    $client = Client::where('user_id', $user->id_user)->firstOrFail();
    $salon = Salon::where('id_salon', $client->salon_id)->first();
    $heure_debut = $salon->heure_debut;
    $heure_fin = $salon->heure_fin;

    $datesalon = Carbon::parse("{$date} {$heure_debut}");
    $enddatesalon = Carbon::parse("{$date} {$heure_fin}");

    $employes = DB::table('employe_type_coiffures')
        ->where('hairstyle_type_id', $hairstyle->id_hairstyle_type)
        ->pluck('employe_id')
        ->toArray();

    $rendezVousEmploye = DB::table('employe_rendez_vous')
        ->whereIn('employe_id', $employes)
        ->leftJoin('rendez_vous', 'rendez_vous.id_appointment', '=', 'employe_rendez_vous.appointment_id')
        ->whereDate('rendez_vous.date_and_time', $datesalon->format('Y-m-d'))
        ->pluck('employe_id')
        ->toArray();

    $intersection = array_intersect($employes, $rendezVousEmploye);

    // Si l'intersection est vide, utiliser tous les employés
    if (empty($intersection)) {
        $intersection = $employes;
    }

    $rendezVous = Rendez_vou::whereDate('date_and_time', $datesalon->format('Y-m-d'))->get();

    $data = [];
    $datecurrent = $datesalon;

    while ($datecurrent->isBefore($enddatesalon)) {
        $endcurrent = $datecurrent->copy()->addMinutes(30);
        $isAvailable = true;

        foreach ($rendezVous as $rdv) {
            $rdvStart = Carbon::parse($rdv->date_and_time);
            $rdvDurationMinutes = $this->durationToMinutes($rdv->duration);
            $rdvEnd = $rdvStart->copy()->addMinutes($rdvDurationMinutes);

            if ($datecurrent->lt($rdvEnd) && $endcurrent->gt($rdvStart)) {
                if (count($intersection) > $hairstyle->nb_employe) {
                    $isAvailable = false;
                } else {
                    $isAvailable = false;
                }
                break;
            }
        }

        $data[] = [
            'time' => $datecurrent->format('H:i'),
            'is_available' => $isAvailable,
        ];

        $datecurrent->addMinutes(30);
    }

    return response()->json(["data" => $data, "employes" => $intersection]);
}



}
