@extends('layouts.app')

@section('titre', __('app.users.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.users.titre') }}</h1>
        <div class="actions"><a class="btn" href="{{ route('users.create') }}">＋ {{ __('app.users.nouveau') }}</a></div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.nom') }}</th><th>{{ __('app.commun.email') }}</th><th>{{ __('app.users.profil') }}</th><th>{{ __('app.users.actif') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge info">{{ __("app.profil.{$user->profile->value}") }}</span></td>
                        <td><span class="badge {{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? '✓' : '✕' }}</span></td>
                        <td>
                            <div class="row-actions no-print">
                                <a class="btn secondary small" href="{{ route('users.edit', $user) }}">{{ __('app.commun.modifier') }}</a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
@endsection
