<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless($request->user()?->hasAnyRole($roles), 403);
        abort_if($request->user()->hasRole('principal') && ! $request->user()->isApproved(), 403, 'আপনার Principal account এখনো Admin কর্তৃক অনুমোদিত নয়।');

        return $next($request);
    }
}
