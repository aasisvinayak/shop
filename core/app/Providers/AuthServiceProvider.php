<?php

namespace App\Providers;

use App\Http\Models\Address;
use App\Http\Models\Invoice;
use App\Http\Models\PaymentCard;
use App\Http\Models\UserDetail;
use App\Policies\AddressPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentCardPolicy;
use App\Policies\UserDetailPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model'        => 'App\Policies\ModelPolicy',
        Address::class     => AddressPolicy::class,
        PaymentCard::class => PaymentCardPolicy::class,
        UserDetail::class  => UserDetailPolicy::class,
        Invoice::class  => InvoicePolicy::class,

    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
