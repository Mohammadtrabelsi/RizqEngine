<?php

namespace Database\Seeders;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnPayment;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PurchasesReturnDatabaseSeeder extends Seeder
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

        $statuses = ['Pending', 'Completed'];
        $paymentStatuses = ['Paid', 'Partial', 'Unpaid'];
        $paymentMethods = ['Cash', 'Credit Card', 'Cheque', 'Bank Transfer'];

        foreach (range(1, 30) as $index) {
            $date = Carbon::parse($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $supplier = $suppliers->random();
            $totalAmount = rand(2000, 80000);
            $taxAmount = rand(0, (int) round($totalAmount * 0.1));
            $discountAmount = rand(0, (int) round($totalAmount * 0.1));
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $paidAmount = match ($paymentStatus) {
                'Paid' => $totalAmount,
                'Partial' => rand(0, $totalAmount),
                default => 0,
            };

            $dueAmount = $totalAmount - $paidAmount;

            $purchaseReturn = PurchaseReturn::create([
                'date' => $date->toDateString(),
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->supplier_name,
                'tax_percentage' => rand(0, 20),
                'tax_amount' => $taxAmount,
                'discount_percentage' => rand(0, 15),
                'discount_amount' => $discountAmount,
                'shipping_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $statuses[array_rand($statuses)],
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'note' => 'Seeded purchase return for testing purposes.',
            ]);

            if ($paidAmount > 0) {
                PurchaseReturnPayment::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'amount' => $paidAmount / 100,
                    'date' => $date->toDateString(),
                    'reference' => 'INV/'.str_pad((string) $purchaseReturn->id, 6, '0', STR_PAD_LEFT),
                    'payment_method' => $purchaseReturn->payment_method,
                    'note' => 'Seeded purchase return payment.',
                ]);
            }
        }

        Model::reguard();
    }
}
