<?php

namespace App\Livewire\Currencies;

use App\Models\Currency;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_currencies'), 403);

        Currency::findOrFail($id)->delete();

        session()->flash('warning', trans('currency.currency-deleted'));
    }

    public function render()
    {
        $currencies = Currency::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('currency_name', 'like', $term)
                    ->orWhere('code', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.currencies.currency-index', compact('currencies'));
    }
}
