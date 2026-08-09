<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class CustomersController extends Controller
{
    public function __construct(private readonly CustomerService $customers) {}

    public function index()
    {
        abort_if(Gate::denies('access_customers'), 403);

        return view('people.customers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_customers'), 403);

        return view('people.customers.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_customers'), 403);

        $data = $this->validated($request);

        $this->customers->create($data, $request->file('image'));

        session()->flash('success', trans('people.customer-created'));

        return redirect()->route('customers.index');
    }

    public function show(Customer $customer)
    {
        abort_if(Gate::denies('show_customers'), 403);

        return view('people.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        abort_if(Gate::denies('edit_customers'), 403);

        return view('people.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if(Gate::denies('update_customers'), 403);

        $data = $this->validated($request);

        $this->customers->update($customer->id, $data, $request->file('image'));

        session()->flash('info', trans('people.customer-updated'));

        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        abort_if(Gate::denies('delete_customers'), 403);

        $this->customers->delete($customer->id);

        session()->flash('warning', trans('people.customer-deleted'));

        return redirect()->route('customers.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
            'customer_email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);
    }
}
