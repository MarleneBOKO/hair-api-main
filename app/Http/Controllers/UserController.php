<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use App\Mail\VerifyEmail;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;



class UserController extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

  
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user], 200);
    }

    
    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

    
        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->user_type = "Salon";
   
        $user->save();
        $user->sendEmailVerificationNotification();

       /* if($user->user_type=="Client")
        {
            $client= new Client();
            $client->name = $validatedData['name'];
            $client->email = $validatedData['email'];
            $client->user_id = $user->id_user;
            $client->save();
        }*/
               
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }
    

  
    public function updateUser(UserRequest $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

        ]);

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

   
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
