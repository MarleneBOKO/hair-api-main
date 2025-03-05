<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Salon;
use App\Models\Client;
use App\Models\Employe;
use BaconQrCode\Writer;
use App\Models\Accessoire;
use App\Models\Rendez_vou;
use App\Models\Performance;
use Illuminate\Http\Request;
use App\Models\Type_coiffure;
use App\Mail\RappelRendezVous;
use App\Models\Historique_service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use BaconQrCode\Renderer\ImageRenderer;
use App\Http\Requests\Rendez_vouRequest;
use App\Models\Transaction;
use Symfony\Component\Mime\Part\TextPart;
use App\Notifications\RdvinfoNotification;
use App\Notifications\EvaluationlinkNotification;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Kkiapay\Kkiapay;

class RendezVouController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id_user)->first();

        if (!$client) {
            return response()->json(['message' => 'Client non trouvé'], 404);
        }

        $rendezVous = Rendez_vou::where('client_id', $client->id_client)
        ->where('status' , 'confirmed')
        ->get();

        $data = [];

        foreach ($rendezVous as $rendezVou) {
            $salon=Salon::where('id_salon' , $rendezVou->salon_id)->first();
            $data[] = [
                'nom_salon' => $salon->salon_name,
                'date_et_heure' => $rendezVou->date_and_time,
                'duree' => $rendezVou->duration,
                'montant' => $rendezVou->total_amount,
                'id_appointment' => $rendezVou->id_appointment,
                'status' =>$rendezVou->status,
            ];
        }

        return response()->json(['data' => $data], 200);
    }

    public function salon_appointments()
    {
        // Récupérer toutes les réservations du salon connecté
        $user=Auth::user();
        $salon=Salon::where('user_id' , $user->id_user)->first();
        $appointments = Rendez_vou::where('salon_id', $salon->id_salon)->get();
        return response()->json(['data' => $appointments]);
    }

    public function show($id)
    {
        $rendezVous = Rendez_vou::findOrFail($id);
        return response()->json(['data' => $rendezVous], 200);
    }

    public function store(Rendez_vouRequest $request)
    {
        // Valider les données de la requête
        $validatedData = $request->validated();

        // Récupération du type de coiffure
        $hairstyle = Type_coiffure::where('id_hairstyle_type', $validatedData['hairstyle_type_id'])->first();
        $nb_employe = $hairstyle->nb_employe;

        // Récupérer les employés disponibles depuis la requête
        $employeVariables = $request->input('employes', []);

        // Vérifier si le nombre d'employés disponibles est supérieur au nombre requis
        if (count($employeVariables) > $nb_employe) {
            // Sélectionner uniquement le nombre d'employés nécessaires
            $employeVariables = array_slice($employeVariables, 0, $nb_employe);
        }

        if (count($employeVariables) > 0) {
            // Calculer la durée moyenne en fonction des employés retenus
            $totalDuration = 0;
            foreach ($employeVariables as $employeId) {
                $employeTypeCoiffure = DB::table('employe_type_coiffures')
                    ->where('employe_id', $employeId)
                    ->where('hairstyle_type_id', $hairstyle->id_hairstyle_type)
                    ->first();

                if ($employeTypeCoiffure) {
                    // Convertir la durée en secondes (depuis le format HH:MM:SS)
                    $durationInSeconds = strtotime($employeTypeCoiffure->duration) - strtotime('TODAY');
                    $totalDuration += $durationInSeconds; // Durée en secondes
                    //dd($totalDuration);
                }
            }

            $averageDuration = $totalDuration / count($employeVariables);
            // Convertir la durée moyenne en format HH:MM:SS
            $hours = floor($averageDuration / 3600);
            $minutes = floor(($averageDuration % 3600) / 60);
            $seconds = $averageDuration % 60;
            $averageDurationFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $usesOwnAccessories = $request->input('usesOwnAccessories', false);

            if (!$usesOwnAccessories) {
                // Récupérer les accessoires sélectionnés
                $selectedAccessories = $request->input('accessories', []);

                // Calculer le coût total des accessoires
                $totalAccessoryCost = 0;
                foreach ($selectedAccessories as $selectedAccessory) {
                    $accessory = Accessoire::find($selectedAccessory['id_accessory']);
                    if ($accessory) {
                        $nb_accessory = max($accessory->nb_accessory, $selectedAccessory['nb_accessory']);
                        $totalAccessoryCost += $accessory->price * $nb_accessory;
                    }
                }

                // Ajouter le coût des accessoires au prix total du rendez-vous
                $totalCost = $validatedData['price'] + $totalAccessoryCost;
            } else {
                // Si le client vient avec ses propres accessoires, le coût total est juste le prix de la coiffure
                $totalCost = $validatedData['price'];
            }

            $user = Auth::user();
            $client = Client::where('user_id', $user->id_user)->first();
            //dd($client);
            $compte = DB::table('virtual_comptes')
                ->where('client_id', $client->id_client)
                ->first();
            if ($compte) {
                if ($compte->somme > 0)
                    $totalCost = $totalCost - $compte->somme;
                DB::table('virtual_comptes')
                    ->where('client_id', $client->id_client)
                    ->update(['somme' => 0]);
            }

            // Créer une nouvelle instance de Rendez_vou
            $rendezVous = new Rendez_vou();
            $rendezVous->notes = $validatedData['notes'];
            $rendezVous->total_amount = $totalCost;
            $rendezVous->date_and_time = $validatedData['date_and_time'];
            $rendezVous->duration = $averageDurationFormatted;
            $rendezVous->hairstyle_type_id = $hairstyle->id_hairstyle_type;
            $rendezVous->client_id = $client->id_client;
            $rendezVous->salon_id = $client->salon_id;
            $salon = Salon::where('id_salon', $client->salon_id)->first();
            $percent = $salon->percent;
            $rendezVous->accompte = ($totalCost * $percent / 100);

            $rendezVous->save();

            // Remplir les propriétés de l'historique de service avec les données validées provenant de la requête
            $historiqueService = new Historique_service();
            $historiqueService->notes = $validatedData['notes'];
            $historiqueService->date_rdv = $validatedData['date_and_time'];
            $historiqueService->amount_paid = $totalCost;
            $historiqueService->hairstyle_name = $hairstyle->name;
            $historiqueService->image = $hairstyle->image;
            $historiqueService->duration = $averageDurationFormatted;
            $historiqueService->salon_id = $client->salon_id;
            $historiqueService->appointment_id = $rendezVous->client_id;
            $historiqueService->client_id = $rendezVous->client_id;
            $historiqueService->date = now()->toDateString();

            $historiqueService->save();

            if (!$usesOwnAccessories)
            {

                $selectedAccessories = $request->input('accessories', []);
                if ($selectedAccessories) {
                    foreach ($selectedAccessories as $selectedAccessory) {
                        DB::table('accessoire_rendez_vous')->insert([
                            'appointment_id' => $rendezVous->id_appointment,
                            'accessory_id' => $selectedAccessory['id_accessory']
                        ]);
                    }
                }

            }

 

            // Assigner les employés au rendez-vous
            foreach ($employeVariables as $index => $employeVariable) {
                DB::table('employe_rendez_vous')->insert([
                    'employe_id' => $employeVariable,
                    'appointment_id' => $rendezVous->id_appointment
                ]);
                DB::table('employe_historique_services')->insert([
                    'employe_id' => $employeVariable,
                    'service_history_id' => $historiqueService->id_service_history
                ]);

                // Enregistrer les performances de l'employé
                $performance = new Performance();
                $performance->date = now()->toDateString();
                $performance->clients_served = 1;
                $performance->revenue_generated = $rendezVous->total_amount;
                $performance->employe_id = $employeVariable;
                $performance->service_history_id = $historiqueService->id_service_history;

                $performance->save();
                $performances[] = $performance;

                // Envoyer un rappel automatique par e-mail au client
                $dateAndTime = $rendezVous->date_and_time;
                $employe = Employe::find($employeVariable);
                if ($employe) {
                    Mail::raw("Hello $employe->name! Vous êtes invité le : $dateAndTime à tresser $hairstyle->name au client $client->name", function ($message) use ($employe) {
                        $message->to($employe->email)
                            ->subject('Rappel de rdv');
                    });
                }
            }

            $salon = Salon::where('id_salon', $rendezVous->salon_id)->first();
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );

            // Création du QR Code
            $writer = new Writer($renderer);
            $qrCodeContent = ("Rendez-vous pris pour le $rendezVous->date_and_time pour la coiffure $hairstyle->name dans le salon : $salon->salon_name");
            $qrCodePath = public_path('qrcodes/rendezvous_' . $rendezVous->id_appointment . '.png');
            $rdvid = $rendezVous->id_appointment;

            try {
                $writer->writeFile($qrCodeContent, $qrCodePath);

                $client->notify(new RdvinfoNotification($rdvid));

            } catch (Exception $e) {
                Log::error('Erreur lors de la génération du QR code ou de l\'envoi de l\'email: ' . $e->getMessage());
                return response()->json(['error' => 'Erreur lors de la génération du QR code ou de l\'envoi de l\'email'], 500);
            }

            return response()->json([
                'message' => 'Rendez-vous créé avec succès',
                'rendezVous' => $rendezVous,
                'historiqueService' => $historiqueService,
                'performances' => $performances
            ], 201);
        } else {
            return response()->json(['error' => 'Aucun employé disponible pour le rendez-vous'], 500);
        }
    }

        /**
     * Convertir la durée au format "HH:MM:SS" en secondes.
     *
     * @param string $duration
     * @return int
     */
    private function convertToSeconds($duration)
    {
        // Séparer les heures, minutes et secondes
        $timeParts = explode(':', $duration);

        // Convertir en secondes
        $seconds = $timeParts[0] * 3600 + $timeParts[1] * 60 + $timeParts[2];

        return $seconds;
    }


    public function update(Rendez_vouRequest $request, $id)
    {


            // Valider les données de la requête
        $request->validate([
            'date_and_time' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        // Récupérer le rendez-vous existant
        $rendezVous = Rendez_vou::findOrFail($id);

        // Récupérer le type de coiffure
        $hairstyle = Type_coiffure::where('id_hairstyle_type', $rendezVous->hairstyle_type_id)->first();
        $nb_employe = $hairstyle->nb_employe;

        // Récupérer les employés assignés à ce rendez-vous
        $employeRendezVous = DB::table('employe_rendez_vous')
            ->where('appointment_id', $rendezVous->id_appointment)
            ->get();

        // Vérifier les conflits de rendez-vous pour chaque employé assigné
        $appointmentConflicts = [];
        $requestedStart = new \DateTime($request->input('date_and_time'));
        $availableEmployees = [];

        foreach ($employeRendezVous as $employeRendezVousEntry) {
            $rendezVousEmploye = DB::table('employe_rendez_vous')
                ->where('employe_id', $employeRendezVousEntry->employe_id)
                ->get();

            $conflict = false;
            foreach ($rendezVousEmploye as $rendezVousEmp) {
                $appointment = Rendez_vou::find($rendezVousEmp->appointment_id);

                if ($appointment->id_appointment != $rendezVous->id_appointment && $appointment->status !== 'terminé' && $appointment->status !== 'annulé') {
                    $start = new \DateTime($appointment->date_and_time);
                    $end = (clone $start)->add(new \DateInterval('PT' . $this->convertToSeconds($appointment->duration) . 'S'));

                    if ($requestedStart < $end && $requestedStart >= $start) {
                        $conflict = true;
                        $appointmentConflicts[] = $rendezVousEmp->appointment_id;
                        break;
                    }
                }
            }

            if (!$conflict) {
                $availableEmployees[] = $employeRendezVousEntry->employe_id;
            }
        }

        // Chercher d'autres employés disponibles si le nombre d'employés disponibles est insuffisant
        if (count($availableEmployees) < $nb_employe) {
            $employes = DB::table('employe_type_coiffures')
                ->where('hairstyle_type_id', $hairstyle->id_hairstyle_type)
                ->get();

            foreach ($employes as $employe) {
                if (!in_array($employe->employe_id, $availableEmployees)) {
                    $rendezVousEmploye = DB::table('employe_rendez_vous')
                        ->where('employe_id', $employe->employe_id)
                        ->get();

                    $conflict = false;
                    foreach ($rendezVousEmploye as $rendezVousEmp) {
                        $appointment = Rendez_vou::find($rendezVousEmp->appointment_id);

                        if ($appointment->status !== 'terminé' && $appointment->status !== 'annulé') {
                            $start = new \DateTime($appointment->date_and_time);
                            $end = (clone $start)->add(new \DateInterval('PT' . $this->convertToSeconds($appointment->duration) . 'S'));

                            if ($requestedStart < $end && $requestedStart >= $start) {
                                $conflict = true;
                                break;
                            }
                        }
                    }

                    if (!$conflict) {
                        $availableEmployees[] = $employe->employe_id;

                        if (count($availableEmployees) === $nb_employe) {
                            break;
                        }
                    }
                }
            }

            // Supprimer les anciennes assignations d'employés
            DB::table('employe_rendez_vous')
                ->where('appointment_id', $rendezVous->id_appointment)
                ->delete();

            // Assigner les nouveaux employés disponibles
            foreach ($availableEmployees as $employeId) {
                DB::table('employe_rendez_vous')->insert([
                    'employe_id' => $employeId,
                    'appointment_id' => $rendezVous->id_appointment
                ]);
            }
        }

        // Calculer la durée moyenne en fonction des nouveaux employés retenus
        $totalDuration = 0;
        foreach ($availableEmployees as $employeId) {
            $employeTypeCoiffure = DB::table('employe_type_coiffures')
                ->where('employe_id', $employeId)
                ->where('hairstyle_type_id', $hairstyle->id_hairstyle_type)
                ->first();

            if ($employeTypeCoiffure) {
                // Convertir la durée en secondes (depuis le format HH:MM:SS)
                $durationInSeconds = strtotime($employeTypeCoiffure->duration) - strtotime('TODAY');
                $totalDuration += $durationInSeconds; // Durée en secondes
            }
        }

        $averageDuration = $totalDuration / count($availableEmployees);
        // Convertir la durée moyenne en format HH:MM:SS
        $hours = floor($averageDuration / 3600);
        $minutes = floor(($averageDuration % 3600) / 60);
        $seconds = $averageDuration % 60;
        $averageDurationFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        // Mettre à jour les champs du rendez-vous
        $rendezVous->date_and_time = $request->input('date_and_time');
        $rendezVous->notes = $request->input('notes', $rendezVous->notes);
        $rendezVous->duration = $averageDurationFormatted;
        $rendezVous->save();

        $historiqueService = new Historique_service();

        // Remplir les propriétés de l'historique de service avec les données validées provenant de la requête
        $historiqueService->notes = $request->input('notes');;
        $historiqueService->date_rdv = $request->input('date_and_time');
        $historiqueService->amount_paid =$rendezVous->total_amount ;
        $historiqueService->hairstyle_name =$hairstyle->name;
        $historiqueService->image =$rendezVous->image;
        $historiqueService->duration=$rendezVous->duration;
        $historiqueService->appointment_id = $id;
        $historiqueService->client_id = $rendezVous->client_id;
        $historiqueService->status = "modifié";
        $historiqueService->salon_id=$rendezVous->salon_id;
        $historiqueService->date =  now()->toDateString();


        $historiqueService->save();

        $dateAndTime = $rendezVous->date_and_time;
        $client= Client::where('id_client', $rendezVous->client_id)->first();
        //dd($client);
        Mail::to($client->email)->send(new RappelRendezVous($dateAndTime));
        foreach ($availableEmployees as $index => $availableEmployee)
        {
            $employe = Employe::find($availableEmployee);
            if ($employe) {
                Mail::raw(" Hello $employe->name ! Les horaires du rendez-vous ont été changé pour  le : $dateAndTime pour la coiffure $hairstyle->name du client $client->name ", function ($message) use ($employe) {
                    $message->to($employe->email)
                            ->subject('Rappel de rdv');
                });

            }
        }

        $rendezVous->save();


        return response()->json(['data' => $rendezVous], 200);
    }

    public function destroy($id)
    {
        $rendezVous = Rendez_vou::findOrFail($id);
        $hairstyle = Type_coiffure::where('id_hairstyle_type', $rendezVous->hairstyle_type_id)->first();
        $accompte = $rendezVous->accompte;
        if($accompte)
        {
            $salon=Salon::where('id_salon',$rendezVous->salon_id)->first();
            $percent=$salon->percent_cancel;
            $somme_retire=($accompte*$percent/100);
            $somme_restant=($accompte-$somme_retire);
          $client =  DB::table('virtual_comptes')
          ->where('client_id' , $rendezVous->client_id)
          ->first();
          if($client)
          {
             DB::table('virtual_comptes')
            ->where('client_id' , $rendezVous->client_id)->increment('somme' , $somme_restant);
          }else{
            DB::table('virtual_comptes')->insert([
                'client_id' => $rendezVous->client_id,
                 'somme' => $somme_restant,

            ]);
          }
        }

        $historiqueService = new Historique_service();

        // Remplir les propriétés de l'historique de service avec les données validées provenant de la requête
        $historiqueService->notes = $rendezVous->notes;
        $historiqueService->date_rdv = $rendezVous->date_and_time;
        $historiqueService->amount_paid = $rendezVous->total_amount;
        $historiqueService->hairstyle_name =$hairstyle->name;
        $historiqueService->image =$rendezVous->image;
        $historiqueService->duration=$rendezVous->duration;
        $historiqueService->salon_id = $rendezVous->salon_id;
        $historiqueService->appointment_id = $id;
        $historiqueService->client_id = $rendezVous->client_id;
        $historiqueService->status = "Annulé";
        $historiqueService->date =  now()->toDateString();

        $historiqueService->save();
        $rendezVous->delete();

        return response()->json($historiqueService);
    }

    public function Terminer($id)
    {
        $rendezVous = Rendez_vou::findOrFail($id);
        $rendezVous->status="Terminé";
        $rendezVous->save();
        $historiqueService = new Historique_service();
        $hairstyle = Type_coiffure::where('id_hairstyle_type', $rendezVous->hairstyle_type_id)->first();

        // Remplir les propriétés de l'historique de service avec les données validées provenant de la requête
        $historiqueService->notes = $rendezVous->notes;
        $historiqueService->date_rdv = $rendezVous->date_and_time;
        $historiqueService->amount_paid = $rendezVous->total_amount;
        $historiqueService->hairstyle_name =$hairstyle->name;
        //$historiqueService->image =$rendezVous->image;
        $historiqueService->duration=$rendezVous->duration;
        $historiqueService->salon_id = $rendezVous->salon_id;
        $historiqueService->client_id = $rendezVous->client_id;
        $historiqueService->appointment_id = $id;
        $historiqueService->status = "Terminé";
        $historiqueService->date =  now()->toDateString();





            $client = Client::where('id_client' , $rendezVous->client_id)->first();
            $client_name = $client->name;
            $date_rdv = $historiqueService->date_rdv;
            $hairstyle_name = $historiqueService->hairstyle_name;
            $salon = Salon::where('id_salon', $historiqueService->salon_id)->first();
            $salon_name = $salon->salon_name;
            $historiqueService->review_send = "True";
            $historiqueService->save();
            $user_id = $client->user_id;


            $service_id=$historiqueService->id_service_history;

                $link = "http://localhost:5173/evaluation-form?id_service_history=$service_id&user_id=$user_id";

                $client->notify(new EvaluationlinkNotification($link, $client_name, $date_rdv, $hairstyle_name, $salon_name));
                return response()->json("review send succefully");
    }

    public function getRendezVousForSalon()
    {
            $user= Auth::user();
            $salon=Salon::where('user_id' , $user->id_user)->first();
            // Récupérer les rendez-vous du salon
            $rendezVousList = Rendez_vou::with(['client', 'hairstyle', 'employes'])
                ->where('salon_id', $salon->id_salon)
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

        public function getAccompte($id_appointment)
        {
            $rendezVou=Rendez_vou::where('id_appointment' , $id_appointment)->first();
            $accompte=$rendezVou->accompte;
            return response()->json(['accompte' => $accompte]);

        }

        public function getsomme()
        {
            $user=Auth::user();
            $client=Client::where('user_id' , $user->id_user)->first();
            $compte = DB::table('virtual_comptes')
            ->where('client_id' , $client->id_client)
            ->first();
            $somme=$compte->somme;
            return response()->json(['data' => $somme]);

        }

        public function getmontantrestant($id_appointment)
            {
                $rendezVou=Rendez_vou::where('id_appointment' , $id_appointment)->first();
                $montant_restant=($rendezVou->total_amount-$rendezVou->accompte);
                return response()->json(['montant_restant' =>  $montant_restant]);
            }

        public function solderdv($transactionId ,$id_appointment)
        {
            $kkiapay = new Kkiapay(config('kkiapay.public_key'),  config('kkiapay.private_key') , config('kkiapay.secret'), $sandbox = true);
            $transactionkkp = $kkiapay->verifyTransaction($transactionId);

            if ($transactionkkp->status != "SUCCESS") {
                throw new Exception("La transaction a échoué. Veuillez réessayer.");
            }
            $rendezVous=Rendez_vou::where('id_appointment' , $id_appointment)->first();
            if ((double)$transactionkkp->amount != (double)($rendezVous->total_amount-$rendezVous->accompte)) {
                return null;
            }


            $data = [];
            $data["performed_at"] =  $transactionkkp->performed_at;
            $data["received_at"] =  $transactionkkp->received_at;
            $data["status"] =  $transactionkkp->status;
            $data["amount"] =  $transactionkkp->amount;
            $data["source"] =  $transactionkkp->source;
            $data["source_common_name"] =  $transactionkkp->source_common_name;
            $data["fees"] =  $transactionkkp->fees;
            $data["net"] =  $transactionkkp->net;
            $data["externalTransactionId"] = $transactionkkp->client->phone."_".$transactionkkp->transactionId;
            $data["acc_fullname"] =  $transactionkkp->client->fullname;
            $data["acc_phone"] =  $transactionkkp->client->phone;
            $data["acc_email"] =  $transactionkkp->client->email;
            $data["acc_person"] =  $transactionkkp->client->person;
            $data["transactionId"] =  $transactionkkp->transactionId;
            $data["transaction_object"] =  'buy-course';
            $data["user_id"] =  Auth::user()->id_user;
            $data["appointment_id"] = $id_appointment;
            $data["id_transaction"] =  \Illuminate\Support\Str::uuid();

            Transaction::create($data);
            $rendezVous->status="Terminé";
            $rendezVous->save();

            $historiqueService = new Historique_service();
            $hairstyle = Type_coiffure::where('id_hairstyle_type', $rendezVous->hairstyle_type_id)->first();

            // Remplir les propriétés de l'historique de service avec les données validées provenant de la requête
            $historiqueService->notes = $rendezVous->notes;
            $historiqueService->date_rdv = $rendezVous->date_and_time;
            $historiqueService->amount_paid = $rendezVous->total_amount;
            $historiqueService->hairstyle_name =$hairstyle->name;
            //$historiqueService->image =$rendezVous->image;
            $historiqueService->duration=$rendezVous->duration;
            $historiqueService->salon_id = $rendezVous->salon_id;
            $historiqueService->client_id = $rendezVous->client_id;
            $historiqueService->appointment_id = $id_appointment;
            $historiqueService->status = "Terminé";
            $historiqueService->date =  now()->toDateString();





            $client = Client::where('id_client' , $rendezVous->client_id)->first();
            $client_name = $client->name;
            $date_rdv = $historiqueService->date_rdv;
            $hairstyle_name = $historiqueService->hairstyle_name;
            $salon = Salon::where('id_salon', $historiqueService->salon_id)->first();
            $salon_name = $salon->salon_name;
            $historiqueService->review_send = "True";
            $historiqueService->save();
            $user_id = $client->user_id;


            $service_id=$historiqueService->id_service_history;

                $link = "http://localhost:5173/evaluation-form?id_service_history=$service_id&user_id=$user_id";

                $client->notify(new EvaluationlinkNotification($link, $client_name, $date_rdv, $hairstyle_name, $salon_name));

        }
}
