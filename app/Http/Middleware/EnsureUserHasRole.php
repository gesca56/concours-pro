<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if($user === null, 403);

        $allowed = array_map(fn (string $role) => Role::from($role), $roles);

        abort_unless(in_array($user->role, $allowed, true), 403, "Accès réservé à un autre rôle.");

        return $next($request);
    }
}
