<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller.
 *
 * @category Laravel
 *
 * @author Laravel <laravel@laravel.com>
 * @license https://github.com/laravel/laravel/blob/master/LICENSE  GPL-3.0
 *
 * @link https://github.com/laravel/laravel
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
