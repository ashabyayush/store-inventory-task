<x-layouts.app title="Customers" subtitle="Everyone who has placed an order.">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-md-5">
                    <label class="form-label" for="search">Search</label>
                    <input id="search" class="form-control" placeholder="Name or email">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="custTable"></table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/customers.js') }}"></script>
    @endpush
</x-layouts.app>