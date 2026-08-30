<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    /** Locales offered in the header, in a stable display order. */
    public function locales(): array
    {
        return array_values(array_intersect(['en', 'fr', 'ar'], SetLocale::SUPPORTED_LOCALES));
    }

    public function render()
    {
        return view('livewire.locale-switcher', [
            'current' => app()->getLocale(),
            'locales' => $this->locales(),
        ]);
    }
}
