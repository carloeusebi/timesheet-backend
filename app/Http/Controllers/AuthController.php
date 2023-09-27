<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Attempt login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        return $this->createNewToken($token);
    }


    /**
     * Logout the User.
     */
    public function logout()
    {
        Auth::logout();
        return response(status: 204);
    }


    /**
     * Get the authenticated User.
     */
    public function user()
    {
        return response()->json(Auth::user());
    }


    protected function createNewToken(string $token)
    {
        $cookie = cookie('token', $token, 60);

        return response()->json(Auth::user())->withCookie($cookie);
    }
}
