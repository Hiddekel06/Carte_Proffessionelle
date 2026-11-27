<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /** Nombre d'essais ratés autorisés avant blocage. */
    private const MAX_TENTATIVES = 5;

    /** Durée du blocage, en secondes (15 minutes). */
    private const DUREE_BLOCAGE = 900;

    /**
     * Affiche la page de connexion Super Admin.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.agents.index');
        }

        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Protection contre les essais répétés : 5 tentatives par email + adresse IP,
        // puis blocage temporaire.
        $cle = 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($cle, self::MAX_TENTATIVES)) {
            $secondes = RateLimiter::availableIn($cle);
            $minutes = (int) ceil($secondes / 60);

            return back()->withErrors([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$minutes} minute(s).",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($cle);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.agents.index'))
                ->with('success', 'Bienvenue sur votre espace d\'administration Super Admin.');
        }

        RateLimiter::hit($cle, self::DUREE_BLOCAGE);
        $restantes = RateLimiter::remaining($cle, self::MAX_TENTATIVES);

        $message = 'Identifiants incorrects. Veuillez vérifier votre adresse email et votre mot de passe.';
        if ($restantes > 0 && $restantes <= 2) {
            $message .= " Il vous reste {$restantes} tentative(s) avant un blocage temporaire.";
        }

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
