<x-layouts.app title="Categories" subtitle="Group your products. A category with products cannot be deleted.">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md">
                    <label class="form-label" for="cname">Category name</label>
                    <input id="cname" class="form-control" placeholder="e.g. Beverages">
                </div>
                <div class="col-12 col-md-auto">
                    <button class="btn btn-primary w-100" id="addCat" type="button">
                        <i class="bi bi-plus-lg"></i> Add category
                    </button>
                </div>
            </div>
            <div id="cMsg"></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="catTable"></table>
        </div>
    </div>

    {{-- Edit modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eid">
                    <label class="form-label" for="ename">Category name</label>
                    <input id="ename" class="form-control">
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
        <script src="{{ asset('js/pages/categories.js') }}"></script>
    @endpush
</x-layouts.app>