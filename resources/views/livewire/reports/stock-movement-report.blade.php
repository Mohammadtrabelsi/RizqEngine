<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control">
                                    @error('start_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control">
                                    @error('end_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select wire:model="product_id" class="form-control">
                                        <option value="">All Products</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select wire:model="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="in">Stock In</option>
                                        <option value="out">Stock Out</option>
                                        <option value="adjustment">Adjustment</option>
                                        <option value="opening">Opening</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                Filter Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-right">Change</th>
                                    <th class="text-right">Before</th>
                                    <th class="text-right">After</th>
                                    <th>Reference</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ optional($movement->product)->product_name ?? '—' }}</td>
                                        <td class="text-center">
                                            @if($movement->type === 'out')
                                                <span class="badge badge-danger">Out</span>
                                            @elseif($movement->type === 'in')
                                                <span class="badge badge-success">In</span>
                                            @elseif($movement->type === 'opening')
                                                <span class="badge badge-secondary">Opening</span>
                                            @else
                                                <span class="badge badge-info">Adjustment</span>
                                            @endif
                                        </td>
                                        <td class="text-right {{ $movement->signed_quantity < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $movement->signed_quantity > 0 ? '+' : '' }}{{ $movement->signed_quantity }}
                                        </td>
                                        <td class="text-right">{{ $movement->quantity_before }}</td>
                                        <td class="text-right">{{ $movement->quantity_after }}</td>
                                        <td>
                                            {{ $movement->reference_type }}{{ $movement->reference_id ? ' #' . $movement->reference_id : '' }}
                                            @if($movement->note)<div class="text-muted small">{{ $movement->note }}</div>@endif
                                        </td>
                                        <td>{{ optional($movement->user)->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No stock movements for the selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
