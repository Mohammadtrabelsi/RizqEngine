<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Triangle POS logo mark (triangle outline subdivided into four, centre one
 * filled with the accent colour) with an optional wordmark. All defaults and
 * the scaled height live here so the template carries no @php.
 */
class Logo extends Component
{
    public int $height;

    public function __construct(
        public int $size = 24,
        public string $stroke = 'var(--color-text)',
        public string $accent = 'var(--color-accent)',
        public int $sw = 7,
        public ?string $label = null,
    ) {
        $this->height = (int) round($size * 0.9);
    }

    public function render(): View
    {
        return view('components.logo');
    }
}
