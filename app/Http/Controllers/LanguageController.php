<?php

namespace App\Http\Controllers;

use Config;
use Redirect;
use Session;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {

        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('applocale', $lang);
        }
        return Redirect::back();
    }
}
