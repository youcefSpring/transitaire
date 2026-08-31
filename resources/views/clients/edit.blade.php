@extends('layouts.app')

@section('titre', __('app.clients.modifier_titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.clients.modifier_titre') }} — {{ $client->raison_sociale }}</h1>
        <div class="actions"><a class="btn secondary small" href="{{ route('clients.show', $client) }}"><span class="fl">←</span> {{ __('app.commun.retour') }}</a></div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('clients.update', $client) }}">
            @csrf
            @method('PUT')
            @include('clients._form')
            <div style="display:flex;gap:8px;margin-block-start:18px">
                <button type="submit" class="btn">{{ __('app.commun.enregistrer') }}</button>
                <a class="btn secondary" href="{{ route('clients.show', $client) }}">{{ __('app.commun.annuler') }}</a>
            </div>
        </form>
    </div>
@endsection
