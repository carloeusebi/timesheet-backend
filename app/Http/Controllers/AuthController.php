<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        /**
         * @var User
         */
        $user = Auth::user();
        $user->load('role');
        return $this->respondWithToken($user, $token);
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


    protected function respondWithToken(User $user, string $token)
    {
        $cookie = cookie('token', $token, 60);
        return response()->json($user)->withCookie($cookie);
    }
}
