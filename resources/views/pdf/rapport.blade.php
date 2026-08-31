@extends('pdf.layout', ['titreDocument' => __("app.rapports.types.{$type}"), 'sousTitreDocument' => __('app.pdf.periode').' : '.$du->format('d/m/Y').' → '.$au->format('d/m/Y')])

@section('contenu')
    @php
        $enumColonnes = [
            'type' => 'type_operation',
            'mode_transport' => 'mode_transport',
            'statut' => 'statut',
            'categorie' => 'frais_categorie',
            'mode' => 'paiement_mode',
            'devise' => 'devise',
        ];
        $entetes = collect($rapport['lignes'] ?? [])->first() ?? [];
        $colonnes = array_keys($entetes);
        $colonnes = $rtl ? array_reverse($colonnes) : $colonnes;
    @endphp

    <table class="bloc">
        <tr>
            <td class="droite" style="width: 100%">
                {{ $texteur->texte(__('app.pdf.periode')) }} :
                <span class="mono">{{ $du->format('d/m/Y') }} → {{ $au->format('d/m/Y') }}</span>
            </td>
        </tr>
    </table>

    @foreach ($rapport as $cle => $valeur)
        @if ($cle !== 'lignes')
            <table class="fiche" style="margin-top: 4mm">
                <tr>
                    @if ($rtl)
                        <td class="mono droite">{{ is_int($valeur) ? $valeur : number_format((float) $valeur, 2, ',', ' ') }}</td>
                        <td class="cle droite">{{ $texteur->texte(__("app.rapports.colonnes.{$cle}") !== "app.rapports.colonnes.{$cle}" ? __("app.rapports.colonnes.{$cle}") : $cle) }}</td>
                    @else
                        <td class="cle">{{ $texteur->texte(__("app.rapports.colonnes.{$cle}") !== "app.rapports.colonnes.{$cle}" ? __("app.rapports.colonnes.{$cle}") : $cle) }}</td>
                        <td class="droite mono">{{ is_int($valeur) ? $valeur : number_format((float) $valeur, 2, ',', ' ') }}</td>
                    @endif
                </tr>
            </table>
        @endif
    @endforeach

    <div class="bloc">
        <div class="bloc-titre">{{ $texteur->texte(__('app.rapports.resultat')) }}</div>
        <table class="data">
            <thead>
                <tr>
                    @foreach ($colonnes as $cle)
                        @php
                            $libelle = __("app.rapports.colonnes.{$cle}") !== "app.rapports.colonnes.{$cle}"
                                ? __("app.rapports.colonnes.{$cle}")
                                : $cle;
                        @endphp
                        <th>{{ $texteur->texte($libelle) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rapport['lignes'] ?? [] as $ligne)
                    <tr>
                        @foreach ($colonnes as $cle)
                            @php
                                $valeur = $ligne[$cle] ?? '—';
                                if (isset($enumColonnes[$cle]) && is_string($valeur)) {
                                    $traduit = __("app.{$enumColonnes[$cle]}.{$valeur}");
                                    $valeur = $traduit !== "app.{$enumColonnes[$cle]}.{$valeur}" ? $traduit : $valeur;
                                }
                                $classe = is_numeric($valeur) ? 'droite mono' : '';
                                $affiche = is_numeric($valeur)
                                    ? (is_int($valeur) || (string) (int) $valeur === $valeur ? $valeur : number_format((float) $valeur, 2, ',', ' '))
                                    : $valeur;
                            @endphp
                            <td class="{{ $classe }}">{{ $texteur->texte($affiche) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="9" class="centre muted">{{ $texteur->texte(__('app.commun.aucune_donnee')) }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
