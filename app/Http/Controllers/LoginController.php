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
            'phone' => 'required|min:10'
        ]);
        //will find a user or create
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);
        if (!$user) {
            return response()->json(['message' => 'Could not process a user with that phone number'], 401);
        }
        $user->notify(new LoginNeedsVerification());
        return response()->json(['message' => 'Text message notification sent.']);
    }
    
    /**
     * verify code
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();
        if ($user) {
            $user->update([
                'login_code' => null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }
        return response()->json(['message' => 'Invalid verification code'], 401);
    }
}
