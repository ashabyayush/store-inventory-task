@php
    $icons = [
        'bag'     => '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        'trend'   => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
        'package' => '<path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'tag'     => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
        'users'   => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'alert'   => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
    ];

    $cards = [
        ['label' => 'Orders today',  'value' => $stats['orders_today'],                                 'icon' => 'bag',     'tone' => 'blue'],
        ['label' => 'Revenue today', 'value' => $symbol . number_format($stats['revenue_today'], 2),   'icon' => 'trend',   'tone' => 'green'],
        ['label' => 'Products',      'value' => $stats['products'],                                     'icon' => 'package', 'tone' => 'purple'],
        ['label' => 'Categories',    'value' => $stats['categories'],                                   'icon' => 'tag',     'tone' => 'orange'],
        ['label' => 'Customers',     'value' => $stats['customers'],                                    'icon' => 'users',   'tone' => 'teal'],
        ['label' => 'Low stock',     'value' => $stats['low_stock'],                                    'icon' => 'alert',   'tone' => $stats['low_stock'] ? 'red' : 'slate'],
    ];
@endphp

<x-layouts.app title="Dashboard" subtitle="Today's activity at a glance.">
    <div class="row g-3 mb-4">
        @foreach ($cards as $c)
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="stat-card tone-{{ $c['tone'] }}">
                    <div class="stat-text">
                        <div class="stat-label">{{ $c['label'] }}</div>
                        <div class="stat-value">{{ $c['value'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            {!! $icons[$c['icon']] !!}
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white fw-bold">Recent orders</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Order</th><th>Customer</th><th>When</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                @forelse ($recentOrders as $o)
                    <tr>
                        <td class="fw-bold">{{ $o->order_number }}</td>
                        <td>{{ $o->customer->name }}</td>
                        <td class="text-nowrap">{{ $o->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-end">{{ $symbol }}{{ number_format($o->grand_total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">No orders yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white fw-bold">Low stock (below {{ $threshold }})</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Code</th><th>Category</th><th class="text-end">Stock</th></tr>
                </thead>
                <tbody>
                @forelse ($lowStock as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td><code>{{ $p->code }}</code></td>
                        <td>{{ $p->category->name }}</td>
                        <td class="text-end low">{{ $p->stock }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Everything is well stocked 🎉</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>