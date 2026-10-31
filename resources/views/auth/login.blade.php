<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Store Counter</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-page">
<div class="login-shell">

    {{-- LEFT: form --}}
    <section class="login-left">
        <div class="login-brand">
            <span class="login-logo"><i class="bi bi-shop">S</i></span>
            <span>Store Counter</span>
        </div>

        <div class="login-main">
            <div class="login-box">
                <h1>Welcome back</h1>
                <p class="login-sub">Sign in to record orders and keep your stock in sync.</p>

                @if (session('status'))
                    <div class="lp-alert lp-alert-warn" role="alert">
                        <i class="bi bi-info-circle"></i> {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" id="loginForm" novalidate>
                    @csrf

                    <div class="lp-field">
                        <label for="email">Email address</label>
                        <div class="lp-input-wrap">
                            <i class="bi bi-envelope lp-ico"></i>
                            <input id="email" name="email" type="email" required autofocus
                                   autocomplete="username" placeholder="you@store.com"
                                   class="lp-input {{ $errors->has('email') ? 'lp-error' : '' }}"
                                   value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="lp-field">
                        <label for="password">Password</label>
                        <div class="lp-input-wrap">
                            <i class="bi bi-lock lp-ico"></i>
                            <input id="password" name="password" type="password" required
                                   autocomplete="current-password" placeholder="Enter your password"
                                   class="lp-input {{ $errors->has('email') ? 'lp-error' : '' }}">
                            <button type="button" class="lp-toggle" id="togglePass" aria-label="Show password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    @error('email')
                        <div class="lp-alert lp-alert-err" role="alert">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror

                    <label class="lp-check">
                        <input type="checkbox" name="remember"> <span>Remember me</span>
                    </label>

                    <button type="submit" class="lp-btn" id="loginBtn">
                        <span>Sign in</span><span class="lp-spinner" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>

        <p class="login-foot">© {{ date('Y') }} Store Counter</p>
    </section>

    {{-- RIGHT: showcase --}}
    <aside class="login-right" aria-hidden="true">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div class="grid-dots"></div>

        <div class="showcase">
            <h2>Every order.<br>Every unit.<br><span>Always in sync.</span></h2>
          <p>Manage daily sales, monitor inventory and keep your stock updated with every transaction.</p>
            <div class="cards">
                <div class="g-card c-order">
                    <div class="g-ico ok"><i class="bi bi-check-lg"></i></div>
                    <div class="g-body">
                        <div class="g-title">ORD-000124 <span class="g-pill">emailed</span></div>
                        <div class="g-sub">Gel Pen ×3 · Assam Tea ×2</div>
                    </div>
                    <div class="g-amt">928.80</div>
                </div>

                <div class="g-card c-stock">
                    <div class="g-ico warn"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="g-body">
                        <div class="g-title">Notebook A5</div>
                        <div class="g-sub">Only 3 left in stock</div>
                        <div class="bar"><i></i></div>
                    </div>
                </div>

                <div class="g-card c-chart">
                    <div class="g-ico"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="g-body">
                        <div class="g-title">Orders today</div>
                        <div class="g-sub">Updated live</div>
                    </div>
                    <div class="mini-bars">
                        <i style="height:30%"></i><i style="height:55%"></i><i style="height:40%"></i>
                        <i style="height:75%"></i><i style="height:60%"></i><i style="height:95%"></i>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
(function () {
  var pass = document.getElementById('password');
  var toggle = document.getElementById('togglePass');
  var form = document.getElementById('loginForm');
  var btn = document.getElementById('loginBtn');

  toggle.addEventListener('click', function () {
    var show = pass.type === 'password';
    pass.type = show ? 'text' : 'password';
    toggle.firstElementChild.className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });

  form.addEventListener('submit', function () {
    btn.classList.add('loading');
    setTimeout(function () { btn.disabled = true; }, 0);
  });

  // Back button-la cached login page vandha refresh pannu (fresh CSRF token ku)
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) { window.location.reload(); }
  });
})();
</script>
</body>
</html>