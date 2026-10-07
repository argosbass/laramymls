<?php

namespace App\Providers;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate as FacadesGate;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        // super admin
        FacadesGate::before(function (User $user, string $ability)
        {
            return $user->isSuperAdmin() ? true: null;
        });

        Event::listen(Login::class, function (Login $event)
        {
            $event->user->forceFill([
                'last_login_at' => now(),
            ])->save();
        });


    }
}
