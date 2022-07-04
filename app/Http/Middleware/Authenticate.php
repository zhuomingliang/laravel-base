<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\Access\Response;

class Authenticate extends Middleware {
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request) {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards) {
        $this->authenticate($request, $guards);

        $path = trim($request->path(), '\\');
        $paths = explode('\\', $path, 2);

        if (!isset($paths[1]) && isset($paths[0])) {
            $method = strtolower($request->method());
            $path = "{$paths[0]}/{$method}Index";
        }

        if ($request->user()->can($paths[0]) || $request->user()->can($path)
         || $path == 'user/permissions/getIndex'
         || $path == 'HomeDecorationExpo/current/getIndex'
         || $path == 'User/ChangePassword/postIndex') {
            return $next($request);
        }


        return Response::deny()->authorize();
    }
}
