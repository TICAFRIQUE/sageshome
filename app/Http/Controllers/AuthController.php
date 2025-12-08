<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        // Sauvegarder l'URL précédente si elle existe et n'est pas login/register
        $previous = url()->previous();
        $current = url()->current();
        
        if ($previous !== $current && 
            !str_contains($previous, '/login') && 
            !str_contains($previous, '/register')) {
            session(['url.intended' => $previous]);
        }

        // dd(session('url.intended'));
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Rediriger vers la page précédente ou la page d'accueil
            return redirect()->intended(route('home'))->with('success', 'Connexion réussie !');
        }

        throw ValidationException::withMessages([
            'email' => ['Les informations d\'identification fournies ne correspondent pas à nos enregistrements.'],
        ]);
    }

    public function showRegister(Request $request)
    {
        // Sauvegarder l'URL précédente si elle existe et n'est pas login/register
        $previous = url()->previous();
        $current = url()->current();
        
        if ($previous !== $current && 
            !str_contains($previous, '/login') && 
            !str_contains($previous, '/register')) {
            session(['url.intended' => $previous]);
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        // Assigner le rôle client
        $user->assignRole('client');

        Auth::login($user);

        // Rediriger vers la page précédente ou la page d'accueil
        return redirect()->intended(route('home'))->with('success', 'Inscription réussie !');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
