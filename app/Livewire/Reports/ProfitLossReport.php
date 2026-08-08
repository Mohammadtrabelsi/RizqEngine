<?php

namespace App\Livewire\Reports;

use App\Services\Reports\ProfitLossService;
use Livewire\Component;

class ProfitLossReport extends Component
{
    public $start_date;

    public $end_date;

    public $total_sales;

    public $sales_amount;

    public $total_purchases;

    public $purchases_amount;

    public $total_sale_returns;

    public $sale_returns_amount;

    public $total_purchase_returns;

    public $purchase_returns_amount;

    public $expenses_amount;

    public $profit_amount;

    public $payments_received_amount;

    public $payments_sent_amount;

    public $payments_net_amount;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date' => 'required|date|after:start_date',
    ];

    public function mount()
    {
        $this->start_date = '';
        $this->end_date = '';
        $this->total_sales = 0;
        $this->sales_amount = 0;
        $this->total_sale_returns = 0;
        $this->sale_returns_amount = 0;
        $this->total_purchases = 0;
        $this->purchases_amount = 0;
        $this->total_purchase_returns = 0;
        $this->purchase_returns_amount = 0;
        $this->payments_received_amount = 0;
        $this->payments_sent_amount = 0;
        $this->payments_net_amount = 0;
    }

    public function render(ProfitLossService $service)
    {
        foreach ($service->summary($this->start_date, $this->end_date) as $key => $value) {
            $this->{$key} = $value;
        }

        return view('livewire.reports.profit-loss-report');
    }

    public function generateReport()
    {
        $this->validate();
    }
}
