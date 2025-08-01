<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    public const INDEX = 'auth.login';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
        public static function getHomeRoute()
    {
        if (Auth::guest()) {
            throw new UnauthorizedException();
        }
        if (Auth::guard()->check()){
            return URL::route('ui.payment.index');
        }
        //TODO: Where we need admin, we need to authorize this commit
        // if (Auth::guard()->check() && !Auth::user()->admin){
        //     return URL::route('ui.index');
        
        // } 
        // else if (Auth::guard()->check() && Auth::user()->admin){
        //     return URL::route('admin.docs.index');
        // } 
        else {

            Auth::logout();
            Session::invalidate();

            Session::regenerateToken();

            Session::put('flash', __('app.invalid_access_permission'));
            Session::put('flashType', 'warning');

            return URL::route('auth.login');
        }
    }
}
