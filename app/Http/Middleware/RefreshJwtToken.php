<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshJwtToken
{
    /**
     * Refresh the tokens, if a user is logged.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!Auth::user() || $this->isLogoutRequest($request)) return $next($request);

        // // refresh the token
        // JWTAuth::parseToken();
        // $newToken = JWTAuth::refresh();

        // // attach the token to the response's cookie
        // $response = $next($request);
        // $response->withCookie(cookie('token', $newToken, 60));

        // return $response;

        return $next($request);
    }

    /**
     * Check if the current request is request for logout.
     */
    private function isLogoutRequest(Request $request): bool
    {
        return $request->path() === 'api/auth/logout';
    }
}
