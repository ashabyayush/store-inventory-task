<x-layouts.app title="Products" subtitle="Catalog and current stock.">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Add product</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label" for="pname">Name</label>
                    <input id="pname" class="form-control">
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label" for="pcode">Code (unique)</label>
                    <input id="pcode" class="form-control">
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label" for="pcat">Category</label>
                    <select id="pcat" class="form-select"></select>
                </div>
                <div class="col-6 col-lg-4">
                    <label class="form-label" for="pprice">Price</label>
                    <input id="pprice" type="number" min="0" step="0.01" class="form-control">
                </div>
                <div class="col-6 col-lg-4">
                    <label class="form-label" for="ptax">Tax %</label>
                    <input id="ptax" type="number" min="0" max="100" step="0.01" value="0" class="form-control">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="pstock">Stock</label>
                    <input id="pstock" type="number" min="0" value="0" class="form-control">
                </div>
            </div>

            <div class="d-grid d-md-block mt-3">
                <button class="btn btn-primary px-4" id="saveProduct" type="button">Save product</button>
            </div>
            <div id="pMsg"></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="filter">Filter by category</label>
                    <select id="filter" class="form-select"></select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="prodTable"></table>
            </div>
        </div>
    </div>

    {{-- Edit modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eid">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="ename">Name</label>
                            <input id="ename" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="ecode">Code (unique)</label>
                            <input id="ecode" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="ecat">Category</label>
                            <select id="ecat" class="form-select"></select>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="eprice">Price</label>
                            <input id="eprice" type="number" min="0" step="0.01" class="form-control">
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="etax">Tax %</label>
                            <input id="etax" type="number" min="0" max="100" step="0.01" class="form-control">
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="estock">Stock</label>
                            <input id="estock" type="number" min="0" class="form-control">
                        </div>
                    </div>
                    <div id="eMsg"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEdit">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/products.js') }}"></script>
    @endpush
</x-layouts.app>