<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    /** Locales offered in the redesign header (subset of supported locales). */
    public function locales(): array
    {
        return array_values(array_intersect(['en', 'fr'], SetLocale::SUPPORTED_LOCALES));
    }

    public function render()
    {
        return view('livewire.locale-switcher', [
            'current' => app()->getLocale(),
            'locales' => $this->locales(),
        ]);
    }
}
