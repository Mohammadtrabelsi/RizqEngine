<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

/**
 * Moves every expired product that still holds stock to an "out of stock"
 * state by zeroing its on-hand quantity.
 *
 * A product is considered expired when it has an {@see Product::$expiry_date}
 * that is on or before today. This mirrors the manual "Mark expired as out of
 * stock" manager exposed on the products listing, so it can be scheduled to run
 * automatically (e.g. daily) to keep expired stock off the shop floor.
 */
class MarkExpiredProductsOutOfStock extends Command
{
    /**
     * @var string
     */
    protected $signature = 'products:mark-expired-out-of-stock
                            {--dry-run : List the products that would be affected without changing them}';

    /**
     * @var string
     */
    protected $description = 'Set the quantity of every expired product to zero (out of stock)';

    public function handle(): int
    {
        $query = Product::query()->expired()->where('product_quantity', '>', 0);

        $count = $query->count();

        if ($count === 0) {
            $this->info('No expired products with remaining stock.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("{$count} expired product(s) would be marked out of stock:");

            $query->get(['id', 'product_name', 'product_code', 'product_quantity', 'expiry_date'])
                ->each(function (Product $product) {
                    $this->line(sprintf(
                        ' - #%d %s (%s) qty=%d expired=%s',
                        $product->id,
                        $product->product_name,
                        $product->product_code,
                        $product->product_quantity,
                        optional($product->expiry_date)->format('Y-m-d')
                    ));
                });

            return self::SUCCESS;
        }

        $affected = $query->update(['product_quantity' => 0]);

        $this->info("{$affected} expired product(s) marked out of stock.");

        return self::SUCCESS;
    }
}
