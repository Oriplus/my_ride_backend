<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{    
    /**
     * submit
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);
        //will find a user or create
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);
        if(!$user){
             return response()->json(['message' => 'Could not process a user with that phone number'], 401);
        }
        $user->notify(new LoginNeedsVerification());
        return response()->json(['message' => 'Text message notification sent.']);
    }
}
