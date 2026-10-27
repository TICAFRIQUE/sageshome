<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>@yield('title') - Sages Home</title>
    <style>
        @page { margin: 26mm 12mm 16mm 12mm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1F2A1A; line-height: 1.4; margin: 0; }

        /* En-tête et pied répétés sur chaque page */
        .pdf-header { position: fixed; top: -20mm; left: 0; right: 0; height: 16mm; border-bottom: 1.5pt solid #C29B32; }
        .pdf-header table { width: 100%; border-collapse: collapse; }
        .pdf-header td { vertical-align: middle; padding: 0; }
        .brand-logo { width: 11mm; height: 11mm; }
        .brand-name { font-size: 13pt; font-weight: bold; color: #37542A; letter-spacing: 1pt; }
        .brand-sub { font-size: 7pt; color: #8A6A1F; letter-spacing: .8pt; }
        .doc-title { text-align: right; font-size: 12pt; font-weight: bold; color: #37542A; }
        .doc-meta { text-align: right; font-size: 7.5pt; color: #6B7266; }

        .pdf-footer { position: fixed; bottom: -11mm; left: 0; right: 0; height: 8mm; border-top: .5pt solid #DDD9CC; font-size: 7pt; color: #6B7266; padding-top: 2mm; }
        .pdf-footer .page:after { content: "Page " counter(page) " / " counter(pages); }

        h2 { font-size: 10.5pt; color: #37542A; margin: 6mm 0 2.5mm; padding-bottom: 1mm; border-bottom: .5pt solid #E7E5DC; }
        h2:first-child { margin-top: 0; }
        .muted { color: #6B7266; }
        .small { font-size: 7.5pt; }
        .right { text-align: right; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .bold { font-weight: bold; }
        .success { color: #1E7B3C; }
        .warning { color: #9A6200; }
        .danger { color: #B42318; }

        /* Indicateurs */
        table.kpis { width: 100%; border-collapse: separate; border-spacing: 2mm 0; margin: 0 -2mm 2mm; }
        table.kpis td { width: 25%; padding: 3mm; background: #F7F6F1; border: .5pt solid #E7E5DC; border-radius: 2mm; vertical-align: top; }
        table.kpis td.featured { background: #37542A; color: #fff; border-color: #37542A; }
        .kpi-label { font-size: 6.8pt; text-transform: uppercase; letter-spacing: .6pt; color: #6B7266; }
        .featured .kpi-label { color: #E6EDE1; }
        .kpi-value { font-size: 13pt; font-weight: bold; margin-top: 1mm; }
        .kpi-value small { font-size: 7pt; font-weight: normal; }
        .kpi-meta { font-size: 7pt; color: #6B7266; margin-top: .5mm; }
        .featured .kpi-meta { color: #E6EDE1; }

        /* Tableaux de données */
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 2mm; }
        table.data thead { display: table-header-group; } /* répété sur chaque page */
        table.data tr { page-break-inside: avoid; }
        table.data th {
            background: #37542A; color: #fff; font-weight: bold; font-size: 7.2pt; text-transform: uppercase;
            letter-spacing: .3pt; padding: 2mm 2mm; text-align: left; border: .5pt solid #37542A;
        }
        table.data td { padding: 1.6mm 2mm; border-bottom: .5pt solid #E7E5DC; vertical-align: top; }
        table.data tbody tr:nth-child(even) td { background: #FAF9F5; }
        table.data tfoot td { font-weight: bold; background: #F1EFE7; border-top: 1pt solid #37542A; padding: 2mm; }
        .badge { display: inline-block; padding: .4mm 1.6mm; border-radius: 3mm; font-size: 6.8pt; font-weight: bold; }
        .b-pending { background: #FFF1D6; color: #8A5A00; }
        .b-confirmed, .b-paid { background: #DFF1E4; color: #1E6B34; }
        .b-cancelled, .b-failed { background: #FBE3E3; color: #9E1E1E; }
        .b-completed { background: #E2EDF7; color: #1F5F99; }
        .b-none { background: #EEEDE8; color: #6B7266; }

        .bar { height: 2mm; background: #EEEDE8; border-radius: 1mm; }
        .bar span { display: block; height: 2mm; background: #C29B32; border-radius: 1mm; }

        .box { border: .5pt solid #E7E5DC; border-radius: 2mm; padding: 3mm; }
        .signature { margin-top: 10mm; width: 100%; }
        .signature td { width: 50%; vertical-align: top; font-size: 8pt; }
        .signature .line { margin-top: 12mm; border-top: .5pt solid #1F2A1A; width: 60mm; }
        @yield('styles')
    </style>
</head>
<body>
    <div class="pdf-header">
        <table>
            <tr>
                <td style="width: 13mm"><img src="{{ public_path('images/logo-mark.png') }}" class="brand-logo" alt=""></td>
                <td>
                    <div class="brand-name">SAGES HOME</div>
                    <div class="brand-sub">IMMO · RÉSIDENCES · VOYAGES</div>
                </td>
                <td>
                    <div class="doc-title">@yield('title')</div>
                    <div class="doc-meta">@yield('subtitle')</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="pdf-footer">
        <table style="width:100%; border-collapse:collapse">
            <tr>
                <td>Sages Home{{ $parametre?->localisation ? ' · ' . $parametre->localisation : '' }}{{ $parametre?->contact1 ? ' · ' . $parametre->contact1 : '' }}{{ $parametre?->email1 ? ' · ' . $parametre->email1 : '' }}</td>
                <td class="right nowrap">Édité le {{ now()->format('d/m/Y à H:i') }} · <span class="page"></span></td>
            </tr>
        </table>
    </div>

    <main>
        @yield('content')
    </main>
</body>
</html>
