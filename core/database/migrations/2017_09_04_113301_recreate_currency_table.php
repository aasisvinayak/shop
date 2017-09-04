<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class RecreateCurrencyTable extends Migration
{
    /**
     * Currencies table name
     *
     * @var string
     */
    protected $table_name;

    /**
     * Currencies to add when table is created
     *
     * @var array
     */
    protected $currencies_to_add = [
        'USD', // U.S. Dollar
        'EUR', // Euro
        'GBP', // Pound Sterling
        'AUD', // Australian Dollar
        'CAD', // Canadian Dollar
        'CZK', // Czech Koruna
        'DKK', // Danish Krone
        'HKD', // Hong Kong Dollar
        'HUF', // Hungarian Forint
        'ILS', // Israeli New Sheqel
        'JPY', // Japanese Yen
        'MXN', // Mexikan Peso
        'NOK', // Norwegian Krone
        'NZD', // New Zealand Dollar
        'PHP', // Philippine Peso
        'PLN', // Polish Zloty
        'SGD', // Singapore Dollar
        'SEK', // Swedish Krona
        'CHF', // Swiss Franc
        'TWD', // Taiwan New Dollar
        'THB', // Thai Baht
        'UAH', // Ukrainian hryvnia
        'ISK', // Icelandic króna
        'HRK', // Croatian kuna
        'RON', // Romanian leu
        'BGN', // Bulgarian lev
        'TRY', // Turkish lira
        'CLP', // Chilean peso
        'ZAR', // South African rand
        'BRL', // Brazilian real
        'MYR', // Malaysian ringgit
        'RUB', // Russian ruble
        'IDR', // Indonesian rupiah
        'INR', // Indian rupee
        'KRW', // Korean won
        'CNY', // Renminbi
    ];

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->table_name = config('currency.drivers.database.table');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('code', 10)->index();
            $table->string('symbol', 25);
            $table->string('format', 50);
            $table->string('exchange_rate');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        // Add currencies
        Artisan::call('currency:manage', [
            'action' => 'add',
            'currency' => implode(',', $this->currencies_to_add)
        ]);

        // Update rates
        Artisan::call('currency:update');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table_name);
    }
}
