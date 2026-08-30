@extends('layouts.app')

@section('titre', __('app.users.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.users.titre') }}</h1>
        <div class="actions"><a class="btn" href="{{ route('users.create') }}">＋ {{ __('app.users.nouveau') }}</a></div>
    </div>

    <div class="card">
        <x-filtres :action="route('users.index')" :paginateur="$users">
            <x-champ-recherche />
            <div class="field">
                <label>{{ __('app.users.profil') }}</label>
                <select name="profile">
                    <option value="">{{ __('app.commun.tous') }}</option>
                    @foreach ($profils as $profil)
                        <option value="{{ $profil->value }}" {{ request('profile') === $profil->value ? 'selected' : '' }}>{{ __("app.profil.{$profil->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.users.actif') }}</label>
                <select name="is_active">
                    <option value="">{{ __('app.commun.tous') }}</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ __('app.commun.actif') }}</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ __('app.commun.inactif') }}</option>
                </select>
            </div>
        </x-filtres>
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
