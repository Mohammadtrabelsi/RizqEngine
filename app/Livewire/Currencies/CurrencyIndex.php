<?php

namespace App\Livewire\Currencies;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CurrencyIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_currencies'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, CurrencyService $currencies): void
    {
        abort_if(Gate::denies('delete_currencies'), 403);

        $currencies->delete($id);

        session()->flash('warning', trans('currency.currency-deleted'));
    }

    public function render(CurrencyService $currencies)
    {
        return view('livewire.currencies.currency-index', [
            'currencies' => $currencies->paginate($this->search),
        ]);
    }
}
