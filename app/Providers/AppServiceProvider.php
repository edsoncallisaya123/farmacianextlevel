<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Hora de Bolivia para fechas de ventas, compras y caja
        date_default_timezone_set('America/La_Paz');
        config(['app.timezone' => 'America/La_Paz']);

        // WhatsApp de la farmacia (código de Bolivia 591 + número)
        View::share('whatsapp', '59169185410');
        View::share('whatsappVisible', '69185410');
    }
}
