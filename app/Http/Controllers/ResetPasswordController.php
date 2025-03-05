<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller

{
    public function reset(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);
           // dd($request->all());
           //dd(hash('sha256', $request->input(('token'))));

           $token=DB::table('password_reset_tokens')->where('email', $request->input('email'))
           ->where('token' , hash('sha256', $request->input(('token'))))->first();
           //dd($token);
           if(!$token){
            throw new \Exception("token invalide");
           }

           $user=User::where('email' , $request->input('email'))->first();
           if(!$user){
            throw new \Exception("L'utilisateur n'existe pas");
           }
           $user->password=Hash::make($request->password);
           $user->save();

           DB::table('password_reset_tokens')->where('email', $request->input('email'))
           ->where('token' , hash('sha256', $request->input(('token'))))->delete();                     
            
            /*$status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));
         
                    $user->save();
         
                    event(new PasswordReset($user));
                }
            );
        
            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => __($status)], 200)
                : response()->json(['message' => __($status)], 400);*/
                return response()->json("password réinitialisé");

        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
}

