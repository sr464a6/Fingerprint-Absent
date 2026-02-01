<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\EnrollQueue;

class ClearEnrollQueue
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Truncate enroll queue before handling the request
        if (EnrollQueue::count() > 0) {
            EnrollQueue::truncate();
        }
        return $next($request);
    }
}
