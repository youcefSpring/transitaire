@props(['nom' => 'search'])

<div class="field champ-recherche">
    <label for="filtre-{{ $nom }}">{{ __('app.commun.rechercher') }}</label>
    <input id="filtre-{{ $nom }}" type="search" name="{{ $nom }}"
           value="{{ request($nom) }}" placeholder="{{ __('app.commun.recherche_placeholder') }}">
</div>
