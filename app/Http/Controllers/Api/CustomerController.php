<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see CustomerService}. The optional profile image is
 * accepted as a multipart "image" file on create/update.
 */
class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customers) {}

    /**
     * {@see CustomerService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->customers->paginate($request->query('search'), (int) $request->query('per_page', 12))
        );
    }

    /**
     * {@see CustomerService::findOrFail()}
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->customers->findOrFail($id));
    }

    /**
     * {@see CustomerService::create()}
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        $customer = $this->customers->create($data, $request->file('image'));

        return response()->json($customer, 201);
    }

    /**
     * {@see CustomerService::update()}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        $customer = $this->customers->update($id, $data, $request->file('image'));

        return response()->json($customer);
    }

    /**
     * {@see CustomerService::delete()}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->customers->delete($id);

        return response()->json(['deleted' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
            'customer_email' => 'required|email|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
        ]);
    }
}
