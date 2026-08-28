<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\CommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CommandeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 100 Commandes (confirmed customer orders) with line items so the
     * orders listing has realistic data to work with. Monetary values are
     * stored as integer cents, matching how the application persists them.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $startDate = Carbon::today()->subMonths(3);
        $endDate = Carbon::today();

        $statuses = [
            Commande::STATUS_PENDING,
            Commande::STATUS_CONFIRMED,
            Commande::STATUS_INVOICED,
        ];

        foreach (range(1, 100) as $index) {
            $date = Carbon::parse($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $customer = $customers->random();

            // Build 1-5 line items and derive the header total from them.
            $lineCount = rand(1, 5);
            $lines = [];
            $subtotal = 0;

            foreach (range(1, $lineCount) as $l) {
                $product = $products->random();
                $unitPrice = (int) $product->getRawOriginal('product_price');
                $quantity = rand(1, 10);
                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;

                $lines[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_code' => $product->product_code,
                    'quantity' => $quantity,
                    'price' => $lineSubtotal,
                    'unit_price' => $unitPrice,
                    'sub_total' => $lineSubtotal,
                    'product_discount_amount' => 0,
                    'product_discount_type' => 'fixed',
                    'product_tax_amount' => 0,
                ];
            }

            $taxPercentage = rand(0, 20);
            $discountPercentage = rand(0, 15);
            $discountAmount = (int) round($subtotal * $discountPercentage / 100);
            $taxAmount = (int) round(($subtotal - $discountAmount) * $taxPercentage / 100);
            $shippingAmount = rand(0, 10000);
            $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

            $commande = Commande::create([
                'date' => $date->toDateString(),
                'customer_id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'status' => $statuses[array_rand($statuses)],
                'note' => 'Seeded commande for testing purposes.',
            ]);

            foreach ($lines as $line) {
                $line['commande_id'] = $commande->id;
                CommandeDetails::create($line);
            }
        }
    }
}
