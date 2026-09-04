<?php

namespace App\Livewire\CashRegister;

use App\Models\CashRegisterSession;
use App\Services\CashRegisterService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class CashRegisterIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public float $opening_float = 0;

    public float $counted_amount = 0;

    public string $note = '';

    public function open(CashRegisterService $register): void
    {
        abort_if(Gate::denies('open_cash_register'), 403);

        $this->validate(['opening_float' => 'required|numeric|min:0']);

        try {
            $register->open(auth()->user(), (int) round($this->opening_float * 100), null, $this->note ?: null);
        } catch (\RuntimeException $e) {
            $this->addError('opening_float', $e->getMessage());

            return;
        }

        $this->reset('opening_float', 'note');
        session()->flash('success', __('cash_register.opened'));
    }

    public function close(CashRegisterService $register): void
    {
        abort_if(Gate::denies('close_cash_register'), 403);

        $session = $register->currentFor(auth()->user());

        if ($session === null) {
            $this->addError('counted_amount', __('cash_register.no_open_session'));

            return;
        }

        $this->validate(['counted_amount' => 'required|numeric|min:0']);

        $register->close($session, (int) round($this->counted_amount * 100), $this->note ?: null);

        $this->reset('counted_amount', 'note');
        session()->flash('success', __('cash_register.closed_flash'));
    }

    public function render(CashRegisterService $register)
    {
        abort_if(Gate::denies('access_cash_register'), 403);

        $current = $register->currentFor(auth()->user());

        return view('livewire.cash-register.cash-register-index', [
            'current' => $current,
            'expected' => $current ? $register->expectedCash($current) : 0,
            'cashSales' => $current ? $register->cashSales($current) : 0,
            'sessions' => CashRegisterSession::with('user')->latest('opened_at')->paginate(10),
        ])->layout('components.layouts.admin', ['title' => __('cash_register.cash_register')]);
    }
}
