<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\SaleReturn;
use App\Models\SaleReturnPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SalesReturnDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $customers = Customer::all();

        if ($customers->isEmpty()) {
            return;
        }

        $startDate = Carbon::today()->subMonths(3);
        $endDate = Carbon::today();

        $statuses = ['Pending', 'Completed'];
        $paymentStatuses = ['Paid', 'Partial', 'Unpaid'];
        $paymentMethods = ['Cash', 'Credit Card', 'Cheque', 'Bank Transfer'];

        foreach (range(1, 40) as $index) {
            $date = Carbon::parse($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $customer = $customers->random();
            $totalAmount = rand(2000, 60000);
            $taxAmount = rand(0, (int) round($totalAmount * 0.1));
            $discountAmount = rand(0, (int) round($totalAmount * 0.1));
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $paidAmount = match ($paymentStatus) {
                'Paid' => $totalAmount,
                'Partial' => rand(0, $totalAmount),
                default => 0,
            };

            $dueAmount = $totalAmount - $paidAmount;

            $saleReturn = SaleReturn::create([
                'date' => $date->toDateString(),
                'customer_id' => $customer->id,
                'customer_name' => $customer->customer_name,
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
                'note' => 'Seeded sale return for testing purposes.',
            ]);

            if ($paidAmount > 0) {
                SaleReturnPayment::create([
                    'sale_return_id' => $saleReturn->id,
                    'amount' => $paidAmount / 100,
                    'date' => $date->toDateString(),
                    'reference' => 'INV/'.str_pad((string) $saleReturn->id, 6, '0', STR_PAD_LEFT),
                    'payment_method' => $saleReturn->payment_method,
                    'note' => 'Seeded sale return payment.',
                ]);
            }
        }

        Model::reguard();
    }
}
