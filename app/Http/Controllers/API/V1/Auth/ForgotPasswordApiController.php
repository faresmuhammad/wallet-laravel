<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\V1\Traits\SendsPasswordResetEmailsApi;
use App\Http\Controllers\Controller;

class ForgotPasswordApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmailsApi;
}
