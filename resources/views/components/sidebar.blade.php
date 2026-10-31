@php
    $items = [
        ['route' => 'dashboard',        'label' => 'Dashboard',     'icon' => 'bi-house'],
        ['route' => 'orders.create',    'label' => 'New Order',     'icon' => 'bi-plus-lg'],
        ['route' => 'orders.history',   'label' => 'Order History', 'icon' => 'bi-clock-history'],
        ['route' => 'customers.index',  'label' => 'Customers',     'icon' => 'bi-people'],
        ['route' => 'products.index',   'label' => 'Products',      'icon' => 'bi-box-seam'],
        ['route' => 'categories.index', 'label' => 'Categories',    'icon' => 'bi-tags'],
        ['route' => 'products.low',     'label' => 'Low Stock',     'icon' => 'bi-exclamation-triangle'],
        ['route' => 'settings.index',   'label' => 'Configuration', 'icon' => 'bi-gear'],
    ];
@endphp

<div class="sidebar-col">
    <aside class="sidebar offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-label="Main menu">
        <div class="offcanvas-header">
            <span class="fw-bold">Menu</span>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <div class="brand">
                <div class="logo">S</div>
                <div>
                    <b class="d-block">Store Counter</b>
                    <small class="side-sub">Orders &amp; Inventory</small>
                </div>
            </div>

            <div class="menu-label">MENU</div>

            <nav class="nav flex-column gap-1">
                @foreach ($items as $item)
                    <a href="{{ route($item['route']) }}"
                       class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="side-foot mt-auto">
                <div class="small mb-2">Signed in as <b>{{ auth()->user()->name }}</b></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                   <button type="submit" class="btn btn-link text-danger fw-bold p-0 text-decoration-none">
    <i class="bi bi-box-arrow-right"></i> Log out
</button>
                </form>
                <div class="small side-sub mt-2">Prices in {{ \App\Models\Setting::currency()['code'] }}</div>
            </div>
        </div>
    </aside>
</div>