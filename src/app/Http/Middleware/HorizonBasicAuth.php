<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HorizonBasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        $username = env('HORIZON_USER', 'admin');
        $password = getenv('HORIZON_PASSWORD') ?: 'horizon';

        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            return response('غیر مجاز', 401)
                ->header('WWW-Authenticate', 'Basic realm="Horizon"');
        }

        return $next($request);
    }
}