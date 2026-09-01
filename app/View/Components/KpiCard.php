<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * A dashboard KPI card. The tone → styling mapping is resolved here so the
 * Blade template stays free of PHP logic.
 */
class KpiCard extends Component
{
    public string $card;

    public string $chipClasses;

    public function __construct(
        public string $label,
        public string $value,
        public ?string $chip = null,
        public string $chipTone = 'neutral',
        public ?string $footer = null,
        public string $tone = 'default',
    ) {
        $this->card = $tone === 'warn'
            ? 'border-warn-border bg-warn-bg'
            : 'border-hairline bg-white';

        $this->chipClasses = match ($chipTone) {
            'ok' => 'bg-ok-bg text-ok',
            'warn' => 'bg-warn-bg text-warn',
            default => 'bg-canvas-2 text-ink-3',
        };
    }

    public function render(): View
    {
        return view('components.kpi-card');
    }
}
