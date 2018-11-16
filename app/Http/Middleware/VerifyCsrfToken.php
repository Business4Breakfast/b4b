<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Redirect;
use Session;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

}
