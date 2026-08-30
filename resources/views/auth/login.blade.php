<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.titre') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <span class="logo" style="width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,var(--primary),#7c3aed);display:grid;place-items:center;color:#fff;font-weight:700">TR</span>
            <div>
                <strong style="font-size:1.05rem">{{ __('app.nom') }}</strong>
                <div class="muted" style="font-size:0.8rem">{{ __('app.auth.titre') }}</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin:0;padding-inline-start:18px">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <div class="field">
                <label for="email">{{ __('app.auth.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">{{ __('app.auth.mot_de_passe') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn" style="justify-content:center">{{ __('app.auth.connexion') }}</button>
        </form>
    </div>
</div>
</body>
</html>
