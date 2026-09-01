<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Renders a coloured status pill. The status → colour/label mapping lives here
 * (not in the Blade template) so the view carries no PHP logic.
 */
class StatusPill extends Component
{
    public string $classes;

    public string $label;

    public function __construct(string $status = 'draft')
    {
        [$this->classes, $this->label] = $this->attributesForStatus($status);
    }

    /**
     * @return array{0: string, 1: string} [utility classes, translated label]
     */
    private function attributesForStatus(string $status): array
    {
        $map = [
            'paid' => ['bg-ok-bg text-ok', __('status.paid')],
            'partial' => ['bg-warn-bg text-warn', __('status.partial')],
            'return' => ['bg-danger-bg text-danger', __('status.return')],
            'draft' => ['bg-canvas-2 text-ink-3', __('status.draft')],
        ];

        return $map[$status] ?? $map['draft'];
    }

    public function render(): View
    {
        return view('components.status-pill');
    }
}
