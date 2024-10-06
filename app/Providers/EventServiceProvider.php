<?php

namespace App\Providers;


use App\Events\EmailChanged;
use App\Events\UserRegistered;
use App\Listeners\EmailChange;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Events\UserRegistrationSuccess;
use App\Listeners\SendVerificationEmail;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistrationSuccess::class => [
            SendWelcomeEmail::class,
        ],
        UserRegistered::class => [
            SendVerificationEmail::class,
        ],
        EmailChanged::class => [
            EmailChange::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
