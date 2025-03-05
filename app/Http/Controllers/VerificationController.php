<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user || !hash_equals($hash, sha1($user->email))) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully']);
    }
}
