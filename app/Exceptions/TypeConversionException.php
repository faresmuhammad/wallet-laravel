<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TypeConversionException extends HttpResponseException
{
    public function __construct(Response $response, ?Throwable $previous = null)
    {
        parent::__construct($response, $previous);
    }
}
