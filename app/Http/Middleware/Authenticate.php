<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Response;

class Authenticate extends Middleware
{
    /**
     * @var array<int, string>
     */
    protected $guards = [];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->guards = $guards;

        try {
            $this->authenticate($request, $guards);
            $res = parent::handle($request, $next, ...$guards);

            return $res;
        } catch (Exception $e) {
            return Response::make('Authorization Token not found', config('constants.validation_codes.unauthorized'));
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return ''; // return route('login');
        }

        return '';
    }
}
