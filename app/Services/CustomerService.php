<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for customers, including the optional profile image
 * kept in the "images" media collection.
 */
class CustomerService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Customer::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('customer_name', 'like', $term)
                    ->orWhere('customer_email', 'like', $term)
                    ->orWhere('customer_phone', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function create(array $data, $image = null): Customer
    {
        $customer = Customer::create($data);
        $this->syncImage($customer, $image);

        return $customer;
    }

    public function update(int $id, array $data, $image = null): Customer
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        $this->syncImage($customer, $image);

        return $customer;
    }

    public function delete(int $id): void
    {
        Customer::findOrFail($id)->delete();
    }

    /**
     * Replace the customer's image with the freshly uploaded file, if any.
     */
    protected function syncImage(Customer $customer, $image): void
    {
        if (! $image) {
            return;
        }

        if ($customer->getFirstMedia('images')) {
            $customer->getFirstMedia('images')->delete();
        }

        $customer->addMedia($image->getRealPath())
            ->usingFileName($image->getClientOriginalName())
            ->toMediaCollection('images');
    }
}
