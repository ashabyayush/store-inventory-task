<x-layouts.app title="New Order" subtitle="Record a customer order. Stock is deducted automatically.">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="cname">Customer name</label>
                    <input id="cname" class="form-control" placeholder="Asha Raman">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="cemail">Customer email</label>
                    <input id="cemail" type="email" class="form-control" placeholder="asha@example.com">
                </div>
            </div>

            <hr>

            <div id="lines"></div>

            <button class="btn btn-outline-primary btn-sm" id="addLine" type="button">
                <i class="bi bi-plus-lg"></i> Add line
            </button>

            <div class="text-end text-muted my-3" id="preview"></div>

            <div class="d-grid d-md-block">
                <button class="btn btn-primary px-4" id="placeOrder" type="button">Place order</button>
            </div>
            <div id="orderMsg"></div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/new-order.js') }}"></script>
    @endpush
</x-layouts.app>