<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\EmailService;

class AuthController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function showLogin(Request $request)
    {
        // Sauvegarder l'URL précédente si elle existe et n'est pas login/register
        $previous = url()->previous();
        $current = url()->current();

        if (
            $previous !== $current &&
            !str_contains($previous, '/login') &&
            !str_contains($previous, '/register')
        ) {
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

        if (
            $previous !== $current &&
            !str_contains($previous, '/login') &&
            !str_contains($previous, '/register')
        ) {
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

    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation par email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Aucun compte n\'est associé à cette adresse email.'
        ]);

        // Générer un token unique
        $token = Str::random(64);

        // Supprimer les anciens tokens pour cet email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Créer un nouveau token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Récupérer l'utilisateur
        $user = User::where('email', $request->email)->first();

        // Envoyer l'email avec le lien de réinitialisation
        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        $this->emailService->send(
            $request->email,
            'Réinitialisation de votre mot de passe - Sages Home',
            'emails.reset-password',
            [
                'user' => $user,
                'resetLink' => $resetLink,
                'token' => $token
            ]
        );

        return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
    }

    /**
     * Afficher le formulaire de réinitialisation du mot de passe
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.exists' => 'Aucun compte n\'est associé à cette adresse email.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.'
        ]);

        // Vérifier le token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation est invalide.']);
        }

        // Vérifier si le token correspond
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation est invalide.']);
        }

        // Vérifier si le token n'a pas expiré (1 heure)
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation a expiré.']);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Envoyer un email de confirmation
        $this->emailService->send(
            $request->email,
            'Mot de passe modifié - Sages Home',
            'emails.password-changed',
            ['user' => $user]
        );

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
