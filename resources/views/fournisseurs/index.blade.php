@extends('layouts.app')

@section('titre', __('app.fournisseurs.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.fournisseurs.titre') }}</h1>
        <div class="actions"><a class="btn" href="{{ route('fournisseurs.create') }}">＋ {{ __('app.fournisseurs.nouveau') }}</a></div>
    </div>

    <div class="card">
        <x-filtres :action="route('fournisseurs.index')" :paginateur="$fournisseurs">
            <x-champ-recherche />
            <div class="field">
                <label>{{ __('app.commun.type') }}</label>
                <select name="type">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    @foreach (\App\Enums\FournisseurType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ __("app.fournisseur_type.{$type->value}") }}</option>
                    @endforeach
                </select>
            </div>
        </x-filtres>

        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.nom') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.commun.telephone') }}</th><th>{{ __('app.commun.email') }}</th><th>{{ __('app.commun.contact') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($fournisseurs as $fournisseur)
                    <tr>
                        <td><a href="{{ route('fournisseurs.show', $fournisseur) }}">{{ $fournisseur->nom }}</a></td>
                        <td><span class="badge info">{{ __("app.fournisseur_type.{$fournisseur->type->value}") }}</span></td>
                        <td class="mono">{{ $fournisseur->telephone }}</td>
                        <td>{{ $fournisseur->email }}</td>
                        <td>{{ $fournisseur->contact }}</td>
                        <td>
                            <div class="row-actions no-print">
                                <a class="btn secondary small" href="{{ route('fournisseurs.edit', $fournisseur) }}">{{ __('app.commun.modifier') }}</a>
                                <form method="POST" action="{{ route('fournisseurs.destroy', $fournisseur) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $fournisseurs->links() }}
    </div>
@endsection
