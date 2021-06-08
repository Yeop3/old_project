<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
use Illuminate\Http\Request;

/**
 * Class TrimStrings.
 */
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];

    protected array $exceptRoutes = [
        'bot_logics/client',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach ($this->exceptRoutes as $exceptRoute) {
            if (mb_stristr($request->url(), $exceptRoute) !== false) {
                return $next($request);
            }
        }

        $this->clean($request);

        return $next($request);
    }
}
