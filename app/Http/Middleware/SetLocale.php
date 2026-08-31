<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const LOCALES = ['ar', 'fr'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (! in_array($locale, self::LOCALES, true)) {
            $locale = in_array(config('app.locale'), self::LOCALES, true) ? config('app.locale') : 'ar';
        }

        app()->setLocale($locale);
        app()->setFallbackLocale('fr');

        return $next($request);
    }
}
