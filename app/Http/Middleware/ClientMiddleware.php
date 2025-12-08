<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check() && in_array(Auth::user()->role, ['client', 'developpeur'])) {
            return $next($request);
        }

        // ✅ Ne pas sauvegarder /login ou /register
        $current = $request->fullUrl();
        if (!str_contains($current, '/login') && !str_contains($current, '/register')) {
            session(['redirect_after_login' => $current]);
            session()->save(); // Forcer la sauvegarde
        }

        return redirect()->route('login')
            ->with('error', 'Veuillez vous connecter pour passer vos commandes.');
    }
}
