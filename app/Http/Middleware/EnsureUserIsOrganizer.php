<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->is_organizer) {
            return redirect()->route('dashboard')->with('error', 'Debes ser organizador para acceder a esta sección');
        }

        return $next($request);
    }
}
