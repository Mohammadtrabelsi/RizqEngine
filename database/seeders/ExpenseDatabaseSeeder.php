<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ExpenseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // A handful of expense categories, each with a few expenses.
        ExpenseCategory::factory()
            ->count(5)
            ->has(Expense::factory()->count(4), 'expenses')
            ->create();

        Model::reguard();
    }
}
