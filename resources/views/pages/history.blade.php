<x-layouts.app title="Order History" subtitle="All orders, newest first. Click an order to see what was ordered.">
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

    {{-- Order details popup --}}
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="omTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="omTitle"></h5>
                        <div class="small text-muted" id="omDate"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="omBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/history.js') }}"></script>
    @endpush
</x-layouts.app>