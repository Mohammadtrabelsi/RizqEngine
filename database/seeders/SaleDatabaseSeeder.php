<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SaleDatabaseSeeder extends Seeder
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

        $statuses = ['Pending', 'Shipped', 'Completed'];
        $paymentStatuses = ['Paid', 'Partial', 'Unpaid'];
        $paymentMethods = ['Cash', 'Credit Card', 'Cheque', 'Bank Transfer'];

        foreach (range(1, 100) as $index) {
            $date = Carbon::parse($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $customer = $customers->random();
            $totalAmount = rand(5000, 150000);
            $discountAmount = rand(0, (int) round($totalAmount * 0.1));
            $taxAmount = rand(0, (int) round($totalAmount * 0.1));
            $shippingAmount = rand(0, 10000);
            $subtotal = $totalAmount - $taxAmount + $discountAmount - $shippingAmount;
            $saleStatus = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $paidAmount = match ($paymentStatus) {
                'Paid' => $totalAmount,
                'Partial' => rand(0, $totalAmount),
                default => 0,
            };

            $dueAmount = $totalAmount - $paidAmount;

            Sale::create([
                'date' => $date->toDateString(),
                'customer_id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'tax_percentage' => rand(0, 20),
                'tax_amount' => $taxAmount,
                'discount_percentage' => rand(0, 15),
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $saleStatus,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'note' => 'Seeded sale for testing purposes.',
            ]);
        }
    }
}
