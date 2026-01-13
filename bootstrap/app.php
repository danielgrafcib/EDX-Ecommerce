<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\User;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user' => \App\Http\Middleware\UserMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            $threshold = 5;
            $count = Product::where('stock', '<=', $threshold)->count();
            if ($count > 0) {
                $adminEmail = User::where('is_admin', true)->orderBy('id')->value('email');
                if ($adminEmail) {
                    Mail::raw('Alertes stock: '.$count.' produit(s) proche(s) de la rupture.', function ($m) use ($adminEmail) {
                        $m->to($adminEmail)->subject('Alerte stock');
                    });
                }
            }
        })->dailyAt('09:00');
    })
    ->create();
