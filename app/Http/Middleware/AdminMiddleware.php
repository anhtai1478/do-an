<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (int) $request->user()->level !== 2) {
            abort(403, 'Bạn không có quyền truy cập vào trang này.');
        }
        ;
        return $next($request);
    }
}
