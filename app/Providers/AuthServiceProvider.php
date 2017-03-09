<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // should return either a User instance or null. You're free to obtain
        // application. The callback which receives the incoming request instance
        // the User instance via an API token or any other method necessary.

        
        
        $this->app['auth']->viaRequest('api', function ($request) {
            $header = $request->input('apitoken');
            if ($header && $header == 'ajitirta') {
                return new User();
            }
            return null;
            /*if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }*/
        });
    }
}
