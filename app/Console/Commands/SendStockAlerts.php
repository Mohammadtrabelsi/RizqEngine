<?php

namespace App\Console\Commands;

use App\Mail\StockAlertDigest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

/**
 * Sends a stock-alert digest to the users who are responsible for
 * replenishment. The digest lists every product that is at or below its
 * configured stock-alert threshold and every product whose expiry date falls
 * within the look-ahead window.
 *
 * The app already surfaces these conditions visually on the dashboard; this
 * command adds the proactive push so a manager does not have to be looking at
 * the screen to notice. Intended to be scheduled daily (see routes/console.php).
 */
class SendStockAlerts extends Command
{
    /**
     * @var string
     */
    protected $signature = 'stock:send-alerts
                            {--days=30 : Expiry look-ahead window, in days}
                            {--dry-run : Report what would be sent without dispatching e-mail}';

    /**
     * @var string
     */
    protected $description = 'E-mail a low-stock and near-expiry digest to the responsible managers';

    /**
     * Roles that receive the digest by default. Falls back to any user when
     * none of these roles exist so the command is still useful out of the box.
     */
    private const RECIPIENT_ROLES = ['Super Admin', 'Admin', 'Owner', 'Manager'];

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        $lowStock = Product::query()
            ->with('category')
            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderBy('product_quantity')
            ->get();

        $expiring = Product::query()
            ->with('category')
            ->expiring($days)
            ->where('product_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        if ($lowStock->isEmpty() && $expiring->isEmpty()) {
            $this->info('Nothing to report: no low-stock or expiring products.');

            return self::SUCCESS;
        }

        $recipients = $this->recipients();

        if ($recipients->isEmpty()) {
            $this->warn('No active recipients found; skipping digest.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%d low-stock and %d product(s) expiring within %d day(s).',
            $lowStock->count(),
            $expiring->count(),
            $days
        ));

        if ($this->option('dry-run')) {
            $this->line('Recipients: '.$recipients->pluck('email')->implode(', '));

            return self::SUCCESS;
        }

        Mail::to($recipients->all())
            ->send(new StockAlertDigest($lowStock, $expiring, $days));

        $this->info('Stock-alert digest sent to '.$recipients->count().' recipient(s).');

        return self::SUCCESS;
    }

    /**
     * Active users who should receive the digest.
     *
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function recipients()
    {
        // Only filter by roles that actually exist; Spatie's role() scope
        // throws when handed an unknown role name (e.g. on a fresh install).
        $existingRoles = Role::query()
            ->whereIn('name', self::RECIPIENT_ROLES)
            ->pluck('name')
            ->all();

        if ($existingRoles !== []) {
            $byRole = User::query()
                ->where('is_active', 1)
                ->whereNotNull('email')
                ->role($existingRoles)
                ->get();

            if ($byRole->isNotEmpty()) {
                return $byRole;
            }
        }

        return User::query()
            ->where('is_active', 1)
            ->whereNotNull('email')
            ->get();
    }
}
