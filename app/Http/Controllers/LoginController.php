<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Salon;
use App\Mail\LoginLinkMail;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validation des données de connexion
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Authentification de l'utilisateur
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Récupération de l'utilisateur authentifié
        $user = User::where('email', $credentials['email'])->first();

        /*if (is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Email address not verified'], 403);
        }*/

        // Génération et enregistrement du token de connexion
        $loginToken = random_int(10000, 99999);
        $user->login_token = $loginToken;
        $user->save();



        // Envoi du lien de connexion par e-mail
        Mail::raw("Your login token: $loginToken", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Login Token');
        });

        return response()->json(['message' => 'Login token sent to your email' ]);
    }

    public function loginWithToken($token)
    {
        // Recherche de l'utilisateur par le token de connexion
        $user = User::where('login_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

         // Authentifier l'utilisateur
         // Auth::login($user);

       // Vérifier si l'utilisateur est authentifié


            // Supprimer tous les autres tokens de l'utilisateur
             $user->tokens()->delete();

            // Générer un nouveau token d'accès avec un nom spécifique
            $accessToken = $user->createToken('Custom API Token')->plainTextToken;



            // Créer un nouveau token avec le hash du token généré et spécifier une valeur pour le champ 'name'
           // $user->tokens()->create(['token' =>$accessToken, 'name' => 'Custom API Token']);

            return response()->json(['message' => 'User logged in successfully', 'token' => $accessToken]);

    }




   public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'User logout']);
    }

    public function getUserType()
    {
        $userconn = Auth::user();
        $user = User::where('id_user', $userconn->id_user)->first();
        $type = $user->user_type;
        $salon = null;
        $client=null;

        if ($type === "Salon") {
            $salon = Salon::where('user_id', $user->id_user)->first();
            if($salon)
            {
                $data = [
                    "type" => "Salon",
                    "is_active" => $salon ? $salon->status === "Actif" : false
                ];
                return response()->json(['data' => $data, 'user' => $user->id_user, 'type' => $type]);


            }else{
                $data="salon non existant";
            }


        } elseif ($type === "Client") {
            $data = ["type" => "Client"];
            $client=Client::where('user_id', $user->id_user)->first();
            return response()->json(['data' => $data, 'user' => $user->id_user, 'type' => $type,'salon'=>$client->salon_id]);

        } else {
            $data = ["type" => "Admin"];
            return response()->json(['data' => $data, 'user' => $user->id_user, 'type' => $type]);

        }

    }

    }
 






