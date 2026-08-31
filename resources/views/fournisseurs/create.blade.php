@extends('layouts.app')

@section('titre', __('app.fournisseurs.nouveau'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.fournisseurs.nouveau') }}</h1>
        <div class="actions"><a class="btn secondary small" href="{{ route('fournisseurs.index') }}"><span class="fl">←</span> {{ __('app.commun.retour') }}</a></div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('fournisseurs.store') }}">
            @csrf
            @include('fournisseurs._form')
            <div style="display:flex;gap:8px;margin-block-start:18px">
                <button type="submit" class="btn">{{ __('app.commun.enregistrer') }}</button>
                <a class="btn secondary" href="{{ route('fournisseurs.index') }}">{{ __('app.commun.annuler') }}</a>
            </div>
        </form>
    </div>
@endsection
