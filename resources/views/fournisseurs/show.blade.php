@extends('layouts.app')

@section('titre', $fournisseur->nom)

@section('content')
    <div class="page-head">
        <h1>{{ $fournisseur->nom }} <span class="badge info">{{ __("app.fournisseur_type.{$fournisseur->type->value}") }}</span></h1>
        <div class="actions">
            <a class="btn secondary small" href="{{ route('fournisseurs.edit', $fournisseur) }}">{{ __('app.commun.modifier') }}</a>
            <a class="btn secondary small" href="{{ route('fournisseurs.index') }}">← {{ __('app.commun.retour') }}</a>
        </div>
    </div>

    <div class="card">
        <dl class="dl">
            <div><dt>{{ __('app.commun.telephone') }}</dt><dd class="mono">{{ $fournisseur->telephone ?? '—' }}</dd></div>
            <div><dt>{{ __('app.commun.email') }}</dt><dd>{{ $fournisseur->email ?? '—' }}</dd></div>
            <div><dt>{{ __('app.commun.contact') }}</dt><dd>{{ $fournisseur->contact ?? '—' }}</dd></div>
            <div><dt>{{ __('app.commun.adresse') }}</dt><dd>{{ $fournisseur->adresse ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <h2>{{ __('app.fournisseurs.operations') }}</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.date') }}</th><th>{{ __('app.commun.dossier') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.commun.montant') }}</th></tr></thead>
                <tbody>
                @forelse ($operations as $frai)
                    <tr>
                        <td class="mono">{{ $frai->date_frais->format('d/m/Y') }}</td>
                        <td><a class="mono" href="{{ route('dossiers.show', $frai->dossier?->numero) }}">{{ $frai->dossier?->numero }}</a></td>
                        <td>{{ __("app.frais_categorie.{$frai->categorie->value}") }}</td>
                        <td class="mono">{{ $frai->montant }} {{ $frai->devise->value }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
