<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $texteur->texte($titreDocument) }}</title>
    <style>
        @page { margin: 12mm 12mm 18mm 12mm; }
        body { font-family: "DejaVu Sans"; font-size: 9.5pt; color: #111; margin: 0; }
        p { margin: 0; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }
        .droite { text-align: right; }
        .centre { text-align: center; }
        .muted { color: #444; font-size: 8pt; }
        .mono { font-family: "DejaVu Sans"; letter-spacing: 0.3pt; }

        .entete { border-bottom: 2.5px solid #111; }
        .logo { display: inline-block; border: 1.5px solid #111; font-weight: bold; font-size: 11pt; padding: 1.5mm 2.5mm; }
        .societe-nom { font-size: 14.5pt; font-weight: bold; line-height: 1.2; }
        .doc-titre { font-size: 17pt; font-weight: bold; line-height: 1.2; }
        .doc-sous { font-size: 11pt; }
        .fisc td { background: #f2f2f2; border: 1px solid #999; font-size: 8pt; padding: 1.6mm 2.5mm; }

        .bloc { margin-top: 5mm; }
        .bloc-titre { font-weight: bold; font-size: 10.5pt; border-bottom: 1px solid #999; padding-bottom: 1mm; margin-bottom: 2mm; }
        .fiche td { border: 1px solid #bbb; padding: 1.8mm 2.5mm; font-size: 9pt; }
        .fiche .cle { background: #f7f7f7; font-size: 8pt; color: #333; white-space: nowrap; }

        .data { margin-top: 5mm; }
        .data th { background: #eee; border: 1px solid #999; padding: 1.8mm 2mm; font-size: 8.5pt; }
        .data td { border: 1px solid #bbb; padding: 1.6mm 2mm; font-size: 9pt; }
        .data tr.totaux td { border: none; padding: 1.2mm 2mm; }
        .data .grand { font-size: 11pt; font-weight: bold; border-top: 1.5px solid #111; }

        .arrete { margin-top: 5mm; border: 1px dashed #666; padding: 3mm; font-size: 9.5pt; }
        .signatures { margin-top: 8mm; }
        .signatures td { width: 46%; border: 1px solid #999; padding: 3mm; height: 18mm; font-size: 9pt; }
        .pied { margin-top: 6mm; border-top: 1px solid #999; padding-top: 2mm; font-size: 7.5pt; color: #555; }
    </style>
</head>
<body>
<table class="entete">
    <tr>
        @if ($rtl)
            <td width="38%" class="droite">
                <div class="doc-titre">{{ $texteur->texte($titreDocument) }}</div>
                <div class="doc-sous">{{ $texteur->texte($sousTitreDocument) }}</div>
            </td>
            <td width="62%" class="droite">
                <div><span class="logo">TR</span></div>
                <div class="societe-nom">{{ $texteur->texte(config('societe.raison_sociale')) }}</div>
                <div class="muted">{{ $texteur->texte(config('societe.forme_juridique')) }}</div>
                <div class="muted">{{ $texteur->texte(config('societe.adresse')) }} — {{ $texteur->texte(config('societe.wilaya')) }}</div>
                <div class="muted">{{ $texteur->texte(config('societe.telephone')) }} · {{ $texteur->texte(config('societe.email')) }}</div>
            </td>
        @else
            <td width="62%">
                <div><span class="logo">TR</span></div>
                <div class="societe-nom">{{ $texteur->texte(config('societe.raison_sociale')) }}</div>
                <div class="muted">{{ $texteur->texte(config('societe.forme_juridique')) }}</div>
                <div class="muted">{{ $texteur->texte(config('societe.adresse')) }} — {{ $texteur->texte(config('societe.wilaya')) }}</div>
                <div class="muted">{{ $texteur->texte(config('societe.telephone')) }} · {{ $texteur->texte(config('societe.email')) }}</div>
            </td>
            <td width="38%" class="droite">
                <div class="doc-titre">{{ $texteur->texte($titreDocument) }}</div>
                <div class="doc-sous">{{ $texteur->texte($sousTitreDocument) }}</div>
            </td>
        @endif
    </tr>
</table>

<table class="fisc" style="margin-top: 2mm">
    <tr>
        <td>
            {{ $texteur->texte(__('app.pdf.nif')) }} : <span class="mono">{{ $texteur->texte(config('societe.nif')) }}</span>
            — {{ $texteur->texte(__('app.pdf.nis')) }} : <span class="mono">{{ $texteur->texte(config('societe.nis')) }}</span>
            — {{ $texteur->texte(__('app.pdf.rc')) }} : <span class="mono">{{ $texteur->texte(config('societe.rc')) }}</span>
            — {{ $texteur->texte(__('app.pdf.ai')) }} : <span class="mono">{{ $texteur->texte(config('societe.ai')) }}</span>
        </td>
    </tr>
</table>

@yield('contenu')

<table class="signatures">
    <tr>
        @if ($rtl)
            <td class="droite"><strong>{{ $texteur->texte(__('app.pdf.client')) }}</strong><br>{{ $texteur->texte(__('app.pdf.cachet')) }}</td>
            <td class="droite"><strong>{{ $texteur->texte(__('app.pdf.transitaire')) }}</strong><br>{{ $texteur->texte(__('app.pdf.cachet')) }}</td>
        @else
            <td><strong>{{ $texteur->texte(__('app.pdf.transitaire')) }}</strong><br>{{ $texteur->texte(__('app.pdf.cachet')) }}</td>
            <td><strong>{{ $texteur->texte(__('app.pdf.client')) }}</strong><br>{{ $texteur->texte(__('app.pdf.cachet')) }}</td>
        @endif
    </tr>
</table>

<table class="pied">
    <tr>
        <td>{{ $texteur->texte(__('app.pdf.genere_le', ['date' => now()->format('d/m/Y'), 'societe' => config('societe.raison_sociale')])) }}</td>
        <td class="droite">{{ $texteur->texte(__('app.nom')) }}</td>
    </tr>
</table>

<script type="text/php">
if (isset($pdf) && $pdf->get_page_number() > 0) {
    $pdf->page_text(290, 820, '{PAGE_NUM} / {PAGE_COUNT}', 'Helvetica', 8, [85, 85, 85]);
}
</script>
</body>
</html>
