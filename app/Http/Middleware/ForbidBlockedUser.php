<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard as StatefulGuardContract;

class ForbidBlockedUser
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        $user = $this->auth->user();

        if ($user && $user->isBlocked()) {
            if ($this->auth instanceof StatefulGuardContract) {
                $this->auth->logout();
            }
            return redirect()
                ->route('login')
                ->withInput()
                ->withErrors([
                    'login' => 'This account is blocked.',
                ]);
        }

        return $next($request);
    }
}
