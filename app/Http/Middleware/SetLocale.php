<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?: $request->cookie('locale')
            ?: config('app.locale', 'ar');

        if (! in_array($locale, ['ar', 'en'], true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);

        $response = $next($request);

        if ($request->cookie('locale') !== $locale) {
            cookie()->queue(cookie()->forever('locale', $locale, null, null, null, $request->isSecure(), false, false, 'lax'));
        }

        return $response;
    }
}
