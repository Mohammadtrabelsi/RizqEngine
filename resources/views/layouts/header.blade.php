<div class="container-fluid">
    <button class="c-header-toggler app-nav-toggler d-lg-none mfe-auto" type="button"
            data-toggle="collapse" data-target="#app-topnav-collapse"
            aria-controls="app-topnav-collapse" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list icon-fs-2rem"></i>
    </button>
    <ul class="c-header-nav mx-auto">

    @php($headerSettings = settings())
      <li class="c-header-nav-item mr-3">
  <a href="{{ route('home') }}" class="c-header-brand d-flex align-items-center mfs-3" style="text-decoration:none; gap:8px;">
        @if($headerSettings->client_logo || $headerSettings->client_name)
            @if($headerSettings->client_logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($headerSettings->client_logo) }}" alt="{{ $headerSettings->client_name ?? 'Logo' }}" style="max-height:32px; max-width:140px;">
            @endif
            @if($headerSettings->client_name)
                <span class="font-weight-bold d-md-down-none" style="font-size:1.05rem;">{{ $headerSettings->client_name }}</span>
            @endif
        @else
            @include('layouts.logo', ['size' => 24, 'label' => 'Triangle POS', 'labelSize' => 15])
        @endif
            </li>

    </a>

    <li class="c-header-nav-item m-3 d-flex align-items-center">
        @include('includes.language-switcher')
    </li>
    @can('create_pos_sales')
    <li class="c-header-nav-item mr-3">
        <a class="btn btn-primary btn-pill {{ request()->routeIs('app.pos.index') ? 'disabled' : '' }}" href="{{ route('app.pos.index') }}">
            <i class="bi bi-cart mr-1"></i> {{ __('app.pos_system') }}
        </a>
    </li>
    @endcan

    @can('show_notifications')
    <li class="c-header-nav-item dropdown d-md-down-none mr-2">
        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="bi bi-bell icon-fs-20px"></i>
            <span class="badge badge-pill badge-danger">{{ $low_quantity_products->count() }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg pt-0">
            <div class="dropdown-header bg-light">
                <strong>{{ $low_quantity_products->count() }} {{ __('app.notifications') }}</strong>
            </div>
            @forelse($low_quantity_products as $product)
                <a class="dropdown-item" href="{{ route('products.show', $product->id) }}">
                    <i class="bi bi-hash mr-1 text-primary"></i> Product: "{{ $product->product_code }}" is low in quantity!
                </a>
            @empty
                <a class="dropdown-item" href="#">
                    <i class="bi bi-app-indicator mr-2 text-danger"></i> {{ __('app.no-notifications') }}
                </a>
            @endforelse
        </div>
    </li>
    @endcan

    <li class="c-header-nav-item dropdown">
        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button"
           aria-haspopup="true" aria-expanded="false">
            <div class="c-avatar mr-2">
                <img class="c-avatar rounded-circle" src="{{ auth()->user()->getFirstMediaUrl('avatars') }}" alt="Profile Image">
            </div>
            <div class="d-flex flex-column">
                <span class="font-weight-bold">{{ auth()->user()->name }}</span>
                <span class="font-italic">Online <i class="bi bi-circle-fill text-success icon-fs-11px"></i></span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right pt-0">
            <div class="dropdown-header bg-light py-2"><strong>{{ __('app.account') }}</strong></div>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="mfe-2  bi bi-person icon-fs-1-2rem"></i> Profile
            </a>
            <a id="logout-link" class="dropdown-item" href="#">
                <i class="mfe-2  bi bi-box-arrow-left icon-fs-1-2rem"></i> {{ __('app.logout') }}   
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </li>
</ul>
</div>
