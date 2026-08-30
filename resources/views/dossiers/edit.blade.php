@extends('layouts.app')

@section('titre', __('app.dossiers.modifier_titre').' — '.$dossier->numero)

@section('content')
    <div class="page-head">
        <h1>{{ __('app.dossiers.modifier_titre') }} — <span class="mono">{{ $dossier->numero }}</span></h1>
        <div class="actions"><a class="btn secondary small" href="{{ route('dossiers.show', $dossier->numero) }}">← {{ __('app.commun.retour') }}</a></div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('dossiers.update', $dossier->numero) }}">
            @csrf
            @method('PUT')
            @include('dossiers._form')
            <div style="display:flex;gap:8px;margin-block-start:18px">
                <button type="submit" class="btn">{{ __('app.commun.enregistrer') }}</button>
                <a class="btn secondary" href="{{ route('dossiers.show', $dossier->numero) }}">{{ __('app.commun.annuler') }}</a>
            </div>
        </form>
    </div>
@endsection
