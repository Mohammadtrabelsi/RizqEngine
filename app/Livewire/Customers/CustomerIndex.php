<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_customers'), 403);

        Customer::findOrFail($id)->delete();

        session()->flash('warning', trans('people.customer-deleted'));
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('customer_name', 'like', $term)
                    ->orWhere('customer_email', 'like', $term)
                    ->orWhere('customer_phone', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.customers.customer-index', compact('customers'));
    }
}
