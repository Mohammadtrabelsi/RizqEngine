<?php

namespace App\Livewire\Customers;

use App\Services\CustomerService;
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

    public function mount(): void
    {
        abort_if(Gate::denies('access_customers'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, CustomerService $customers): void
    {
        abort_if(Gate::denies('delete_customers'), 403);

        $customers->delete($id);

        session()->flash('warning', trans('people.customer-deleted'));
    }

    public function render(CustomerService $customers)
    {
        return view('livewire.customers.customer-index', [
            'customers' => $customers->paginate($this->search),
        ]);
    }
}
