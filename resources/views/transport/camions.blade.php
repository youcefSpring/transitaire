@extends('layouts.app')

@section('titre', __('app.transport.camions'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.transport.camions') }}</h1>
        <div class="actions">
            <a class="btn secondary small" href="{{ route('chauffeurs.index') }}">{{ __('app.transport.chauffeurs') }}</a>
            <a class="btn secondary small" href="{{ route('livraisons.index') }}">{{ __('app.transport.livraisons') }}</a>
        </div>
    </div>

    <div class="card no-print">
        <h2>{{ __('app.transport.nouveau_camion') }}</h2>
        <form class="inline-form" method="POST" action="{{ route('camions.store') }}">
            @csrf
            <div class="field"><label>{{ __('app.transport.immatriculation') }} *</label><input name="immatriculation" required class="mono"></div>
            <div class="field" style="flex:1;min-width:200px"><label>{{ __('app.commun.notes') }}</label><input name="notes"></div>
            <button type="submit" class="btn small">＋</button>
        </form>
    </div>

    <div class="card">
        <x-filtres :action="route('camions.index')" :paginateur="$camions">
            <x-champ-recherche />
        </x-filtres>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.transport.immatriculation') }}</th><th>{{ __('app.commun.notes') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($camions as $camion)
                    <tr>
                        <td class="mono">{{ $camion->immatriculation }}</td>
                        <td>{{ $camion->notes }}</td>
                        <td>
                            <div class="row-actions no-print">
                                <form method="POST" action="{{ route('camions.destroy', $camion) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $camions->links() }}
    </div>
@endsection
