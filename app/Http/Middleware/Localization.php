<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Localization
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
        if (Session::has('language')) {
            $language = Session::get('language');
            App::setLocale($language);
        }
        $this->shareLanguageToAllView();
        return $next($request);
    }

    protected function shareLanguageToAllView()
    {
        view()->composer('*', function ($view) {
            $currentLanguage = Session::get('language', 'vi');
            if (empty($currentLanguage)) {
                $currentLanguage = 'en';
            }
            $view->with('currentLanguage', $currentLanguage);
        });
    }
}
