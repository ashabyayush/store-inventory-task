@props(['title', 'subtitle' => null])
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Store Counter</title>

    {{-- Currency symbol for JS (set in Configuration) --}}
    <script>window.APP = @json(['currency' => \App\Models\Setting::currency()]);</script>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="d-lg-flex">
        <x-sidebar />

        <div class="app-main flex-grow-1">
            {{-- Mobile / tablet top bar --}}
            <header class="d-lg-none sticky-top d-flex align-items-center gap-3 px-3 py-2 bg-white border-bottom">
                <button class="btn btn-outline-secondary" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Open menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="brand mb-0">
                    <div class="logo">S</div>
                    <b>Store Counter</b>
                </div>
            </header>

            <main class="p-3 p-md-4">
                <div class="container-fluid px-0">
                    <h1 class="h3 mb-1">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="text-muted mb-4">{{ $subtitle }}</p>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Shared popup (messages + confirmations) --}}
    <div class="modal fade" id="appModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="appModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="appModalCancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="appModalOk">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>