@extends('layouts.app')

@section('titre', $dossier->numero)

@section('content')
    @php($etapesFaites = $dossier->douaneEtapes->pluck('etape'))

    <div class="page-head">
        <h1>
            <span class="mono">{{ $dossier->numero }}</span>
            <span class="badge {{ $dossier->bloque ? 'danger' : 'primary' }}">{{ $dossier->bloque ? __('app.dossiers.bloque') : __("app.statut.{$dossier->statut->value}") }}</span>
        </h1>
        <div class="actions">
            @unless ($dossier->bloque || $dossier->statut === \App\Enums\DossierStatut::Cloture)
                <form method="POST" action="{{ route('dossiers.statut', $dossier->numero) }}" style="display:inline-flex;gap:6px">
                    @csrf
                    @method('PATCH')
                    <select name="statut">
                        @foreach (\App\Enums\DossierStatut::cases() as $statut)
                            <option value="{{ $statut->value }}">{{ __("app.statut.{$statut->value}") }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn small"><span class="fl">↪</span> {{ __('app.dossiers.statut_suivant') }}</button>
                </form>
            @endunless
            <a class="btn secondary small" href="{{ route('dossiers.edit', $dossier->numero) }}">{{ __('app.commun.modifier') }}</a>
            <a class="btn small" href="{{ route('dossiers.pdf', $dossier->numero) }}">🖨 {{ __('app.commun.pdf') }}</a>
        </div>
    </div>

    @if ($dossier->bloque)
        <div class="card" style="border-color:var(--danger)">
            <h2>{{ __('app.dossiers.bloque') }} — {{ $dossier->raison_blocage }}</h2>
            <form method="POST" action="{{ route('dossiers.blocage', $dossier->numero) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="bloque" value="0">
                <button type="submit" class="btn small">{{ __('app.dossiers.debloquer') }}</button>
            </form>
        </div>
    @else
        <div class="card no-print">
            <form class="inline-form" method="POST" action="{{ route('dossiers.blocage', $dossier->numero) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="bloque" value="1">
                <div class="field" style="flex:1;min-width:220px">
                    <label>{{ __('app.dossiers.raison_blocage') }}</label>
                    <input name="raison" required>
                </div>
                <button type="submit" class="btn danger small">{{ __('app.dossiers.bloquer') }}</button>
            </form>
        </div>
    @endif

    <div class="grid-kpi">
        <div class="kpi"><span class="label">{{ __('app.dossiers.facture_client') }} (DZD)</span><span class="value mono">{{ number_format($marge['dzd']['facture_client'], 2, ',', ' ') }}</span></div>
        <div class="kpi bad"><span class="label">{{ __('app.dossiers.supporte_transitaire') }} (DZD)</span><span class="value mono">{{ number_format($marge['dzd']['supporte_transitaire'], 2, ',', ' ') }}</span></div>
        <div class="kpi {{ $marge['dzd']['marge_reelle'] >= 0 ? 'good' : 'bad' }}"><span class="label">{{ __('app.dossiers.marge_reelle') }} (DZD)</span><span class="value mono">{{ number_format($marge['dzd']['marge_reelle'], 2, ',', ' ') }}</span></div>
    </div>

    <div class="card">
        <h2>{{ __('app.commun.client') }} : {{ $dossier->client?->raison_sociale }} <a class="btn secondary small no-print" href="{{ route('clients.show', $dossier->client) }}">{{ __('app.commun.detail') }}</a></h2>
        <dl class="dl">
            <div><dt>{{ __('app.dossiers.type') }}</dt><dd>{{ __("app.type_operation.{$dossier->type->value}") }}</dd></div>
            <div><dt>{{ __('app.dossiers.mode_transport') }}</dt><dd>{{ __("app.mode_transport.{$dossier->mode_transport->value}") }}</dd></div>
            <div><dt>{{ __('app.dossiers.port_aeroport') }}</dt><dd>{{ $dossier->port_aeroport }}</dd></div>
            <div><dt>{{ __('app.dossiers.fournisseur_destinataire') }}</dt><dd>{{ $dossier->fournisseur_destinataire }}</dd></div>
            <div><dt>{{ __('app.dossiers.arrivee_prevue') }}</dt><dd class="mono">{{ $dossier->date_arrivee_prevue->format('d/m/Y') }}</dd></div>
            <div><dt>{{ __('app.dossiers.arrivee_reelle') }}</dt><dd class="mono">{{ $dossier->date_arrivee_reelle?->format('d/m/Y') ?? '—' }}</dd></div>
            <div><dt>{{ __('app.dossiers.bl_awb') }}</dt><dd class="mono">{{ $dossier->numero_bl_awb }}</dd></div>
            <div><dt>{{ __('app.dossiers.nombre_colis') }}</dt><dd class="mono">{{ $dossier->nombre_colis }}</dd></div>
            <div><dt>{{ __('app.dossiers.poids') }}</dt><dd class="mono">{{ $dossier->poids }}</dd></div>
            <div><dt>{{ __('app.dossiers.volume') }}</dt><dd class="mono">{{ $dossier->volume }}</dd></div>
            <div><dt>{{ __('app.dossiers.nature_marchandise') }}</dt><dd>{{ $dossier->nature_marchandise }}</dd></div>
            <div><dt>{{ __('app.dossiers.valeur_declaree') }}</dt><dd class="mono">{{ $dossier->valeur_declaree }} {{ $dossier->devise->value }}</dd></div>
            <div><dt>{{ __('app.dossiers.incoterm') }}</dt><dd class="mono">{{ $dossier->incoterm }}</dd></div>
            <div><dt>{{ __('app.dossiers.creer_le') }}</dt><dd>{{ $dossier->created_at->format('d/m/Y H:i') }} — {{ $dossier->createur?->name }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.marchandises') }}</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.dossiers.designation') }}</th><th>{{ __('app.dossiers.quantite') }}</th><th>{{ __('app.dossiers.unite') }}</th><th>{{ __('app.dossiers.nombre_colis') }}</th><th>{{ __('app.dossiers.poids') }}</th><th>{{ __('app.dossiers.volume') }}</th><th>{{ __('app.dossiers.valeur') }}</th><th>{{ __('app.dossiers.pays_origine') }}</th><th>{{ __('app.dossiers.code_tarifaire') }}</th><th class="amount"></th></tr></thead>
                <tbody>
                @forelse ($dossier->marchandises as $marchandise)
                    <tr>
                        <td>{{ $marchandise->designation }}</td>
                        <td class="mono">{{ $marchandise->quantite }}</td>
                        <td>{{ $marchandise->unite }}</td>
                        <td class="mono">{{ $marchandise->nombre_colis }}</td>
                        <td class="mono">{{ $marchandise->poids }}</td>
                        <td class="mono">{{ $marchandise->volume }}</td>
                        <td class="mono">{{ $marchandise->valeur }}</td>
                        <td>{{ $marchandise->pays_origine }}</td>
                        <td class="mono">{{ $marchandise->code_tarifaire }}</td>
                        <td>
                            <div class="row-actions no-print">
                                <form method="POST" action="{{ route('marchandises.destroy', [$dossier->numero, $marchandise]) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <form class="inline-form no-print" method="POST" action="{{ route('dossiers.marchandises', $dossier->numero) }}" style="margin-block-start:12px">
            @csrf
            <div class="field"><label>{{ __('app.dossiers.designation') }} *</label><input name="designation" required></div>
            <div class="field"><label>{{ __('app.dossiers.quantite') }} *</label><input type="number" step="0.001" min="0" name="quantite" required class="mono" style="width:110px"></div>
            <div class="field"><label>{{ __('app.dossiers.unite') }} *</label><input name="unite" required style="width:90px"></div>
            <div class="field"><label>{{ __('app.dossiers.nombre_colis') }} *</label><input type="number" min="0" name="nombre_colis" required class="mono" style="width:100px"></div>
            <div class="field"><label>{{ __('app.dossiers.poids') }} *</label><input type="number" step="0.001" min="0" name="poids" required class="mono" style="width:110px"></div>
            <div class="field"><label>{{ __('app.dossiers.volume') }} *</label><input type="number" step="0.001" min="0" name="volume" required class="mono" style="width:110px"></div>
            <div class="field"><label>{{ __('app.dossiers.valeur') }} *</label><input type="number" step="0.01" min="0" name="valeur" required class="mono" style="width:120px"></div>
            <div class="field"><label>{{ __('app.dossiers.pays_origine') }} *</label><input name="pays_origine" required style="width:130px"></div>
            <div class="field"><label>{{ __('app.dossiers.code_tarifaire') }} *</label><input name="code_tarifaire" required class="mono" style="width:130px"></div>
            <button type="submit" class="btn small">＋</button>
        </form>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.conteneurs') }} <a class="btn secondary small no-print" href="{{ route('conteneurs.index') }}">{{ __('app.conteneurs.titre') }}</a></h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.conteneurs.numero') }}</th><th>{{ __('app.conteneurs.bl') }}</th><th>{{ __('app.dossiers.navire') }}</th><th>{{ __('app.dossiers.eta') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.dossiers.date_sortie') }}</th><th>{{ __('app.dossiers.date_retour') }}</th></tr></thead>
                <tbody>
                @forelse ($dossier->conteneurs as $conteneur)
                    <tr>
                        <td class="mono">{{ $conteneur->numero }}</td>
                        <td class="mono">{{ $conteneur->numero_bl }}</td>
                        <td>{{ $conteneur->navire }}</td>
                        <td class="mono">{{ $conteneur->date_eta->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $conteneur->statut === \App\Enums\ConteneurStatut::Retourne ? 'success' : 'warning' }}">{{ __("app.conteneur_statut.{$conteneur->statut->value}") }}</span></td>
                        <td class="mono">{{ $conteneur->date_sortie?->format('d/m/Y') ?? '—' }}</td>
                        <td class="mono">{{ $conteneur->date_retour?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.documents') }}</h2>
        <form class="inline-form no-print" method="POST" action="{{ route('dossiers.documents', $dossier->numero) }}" enctype="multipart/form-data" style="margin-block-end:12px">
            @csrf
            <div class="field">
                <label>{{ __('app.commun.type') }}</label>
                <select name="categorie">
                    @foreach (\App\Enums\DocumentCategorie::cases() as $categorie)
                        <option value="{{ $categorie->value }}">{{ __("app.document_categorie.{$categorie->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>{{ __('app.dossiers.televerser') }} (PDF/JPG/PNG, ≤ 20 Mo)</label><input type="file" name="fichier" accept=".pdf,.jpg,.jpeg,.png" required></div>
            <button type="submit" class="btn small">⬆ {{ __('app.dossiers.televerser') }}</button>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.nom') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.commun.date') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($dossier->documents as $document)
                    <tr>
                        <td>{{ $document->nom_original }}</td>
                        <td><span class="badge info">{{ __("app.document_categorie.{$document->categorie->value}") }}</span></td>
                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="row-actions no-print">
                                <a class="btn secondary small" href="{{ route('documents.download', $document) }}">⬇</a>
                                <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.douane') }}</h2>
        <ul class="timeline" style="margin-block-end:14px">
            @foreach (\App\Enums\DouaneEtape::cases() as $etape)
                @php($enregistree = $dossier->douaneEtapes->firstWhere('etape', $etape))
                <li class="{{ $enregistree ? 'done' : '' }}">
                    <span class="dot"></span>
                    <div>
                        <strong>{{ __("app.douane_etape.{$etape->value}") }}</strong>
                        <span class="muted">
                            {{ $enregistree ? $enregistree->executedBy?->name.' — '.$enregistree->executed_at->format('d/m/Y H:i') : '—' }}
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
        <form class="inline-form no-print" method="POST" action="{{ route('dossiers.douane', $dossier->numero) }}">
            @csrf
            <div class="field">
                <label>{{ __('app.dossiers.etape') }}</label>
                <select name="etape">
                    @foreach (\App\Enums\DouaneEtape::cases() as $etape)
                        <option value="{{ $etape->value }}">{{ __("app.douane_etape.{$etape->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="flex:1;min-width:200px"><label>{{ __('app.commun.notes') }}</label><input name="notes"></div>
            <button type="submit" class="btn small">＋ {{ __('app.commun.enregistrer') }}</button>
        </form>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.frais') }}</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.date') }}</th><th>{{ __('app.dossiers.sens') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.dossiers.libelle') }}</th><th>{{ __('app.commun.fournisseur') }}</th><th>{{ __('app.commun.montant') }}</th><th class="amount"></th></tr></thead>
                <tbody>
                @forelse ($dossier->frais as $frai)
                    <tr>
                        <td class="mono">{{ $frai->date_frais->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $frai->sens === \App\Enums\FraisSens::FactureClient ? 'success' : 'danger' }}">{{ __("app.dossiers.{$frai->sens->value}") }}</span></td>
                        <td>{{ __("app.frais_categorie.{$frai->categorie->value}") }}</td>
                        <td>{{ $frai->libelle }}</td>
                        <td>{{ $frai->fournisseur?->nom }}</td>
                        <td class="mono">{{ $frai->montant }} {{ $frai->devise->value }}</td>
                        <td>
                            <div class="row-actions no-print">
                                <form method="POST" action="{{ route('frais.destroy', $frai) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <form class="inline-form no-print" method="POST" action="{{ route('dossiers.frais', $dossier->numero) }}" style="margin-block-start:12px">
            @csrf
            <div class="field">
                <label>{{ __('app.dossiers.sens') }}</label>
                <select name="sens" id="frais-sens">
                    @foreach (\App\Enums\FraisSens::cases() as $sens)
                        <option value="{{ $sens->value }}">{{ __("app.dossiers.{$sens->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.commun.type') }}</label>
                <select name="categorie" id="frais-categorie">
                    @foreach (\App\Enums\FraisCategorie::cases() as $categorie)
                        <option value="{{ $categorie->value }}">{{ __("app.frais_categorie.{$categorie->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>{{ __('app.dossiers.libelle') }}</label><input name="libelle"></div>
            <div class="field">
                <label>{{ __('app.commun.fournisseur') }}</label>
                <select name="fournisseur_id" id="frais-fournisseur">
                    <option value=""></option>
                    @foreach ($fournisseurs as $fournisseur)
                        <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>{{ __('app.commun.montant') }} *</label><input type="number" step="0.01" min="0" name="montant" required class="mono" style="width:130px"></div>
            <div class="field">
                <label>{{ __('app.commun.devise') }}</label>
                <select name="devise">
                    @foreach (\App\Enums\Devise::cases() as $devise)
                        <option value="{{ $devise->value }}">{{ $devise->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>{{ __('app.commun.date') }} *</label><input type="date" name="date_frais" value="{{ now()->toDateString() }}" required class="mono"></div>
            <button type="submit" class="btn small">＋</button>
        </form>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.facturation') }} <a class="btn secondary small no-print" href="{{ route('documents-commerciaux.create') }}">{{ __('app.factures.nouveau') }}</a></h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.numero') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.factures.montant_total') }}</th><th>{{ __('app.factures.echeance') }}</th></tr></thead>
                <tbody>
                @forelse ($dossier->documentsCommerciaux as $document)
                    <tr>
                        <td><a class="mono" href="{{ route('documents-commerciaux.show', $document) }}">{{ $document->numero }}</a></td>
                        <td>{{ __("app.dc_type.{$document->type->value}") }}</td>
                        <td><span class="badge {{ $document->statut === \App\Enums\DocumentCommercialStatut::Paye ? 'success' : 'primary' }}">{{ __("app.dc_statut.{$document->statut->value}") }}</span></td>
                        <td class="mono">{{ $document->montant_total }} {{ $document->devise->value }}</td>
                        <td class="mono">{{ $document->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>{{ __('app.dossiers.livraison') }} <a class="btn secondary small no-print" href="{{ route('livraisons.index') }}">{{ __('app.transport.livraisons') }}</a></h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.transport.chargement') }}</th><th>{{ __('app.transport.destination') }}</th><th>{{ __('app.transport.camion_ou_transporteur') }}</th><th>{{ __('app.transport.chauffeurs') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.transport.frais_transport') }}</th></tr></thead>
                <tbody>
                @forelse ($dossier->livraisons as $livraison)
                    <tr>
                        <td class="mono">{{ $livraison->date_heure_chargement->format('d/m/Y H:i') }}</td>
                        <td>{{ $livraison->destination }}</td>
                        <td>{{ $livraison->camion?->immatriculation ?? $livraison->transporteurExterne?->nom }}</td>
                        <td>{{ $livraison->chauffeur?->nom }}</td>
                        <td><span class="badge {{ $livraison->statut === \App\Enums\LivraisonStatut::Livree ? 'success' : 'warning' }}">{{ __("app.livraison_statut.{$livraison->statut->value}") }}</span></td>
                        <td class="mono">{{ $livraison->frais_transport }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
