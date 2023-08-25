<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\V1\Traits\ResetsPasswordsApi;
use App\Http\Controllers\Controller;

class ResetPasswordApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswordsApi;


}
