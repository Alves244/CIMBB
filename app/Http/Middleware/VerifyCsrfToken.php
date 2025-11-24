<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Add some lightweight logging when we detect CSRF issues on the login route so
     * we can understand why the token is failing in non-local browsers.
     */
    protected function tokensMatch($request)
    {
        $matches = parent::tokensMatch($request);

        if (! $matches && $request->is('session')) {
            logger()->warning('CSRF token mismatch on /session', [
                'has_session' => $request->hasSession(),
                'session_token_length' => strlen(optional($request->session())->token() ?? ''),
                'request_token_length' => strlen((string) $this->getTokenFromRequest($request)),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 120),
            ]);
        }

        return $matches;
    }
}
