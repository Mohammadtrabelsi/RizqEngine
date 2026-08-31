<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Owns all persistence for customers, including the optional profile image
 * kept in the "images" media collection.
 */
class CustomerService
{
    /**
     * Every customer, for populating select controls (e.g. the POS screen).
     *
     * @return Collection<int, Customer>
     */
    public function all(): Collection
    {
        return Customer::all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(?string $search = null, int $perPage = 12, array $filters = []): LengthAwarePaginator
    {
        $city = $filters['city'] ?? '';
        $country = $filters['country'] ?? '';
        $hasTaxId = $filters['hasTaxId'] ?? '';

        return Customer::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('customer_name', 'like', $term)
                        ->orWhere('customer_email', 'like', $term)
                        ->orWhere('customer_phone', 'like', $term)
                        ->orWhere('tax_identification_number', 'like', $term);
                });
            })
            ->when($city !== '', function ($query) use ($city) {
                $query->where('city', $city);
            })
            ->when($country !== '', function ($query) use ($country) {
                $query->where('country', $country);
            })
            ->when($hasTaxId === 'yes', function ($query) {
                $query->whereNotNull('tax_identification_number')
                    ->where('tax_identification_number', '!=', '');
            })
            ->when($hasTaxId === 'no', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('tax_identification_number')
                        ->orWhere('tax_identification_number', '=', '');
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Distinct non-empty cities across all customers.
     *
     * @return Collection<int, string>
     */
    public function cities()
    {
        return Customer::query()
            ->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->orderBy('city')->pluck('city');
    }

    /**
     * Distinct non-empty countries across all customers.
     *
     * @return Collection<int, string>
     */
    public function countries()
    {
        return Customer::query()
            ->whereNotNull('country')->where('country', '!=', '')
            ->distinct()->orderBy('country')->pluck('country');
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
