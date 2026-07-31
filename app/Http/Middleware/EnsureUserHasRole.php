<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless($request->user() !== null && in_array($request->user()->role->value, $roles, true), 403);
        abort_if($request->user()->role === UserRole::Principal && ! $request->user()->isApproved(), 403, 'আপনার Principal account এখনো Admin কর্তৃক অনুমোদিত নয়।');

        return $next($request);
    }
}
