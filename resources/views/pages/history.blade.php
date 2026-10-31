<x-layouts.app title="Order History" subtitle="All orders, newest first. Filter by customer email to narrow down.">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md">
                    <label class="form-label" for="hemail">Customer email</label>
                    <input id="hemail" type="text" class="form-control" placeholder="e.g. asha@example.com">
                </div>
                <div class="col-6 col-md-auto">
                    <button class="btn btn-primary w-100" id="lookup" type="button">Look up</button>
                </div>
                <div class="col-6 col-md-auto">
                    <button class="btn btn-outline-secondary w-100" id="clear" type="button">Clear</button>
                </div>
            </div>

            <p class="text-muted" id="summary"></p>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="ordTable"></table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3" id="pager"></div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/history.js') }}"></script>
    @endpush
</x-layouts.app>