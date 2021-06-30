<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WeWantJSONMiddleware
{
    /**
     * We only accept json
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('post') && !$request->expectsJson()) {
            // Verify if POST request is JSON
            return response(['message' => 'Only JSON requests are allowed'], 406);
        } elseif (!$request->isMethod('post')) {
            // Verify if it's not POST request
            return response(['message' => 'Resource not found'], 404);
        } else 
            return $next($request);
    }
}
