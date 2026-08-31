@extends('layouts.app')

@section('titre', __('app.rapports.types.'.$type))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.rapports.types.'.$type) }}</h1>
        <div class="actions no-print">
            <a class="btn small" href="{{ route('rapports.pdf', $type) }}?du={{ $du->toDateString() }}&au={{ $au->toDateString() }}">🖨 {{ __('app.commun.pdf') }}</a>
            <button type="button" class="btn secondary small" onclick="window.print()">🖨 {{ __('app.commun.imprimer') }}</button>
        </div>
    </div>

    <div class="card no-print">
        <form class="toolbar" method="GET" action="{{ route('rapports.show', $type) }}">
            <div class="field"><label>{{ __('app.commun.du') }}</label><input type="date" name="du" value="{{ $du->toDateString() }}"></div>
            <div class="field"><label>{{ __('app.commun.au') }}</label><input type="date" name="au" value="{{ $au->toDateString() }}"></div>
            <button type="submit" class="btn small">{{ __('app.commun.generer') }}</button>
        </form>
    </div>

    <div class="card">
        <h2>{{ __('app.rapports.resultat') }} <span class="muted mono">{{ $du->format('d/m/Y') }} → {{ $au->format('d/m/Y') }}</span></h2>

        @foreach ($rapport as $cle => $valeur)
            @if ($cle === 'lignes')
                <div class="table-wrap" style="margin-block-start:10px">
                    <table>
                        <thead>
                        <tr>
                            @foreach (collect($valeur)->first() ?? [] as $entete => $_)
                                <th>{{ $entete }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($valeur as $ligne)
                            <tr>
                                @foreach ($ligne as $colonne)
                                    <td class="{{ is_numeric($colonne) ? 'mono' : '' }}">{{ is_numeric($colonne) ? $colonne : $colonne }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="9" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="kpi" style="margin-block-start:10px">
                    <span class="label">{{ __("app.dashboard.{$cle}") !== "app.dashboard.{$cle}" ? __("app.dashboard.{$cle}") : $cle }}</span>
                    <span class="value mono">{{ is_numeric($valeur) ? $valeur : number_format((float) $valeur, 2, ',', ' ') }}</span>
                </div>
            @endif
        @endforeach
    </div>
@endsection
