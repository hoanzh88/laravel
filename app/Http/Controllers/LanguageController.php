<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
        $language = $request->language;
        if ($this->isValidLanguage($language)) {
            App::setLocale($language);
            Session::put('language', $language);
            echo $language;
        }
        // return redirect()->back();
    }

    private function isValidLanguage(string $language): bool
    {
        return in_array($language, ['en', 'vi']);
    }
}
