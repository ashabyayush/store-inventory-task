<x-layouts.app title="Configuration" subtitle="Currency and your store login details.">
    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Currency</div>
                <div class="card-body">
                    <label class="form-label" for="currency">Price currency</label>
                    <select id="currency" class="form-select"></select>
                    <div class="form-text">
                        Only the symbol shown across the app changes. Existing amounts are not converted.
                    </div>
                    <button class="btn btn-primary mt-3" id="saveCurrency" type="button">Save currency</button>
                    <div id="curMsg"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Store login</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="aname">Username (name)</label>
                        <input id="aname" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="aemail">Login email</label>
                        <input id="aemail" type="email" class="form-control">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="apass">New password</label>
                            <input id="apass" type="password" class="form-control" autocomplete="new-password">
                            <div class="form-text">Leave blank to keep the current one.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="apass2">Confirm new password</label>
                            <input id="apass2" type="password" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="acurrent">Current password <span class="text-danger">*</span></label>
                        <input id="acurrent" type="password" class="form-control" autocomplete="current-password">
                        <div class="form-text">Required to save any change.</div>
                    </div>
                    <button class="btn btn-primary" id="saveAccount" type="button">Save login details</button>
                    <div id="accMsg"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/settings.js') }}"></script>
    @endpush
</x-layouts.app>