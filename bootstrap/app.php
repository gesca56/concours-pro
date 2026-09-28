<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // Fait confiance au proxy inverse de la plateforme d'hébergement (Render)
        // qui termine le HTTPS et transmet la requête en HTTP en interne, afin
        // que Laravel détecte correctement le schéma d'origine (X-Forwarded-Proto)
        // et génère des URLs d'assets en https:// plutôt qu'en http://.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Règle métier refusée (abort 422) sur le site : on revient à la page
        // précédente avec le message, plutôt qu'une page d'erreur brute.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 422 && ! $request->expectsJson()) {
                return back()->with('error', $e->getMessage());
            }

            // Jeton CSRF expiré (419 « Page Expired ») : formulaire ouvert avant une
            // déconnexion, dans un autre onglet ou avant un redémarrage du serveur.
            // On recharge la page avec un nouveau jeton au lieu d'afficher une erreur.
            if ($e->getStatusCode() === 419 && ! $request->expectsJson()) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Votre session a expiré. Veuillez réessayer.');
            }
        });
    })->create();
