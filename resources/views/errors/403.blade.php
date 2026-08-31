@extends('layouts.app')

@section('titre', __('app.messages.acces_refuse'))

@section('content')
    <div class="card" style="border-color:var(--danger)">
        <h1 style="color:var(--danger)">⛔ {{ __('app.messages.acces_refuse') }}</h1>
        @if (auth()->check())
            <p class="muted">{{ auth()->user()->name }} — {{ __("app.profil.".auth()->user()->profile->value) }}</p>
        @endif
        <a class="btn secondary small" href="{{ route('dashboard.index') }}"><span class="fl">←</span> {{ __('app.nav.dashboard') }}</a>
    </div>
@endsection
