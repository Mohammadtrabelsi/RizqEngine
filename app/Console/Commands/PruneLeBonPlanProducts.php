<?php

namespace App\Console\Commands;

use App\Models\Product;
use Database\Seeders\LeBonPlanSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Deletes every product whose name is not part of the canonical "Le Bon Plan"
 * catalogue defined in {@see LeBonPlanSeeder}.
 *
 * A product is kept when any of its stored translations matches one of the
 * seeder's product names, so seeded products that later received a translation
 * are not removed by mistake. Deletion relies on the products table foreign
 * keys (detail tables null out, stock tables cascade), mirroring the behaviour
 * of the regular product deletion flow.
 */
class PruneLeBonPlanProducts extends Command
{
    /**
     * @var string
     */
    protected $signature = 'products:prune-le-bon-plan
                            {--dry-run : List the products that would be deleted without deleting them}';

    /**
     * @var string
     */
    protected $description = 'Delete all products that do not exist in the LeBonPlanSeeder catalogue.';

    public function handle(): int
    {
        $canonical = collect(LeBonPlanSeeder::productRows())
            ->map(fn (array $row): string => $this->normalise($row[0]))
            ->unique()
            ->flip();

        $toDelete = Product::query()->get()->filter(function (Product $product) use ($canonical): bool {
            $names = array_values($product->getTranslations('product_name'));
            $names[] = $product->getRawOriginal('product_name');

            foreach ($names as $name) {
                if (! is_string($name) || $name === '') {
                    continue;
                }

                if ($canonical->has($this->normalise($name))) {
                    return false;
                }
            }

            return true;
        });

        if ($toDelete->isEmpty()) {
            $this->info('No products to prune — every product exists in the LeBonPlanSeeder catalogue.');

            return self::SUCCESS;
        }

        $this->warn(sprintf('%d product(s) are not in the LeBonPlanSeeder catalogue:', $toDelete->count()));
        foreach ($toDelete as $product) {
            $this->line(sprintf('  - [#%d] %s', $product->id, (string) $product->product_name));
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run — nothing was deleted.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($toDelete): void {
            foreach ($toDelete as $product) {
                $product->delete();
            }
        });

        $this->info(sprintf('Deleted %d product(s).', $toDelete->count()));

        return self::SUCCESS;
    }

    /**
     * Normalise a product name for comparison: trim surrounding whitespace,
     * collapse inner runs of whitespace and lower-case it so that trivial
     * spacing/casing differences do not cause a canonical product to be pruned.
     */
    private function normalise(string $name): string
    {
        return mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', $name)));
    }
}
