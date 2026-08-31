@extends('layouts.app')

@section('titre', __('app.clients.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.clients.titre') }}</h1>
        <div class="actions">
            @can('clients.gerer')
                <a class="btn" href="{{ route('clients.create') }}">＋ {{ __('app.clients.nouveau') }}</a>
            @endcan
        </div>
    </div>

    <div class="card">
        <x-filtres :action="route('clients.index')" :paginateur="$clients">
            <x-champ-recherche />
        </x-filtres>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>{{ __('app.clients.raison_sociale') }}</th>
                    <th>{{ __('app.clients.nif') }}</th>
                    <th>{{ __('app.commun.telephone') }}</th>
                    <th>{{ __('app.commun.email') }}</th>
                    <th>{{ __('app.clients.reste_a_payer') }} (DZD)</th>
                    <th class="amount">{{ __('app.commun.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($clients as $client)
                    <tr>
                        <td><a href="{{ route('clients.show', $client) }}">{{ $client->raison_sociale }}</a></td>
                        <td class="mono">{{ $client->nif }}</td>
                        <td class="mono">{{ $client->telephone }}</td>
                        <td>{{ $client->email }}</td>
                        <td class="amount">{{ number_format($client->solde['dzd']['reste_a_payer'] ?? 0, 2, ',', ' ') }}</td>
                        <td>
                            <div class="row-actions">
                                @can('clients.gerer')
                                    <a class="btn secondary small" href="{{ route('clients.edit', $client) }}">{{ __('app.commun.modifier') }}</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $clients->links() }}
    </div>
@endsection
