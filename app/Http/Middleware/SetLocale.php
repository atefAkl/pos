<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('locale')) {
            session()->put('locale', config('app.locale', 'ar'));
        }

        $locale = session('locale');
        
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
            session()->put('locale', $locale);
        }

        App::setLocale($locale);
        return $next($request);
    }
}
