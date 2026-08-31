@extends('layouts.app')

@section('titre', __('app.transport.chauffeurs'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.transport.chauffeurs') }}</h1>
        <div class="actions">
            <a class="btn secondary small" href="{{ route('camions.index') }}">{{ __('app.transport.camions') }}</a>
            <a class="btn secondary small" href="{{ route('livraisons.index') }}">{{ __('app.transport.livraisons') }}</a>
        </div>
    </div>

    @can('transport.gerer')
    <div class="card no-print">
        <h2>{{ __('app.transport.nouveau_chauffeur') }}</h2>
        <form class="inline-form" method="POST" action="{{ route('chauffeurs.store') }}">
            @csrf
            <div class="field"><label>{{ __('app.commun.nom') }} *</label><input name="nom" required></div>
            <div class="field"><label>{{ __('app.commun.telephone') }} *</label><input name="telephone" required class="mono"></div>
            <button type="submit" class="btn small">＋</button>
        </form>
    </div>
    @endcan

    <div class="card">
        <x-filtres :action="route('chauffeurs.index')" :paginateur="$chauffeurs">
            <x-champ-recherche />
        </x-filtres>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.nom') }}</th><th>{{ __('app.commun.telephone') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($chauffeurs as $chauffeur)
                    <tr>
                        <td>{{ $chauffeur->nom }}</td>
                        <td class="mono">{{ $chauffeur->telephone }}</td>
                        <td>
                            <div class="row-actions no-print">
                                @can('transport.gerer')
                                    <form method="POST" action="{{ route('chauffeurs.destroy', $chauffeur) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn danger small">✕</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $chauffeurs->links() }}
    </div>
@endsection
