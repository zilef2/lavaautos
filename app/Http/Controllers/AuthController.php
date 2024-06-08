<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends Controller{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $accessToken = $tokenResult->accessToken;
        $expiresAt = $tokenResult->token->expires_at ?? now()->addMinutes(30);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'errors' => $validator->errors()
            ], 422));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $tokenResult = $user->createToken('Personal Access Token');
        $accessToken = $tokenResult->accessToken;
        $expiresAt = $tokenResult->token->expires_at ?? now()->addMinutes(30);
//        $expiresAt = $tokenResult->token->expires_at ?? now()->addWeeks(1);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt
        ]);

//        return 'si llega?';
//        return response()->json([
//            'access_token' => $accessToken,
//            'token_type' => 'Bearer',
//            'expires_at' => $expiresAt
//        ]);
    }
}
