<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],
        \App\Events\User\UserCreatedEvent::class => [
            \App\Listeners\User\SendUserCreated::class
        ],
        \App\Events\Security\PasswordSetEvent::class => [
            \App\Listeners\Security\LogPasswordSet::class,
            \App\Listeners\Security\NotifyPasswordUpdate::class,
        ],
        \App\Events\Security\PasswordResetEvent::class => [
            \App\Listeners\Security\PasswordResetNotifier::class,
            \App\Listeners\Security\PasswordResetActivityLogger::class,
        ]
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
