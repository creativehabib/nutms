<?php

namespace App\Livewire\Layout;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function changeLanguage(string $lang): void
    {
        if (in_array($lang, ['en', 'bn'], true)) {
            Session::put('locale', $lang);

            if (Auth::check()) {
                Auth::user()->update(['locale' => $lang]);
            }
            App::setLocale($lang);
        }
        $this->redirect(request()->header('Referer') ?? '/dashboard');
    }

    public function render(): View
    {
        $currentLocale = Auth::check() ? Auth::user()->locale : Session::get('locale', config('app.locale'));

        return view('livewire.layout.language-switcher', ['currentLocale' => $currentLocale]);
    }
}
