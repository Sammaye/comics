<?php

namespace App\Http\Middleware;

use Barryvdh\Debugbar\LaravelDebugbar;
use Barryvdh\Debugbar\ServiceProvider;
use Closure;
use DebugBar\DebugBar;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Config;

class isDebugBarEnabled
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
        \Debugbar::enable();
        /*
         * TODO none of it works...
        if ($user->can('root')) {
            Config::set('app.debug', true);
            config(['app.debug' => true, 'debugbar.enabled' => true]);
            \DebugBar::enable();
            DebugBar::enable();
            //(new LaravelDebugbar())->enable();
            $provider = new ServiceProvider(app());
            $provider->register();
            $provider->boot();

        }
        */

        return $next($request);
    }
}
