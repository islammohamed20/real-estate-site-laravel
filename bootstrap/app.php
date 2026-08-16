<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsureNotForceLoggedOut;
use App\Http\Middleware\RequireTwoFactor;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackVisitor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            SecurityHeaders::class,
        ]);

        // The Evolution API WhatsApp instance posts incoming-message events here (no CSRF token).
        $middleware->validateCsrfTokens(except: [
            'webhook/whatsapp/evolution',
        ]);

        $middleware->alias([
            'active' => EnsureActiveUser::class,
            '2fa' => RequireTwoFactor::class,
            'force_logout' => EnsureNotForceLoggedOut::class,
            'track_visitor' => TrackVisitor::class,
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Centralized exception customization is added here as the application grows.
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Warn administrators before trashed plans are permanently deleted (7 days before purge, retention from Settings).
        $schedule->command('crm:warn-trashed-plans --days=0 --before=7')
            ->daily()
            ->at('09:00')
            ->withoutOverlapping()
            ->onOneServer();

        // Permanently delete installment plans that stayed in the trash past the retention period (from Settings).
        $schedule->command('crm:purge-trashed-plans --days=0 --auto')
            ->daily()
            ->at('03:00')
            ->withoutOverlapping()
            ->onOneServer();

        // Fallback sync of incoming WhatsApp messages when webhooks are not registered.
        $schedule->command('whatsapp:sync')
            ->everyTwoMinutes()
            ->withoutOverlapping();

        // Notify sales managers about unassigned conversations awaiting a reply for over an hour.
        $schedule->command('whatsapp:notify-unassigned --hours=1')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onOneServer();
    })
    ->create();
