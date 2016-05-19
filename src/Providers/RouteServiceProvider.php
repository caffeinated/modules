<?php

namespace Caffeinated\Modules\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

abstract class RouteServiceProvider extends ServiceProvider
{
    /**
     * Set the root controller namespace for the application.
     */
    protected function setRootControllerNamespace()
    {
        // Intentionally left empty to prevent overwriting the
        // root controller namespace.
    }

    /**
     * Add middleware to the router.
     *
     * @param array $routeMiddleware
     */
    protected function addRouteMiddleware($routeMiddleware)
    {
        if (is_array($routeMiddleware) and count($routeMiddleware) > 0) {
            foreach ($routeMiddleware as $key => $middleware) {
                $this->middleware($key, $middleware);
            }
        }
    }

    /**
     * Add middleware groups to the router.
     *
     * @param array $middlewareGroups
     */
    protected function addMiddlewareGroups($middlewareGroups)
    {
        if (is_array($middlewareGroups) and count($middlewareGroups) > 0) {
            foreach ($middlewareGroups as $key => $groupMiddleware) {
                foreach ($groupMiddleware as $middleware) {
                    $this->pushMiddlewareToGroup($key, $middleware);
                }
            }
        }
    }
}
