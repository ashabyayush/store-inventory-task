<x-layouts.app title="Low Stock" subtitle="Products below the threshold.">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-2 align-items-end mb-3">
                <div class="col-8 col-md-3">
                    <label class="form-label" for="threshold">Threshold</label>
                    <input id="threshold" type="number" min="1" class="form-control"
                           value="{{ config('inventory.low_stock_threshold') }}">
                </div>
                <div class="col-4 col-md-auto">
                    <button class="btn btn-primary w-100" id="apply" type="button">Apply</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="lowTable"></table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/low-stock.js') }}"></script>
    @endpush
</x-layouts.app>