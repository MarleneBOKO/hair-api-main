<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        //dd($request);
  
       /* $status = Password::sendResetLink(
            $request->only('email')
        );
       // dd($status);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);*/
            $token = Str::random(60); // Générer un nouveau token

            DB::table('password_reset_tokens')->insert([
                'email' => $request->input('email'),
                'token' => hash('sha256', $token), // Stockez le token sous forme hachée
                'created_at' => now(),
            ]);
            $email=$request->input('email');

            Mail::raw("Votre token : $token  ", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Votre token de réinitialisation');
            });
            return response()->json(['data' => $token ], 201);


    }
}
