<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch($locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            session()->put('locale', $locale);
        }
        return redirect()->back();
    }
}
