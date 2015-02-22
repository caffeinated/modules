<?php
namespace Caffeinated\Modules\Middleware;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Str;

class ModuleMiddleware
{
    /**
     * The UrlGenerator implementation.
     *
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * Create a new filter instance.
     *
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Handle an incoming request and set root controller namespace in a module.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        if (!is_null($route)) {
            $action = $route->getAction();
            if (!empty($action['namespace'])) {
                if (Str::startsWith($action['namespace'], app('modules')->getNamespace())) {
                    $this->urlGenerator->setRootControllerNamespace($action['namespace']);
                }
            }
        }

        return $next($request);
    }
}
