<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PurchaseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            return;
        }

        $startDate = Carbon::today()->subMonths(3);
        $endDate = Carbon::today();

        $statuses = ['Pending', 'Ordered', 'Completed'];
        $paymentStatuses = ['Paid', 'Partial', 'Unpaid'];
        $paymentMethods = ['Cash', 'Credit Card', 'Cheque', 'Bank Transfer'];

        foreach (range(1, 80) as $index) {
            $date = Carbon::parse($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $supplier = $suppliers->random();
            $totalAmount = rand(5000, 200000);
            $discountAmount = rand(0, (int) round($totalAmount * 0.1));
            $taxAmount = rand(0, (int) round($totalAmount * 0.1));
            $shippingAmount = rand(0, 10000);
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $paidAmount = match ($paymentStatus) {
                'Paid' => $totalAmount,
                'Partial' => rand(0, $totalAmount),
                default => 0,
            };

            $dueAmount = $totalAmount - $paidAmount;

            $purchase = Purchase::create([
                'date' => $date->toDateString(),
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->supplier_name,
                'tax_percentage' => rand(0, 20),
                'tax_amount' => $taxAmount,
                'discount_percentage' => rand(0, 15),
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $statuses[array_rand($statuses)],
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'note' => 'Seeded purchase for testing purposes.',
            ]);

            if ($paidAmount > 0) {
                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    // Amount setter scales to cents; the stored paid amount is
                    // already in cents, so scale it back to major units here.
                    'amount' => $paidAmount / 100,
                    'date' => $date->toDateString(),
                    'reference' => 'INV/'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT),
                    'payment_method' => $purchase->payment_method,
                    'note' => 'Seeded purchase payment.',
                ]);
            }
        }

        Model::reguard();
    }
}
