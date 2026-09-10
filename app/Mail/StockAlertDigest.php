<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Daily digest e-mail warning managers about products that are low on stock,
 * out of stock, or approaching their expiry date. Built by the
 * {@see \App\Console\Commands\SendStockAlerts} command.
 */
class StockAlertDigest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection  $lowStock  Products at or below their alert threshold.
     * @param  Collection  $expiring  Products expiring within the alert window.
     * @param  int  $expiryDays  Size of the expiry look-ahead window, in days.
     */
    public function __construct(
        public Collection $lowStock,
        public Collection $expiring,
        public int $expiryDays,
    ) {}

    public function build(): self
    {
        $company = settings()->company_name ?? config('app.name');

        return $this->subject(__('app.stock-alert-subject', ['company' => $company]))
            ->view('emails.stock-alerts', [
                'lowStock' => $this->lowStock,
                'expiring' => $this->expiring,
                'expiryDays' => $this->expiryDays,
                'company' => $company,
            ]);
    }
}
