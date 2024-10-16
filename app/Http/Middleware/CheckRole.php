<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole($roles)) {
            Auth::logout();
            abort(403, 'ACCESO NO AUTORIZADO');
        }
        return $next($request);
    }
}