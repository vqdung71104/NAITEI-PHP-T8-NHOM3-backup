<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ShareInertiaData
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'lang' => Session::get('lang', config('app.locale')),
            '_token' => csrf_token(),
        ]);

        return $next($request);
    }
}
