<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;

class LanguageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $locale = session('locale', config('app.locale', 'ar'));
            App::setLocale($locale);
            
            $isRtl = $locale === 'ar';
            $view->with('dir', $isRtl ? 'rtl' : 'ltr');
            $view->with('align', $isRtl ? 'right' : 'left');
            $view->with('reverseAlign', $isRtl ? 'left' : 'right');
        });
    }
}
