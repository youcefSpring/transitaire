@extends('layouts.app')

@section('titre', __('app.audit.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.audit.titre') }}</h1>
    </div>

    <div class="card">
        <form class="toolbar no-print" method="GET" action="{{ route('audit.index') }}">
            <div class="field"><label>{{ __('app.commun.numero') }}</label><input name="dossier" value="{{ request('dossier') }}" placeholder="TR-2026-0001" class="mono"></div>
            <div class="field"><label>{{ __('app.commun.date') }}</label><input type="date" name="date" value="{{ request('date') }}" class="mono"></div>
            <button type="submit" class="btn secondary small">🔍</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.date') }}</th><th>{{ __('app.audit.utilisateur') }}</th><th>{{ __('app.audit.action') }}</th><th>{{ __('app.commun.dossier') }}</th></tr></thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="mono">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user?->name }}</td>
                        <td>{{ $log->action }}</td>
                        <td>@if ($log->dossier)<a class="mono" href="{{ route('dossiers.show', $log->dossier->numero) }}">{{ $log->dossier->numero }}</a>@else—@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    </div>
@endsection
