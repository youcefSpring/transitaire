@props(['cle'])

@php $texte = __("app.aide.{$cle}"); @endphp

@if ($texte !== "app.aide.{$cle}")
    <small class="hint">{{ $texte }}</small>
@endif
