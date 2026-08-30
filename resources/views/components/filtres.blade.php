@props(['action', 'paginateur' => null])

@php
    $filtresActifs = collect(request()->query())
        ->except('page')
        ->filter(fn ($valeur) => $valeur !== null && $valeur !== '')
        ->isNotEmpty();
@endphp

<form class="toolbar no-print" method="GET" action="{{ $action }}">
    {{ $slot }}

    <div class="field filtres-actions">
        <button type="submit" class="btn secondary small">{{ __('app.commun.rechercher') }}</button>
        @if ($filtresActifs)
            <a class="btn secondary small" href="{{ $action }}">{{ __('app.commun.reinitialiser') }}</a>
        @endif
    </div>

    @if ($paginateur)
        <div class="field filtres-total">
            <span class="hint">{{ $paginateur->total() }} {{ __('app.commun.resultats') }}</span>
        </div>
    @endif
</form>
