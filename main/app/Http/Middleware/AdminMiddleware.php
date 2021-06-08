<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

/**
 * Class AdminMiddleware.
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $parsedParams = collect(parse_url(config('app.url')));
        $adminHost = $parsedParams->get('host');

        if ($request->getHost() !== $adminHost) {
            abort(404);
        }

        $user = $request->user();

        if ($user->role !== User::ROLE_ADMIN) {
            abort(403);
        }

        return $next($request);
    }
}
