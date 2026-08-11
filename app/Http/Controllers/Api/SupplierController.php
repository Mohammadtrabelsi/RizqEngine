<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see SupplierService}. The optional profile image is
 * accepted as a multipart "image" file on create/update.
 */
class SupplierController extends Controller
{
    public function __construct(private readonly SupplierService $suppliers) {}

    /**
     * {@see SupplierService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->suppliers->paginate($request->query('search'), (int) $request->query('per_page', 12))
        );
    }

    /**
     * {@see SupplierService::findOrFail()}
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->suppliers->findOrFail($id));
    }

    /**
     * {@see SupplierService::create()}
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        $supplier = $this->suppliers->create($data, $request->file('image'));

        return response()->json($supplier, 201);
    }

    /**
     * {@see SupplierService::update()}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $this->validated($request);
        unset($data['image']);

        $supplier = $this->suppliers->update($id, $data, $request->file('image'));

        return response()->json($supplier);
    }

    /**
     * {@see SupplierService::delete()}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->suppliers->delete($id);

        return response()->json(['deleted' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'required|max:255',
            'supplier_email' => 'required|email|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
        ]);
    }
}
