<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ConvertRequestFieldsToSnakeCase
{
    /**
     * Transform the request fields from CamelCase to snake_case.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $replaced = [];
        foreach ($request->all() as $key => $value) {
            $replaced[Str::snake($key)] = $value;
        }
        $request->replace($replaced);

        return $next($request);
    }
}
