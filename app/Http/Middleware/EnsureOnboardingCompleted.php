<?php
// app/Http/Middleware/EnsureOnboardingCompleted.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas authentifié, laisser passer
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $tenant = $user->tenant;

        // Vérifier si le tenant existe
        if (!$tenant) {
            // Log erreur critique
            logger()->critical('User sans tenant détecté', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Erreur de configuration. Veuillez contacter le support.');
        }

        // Récupérer la progression onboarding
        $progress = $tenant->onboardingProgress;

        // Si pas de progression, créer
        if (!$progress) {
            $progress = $tenant->onboardingProgress()->create([
                'current_step' => 1,
                'completed' => false,
            ]);
        }

        // Routes exclues de la vérification
        $excludedRoutes = [
            'onboarding',
            'logout',
            'profile.*',
            'support.*',
        ];

        // Si la route actuelle est exclue, laisser passer
        foreach ($excludedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // Si onboarding pas terminé et pas sur la page onboarding
        if (!$progress->completed && !$request->routeIs('onboarding')) {
            // Flash message explicatif
            session()->flash('warning', 'Veuillez d\'abord compléter la configuration de votre entreprise.');

            return redirect()->route('onboarding');
        }

        // Si onboarding terminé et sur la page onboarding, rediriger dashboard
        if ($progress->completed && $request->routeIs('onboarding')) {
            return redirect()->route('dashboard')
                ->with('info', 'Votre configuration est déjà complète.');
        }

        return $next($request);
    }
}
