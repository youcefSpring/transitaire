@extends('layouts.app')

@section('titre', __('app.clients.nouveau'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.clients.nouveau') }}</h1>
        <div class="actions"><a class="btn secondary small" href="{{ route('clients.index') }}">← {{ __('app.commun.retour') }}</a></div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('clients.store') }}">
            @csrf
            @include('clients._form')
            <div style="display:flex;gap:8px;margin-block-start:18px">
                <button type="submit" class="btn">{{ __('app.commun.enregistrer') }}</button>
                <a class="btn secondary" href="{{ route('clients.index') }}">{{ __('app.commun.annuler') }}</a>
            </div>
        </form>
    </div>
@endsection
