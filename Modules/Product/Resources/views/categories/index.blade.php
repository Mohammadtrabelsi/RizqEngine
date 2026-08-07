@extends('layouts.app')

@section('title', 'Product Categories')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Categories</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="row">
            <div class="col-12 mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryCreateModal">
                    Add Category <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>

        <div class="row">
            @forelse($categories as $category)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">{{ $category->category_name }}</h5>
                            <p class="text-muted mb-2"><small>{{ $category->category_code }}</small></p>
                            <span class="badge bg-info mb-3">{{ $category->products_count }} products</span>
                            <div class="btn-group d-block">
                                @include('product::categories.partials.actions', ['data' => $category])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No categories found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    @include('product::includes.category-modal')
@endsection
