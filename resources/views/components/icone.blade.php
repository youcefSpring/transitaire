@props(['name', 'size' => 18])

@php
    $traces = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'clients' => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0"/><path d="M16 5.4a3.2 3.2 0 0 1 0 6.2"/><path d="M17.5 14.6A5.5 5.5 0 0 1 20.5 19"/>',
        'dossiers' => '<path d="M3 7.5A1.5 1.5 0 0 1 4.5 6h4l2 2.5h7A1.5 1.5 0 0 1 19 10v7.5A1.5 1.5 0 0 1 17.5 19h-13A1.5 1.5 0 0 1 3 17.5z"/>',
        'conteneurs' => '<path d="M3 10.5h18l-1.6 6.2A2 2 0 0 1 17.5 18h-11a2 2 0 0 1-1.9-1.3z"/><path d="M7 10.5V7l5-2.5L17 7v3.5"/><path d="M12 4.5V10"/>',
        'factures' => '<path d="M6 3.5h12v17l-2.4-1.6-2.4 1.6-2.4-1.6L8.4 20.5 6 18.9z"/><path d="M9 8h6"/><path d="M9 12h6"/>',
        'paiements' => '<rect x="2.8" y="5.5" width="18.4" height="13" rx="2.2"/><path d="M2.8 10h18.4"/><path d="M6.5 14.5h3.5"/>',
        'fournisseurs' => '<path d="M8.5 12.5 5 9l3-3 2.2 2.2h5.3L18 9l-3.5 3.5"/><path d="M5.5 12.5v6h13v-6"/>',
        'transport' => '<path d="M2.5 7.5h10v9h-10z"/><path d="M12.5 10.5h4l3 3v3h-7z"/><circle cx="6.5" cy="18" r="1.8"/><circle cx="16.5" cy="18" r="1.8"/>',
        'alertes' => '<path d="M12 4a5.5 5.5 0 0 1 5.5 5.5c0 4 1.5 5.5 1.5 5.5H5s1.5-1.5 1.5-5.5A5.5 5.5 0 0 1 12 4z"/><path d="M10.2 18a1.9 1.9 0 0 0 3.6 0"/>',
        'rapports' => '<path d="M4 19.5V5"/><path d="M4 19.5h16"/><path d="M8 16V11"/><path d="M12.5 16V7.5"/><path d="M17 16v-3"/>',
        'users' => '<rect x="4.5" y="10.5" width="15" height="9.5" rx="2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/><path d="M12 14v2.5"/>',
        'audit' => '<circle cx="12" cy="12" r="8.2"/><path d="m14.8 9.2-1.7 4-4 1.7 1.7-4z"/>',
        'douane' => '<path d="M12 3.5 19 6v5.5c0 4-3 7-7 8.5-4-1.5-7-4.5-7-8.5V6z"/><path d="m9.3 12 1.9 1.9 3.5-3.8"/>',
        'documents' => '<path d="M6.5 3.5h7L18 8v12.5H6.5z"/><path d="M13.5 3.5V8H18"/><path d="M9.5 12.5h5"/><path d="M9.5 16h5"/>',
        'frais' => '<circle cx="12" cy="12" r="8.2"/><path d="M14.5 9.3A3 3 0 0 0 9.6 11c0 2.6 4.8 1.4 4.8 4a3 3 0 0 1-4.9 1.7"/><path d="M12 7.2v9.6"/>',
        'facturation' => '<path d="M6 3.5h12v17l-2.4-1.6-2.4 1.6-2.4-1.6L8.4 20.5 6 18.9z"/><path d="M9 8h6"/><path d="M9 12h4"/>',
        'traceabilite' => '<circle cx="12" cy="12" r="8.2"/><path d="m14.8 9.2-1.7 4-4 1.7 1.7-4z"/>',
        'deconnexion' => '<path d="M14 7.5V5.5a1.5 1.5 0 0 0-1.5-1.5h-6A1.5 1.5 0 0 0 5 5.5v13A1.5 1.5 0 0 0 6.5 20h6a1.5 1.5 0 0 0 1.5-1.5v-2"/><path d="M10.5 12H20"/><path d="m17 9 3 3-3 3"/>',
    ];
    $trace = $traces[$name] ?? $traces['dossiers'];
@endphp

<svg class="ico" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true" focusable="false">{!! $trace !!}</svg>
