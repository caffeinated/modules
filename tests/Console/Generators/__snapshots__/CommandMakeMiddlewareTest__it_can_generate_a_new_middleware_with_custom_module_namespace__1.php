<?php return '<?php

namespace App\\MiddlewareModules\\Middleware\\Http\\Middleware;

use Closure;

class CustomMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \\Illuminate\\Http\\Request  $request
     * @param  \\Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
';
