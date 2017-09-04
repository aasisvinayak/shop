<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],

        'App\Events\OrderShipped' => [
            'App\Listeners\SendShipmentNotification',
        ],

        'App\Events\OrderPlaced' => [
            'App\Listeners\SendOrderNotification',
        ],

        'App\Events\ProcessPayment' => [
            'App\Listeners\TakePayment',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
