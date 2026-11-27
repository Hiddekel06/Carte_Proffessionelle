@php
    use App\Models\Agent;
    use App\Support\QrCode;

    /*
     * Toutes les positions ci-dessous sont en « unités design » : la page Canva
     * mesure 1123 × 1587 unités. La carte est rendue à cette taille puis mise à
     * l'échelle (zoom) pour l'écran et pour l'impression.
     */
    $inspecteur = $agent->modele_carte_effectif === Agent::MODELE_INSPECTEUR;
    $modele     = $inspecteur ? 'inspecteur' : 'standard';

    $layout = $inspecteur
        ? [
            'photo'  => ['top' => 283.1, 'left' => 367.2, 'width' => 382.1, 'height' => 431.9],
            'valeurs_left' => 440.9,
            'lignes' => [
                ['top' => 1015.0, 'valeur' => $agent->nom_carte,       'lignes_max' => 1],
                ['top' => 1107.0, 'valeur' => $agent->matricule_carte, 'lignes_max' => 1],
                ['top' => 1203.6, 'valeur' => $agent->fonction ?? $agent->poste ?? '', 'lignes_max' => 2],
            ],
            'qr' => ['top' => 1014.0, 'left' => 425.8, 'size' => 204.0],
        ]
        : [
            'photo'  => ['top' => 271.2, 'left' => 391.9, 'width' => 366.3, 'height' => 414.1],
            'valeurs_left' => 467.0,
            'lignes' => [
                ['top' => 989.0,  'valeur' => $agent->nom_carte,       'lignes_max' => 1],
                ['top' => 1081.1, 'valeur' => $agent->matricule_carte, 'lignes_max' => 1],
                ['top' => 1173.1, 'valeur' => $agent->service ?? $agent->structure_affectation ?? '', 'lignes_max' => 2],
                ['top' => 1265.2, 'valeur' => $agent->fonction ?? $agent->poste ?? '', 'lignes_max' => 2],
            ],
            'qr' => ['top' => 580.1, 'left' => 337.1, 'size' => 448.4],
        ];

    $largeurMm  = config('carte.largeur_mm', 85);
    $hauteurMm  = round($largeurMm * 1587.4 / 1122.5, 2);
    // 1 mm = 96 / 25,4 px CSS
    $zoomPrint  = round(($largeurMm * 96 / 25.4) / 1123, 5);
    $qrDataUri  = QrCode::svgDataUri($agent->public_url);
    $police     = config('carte.police');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Carte professionnelle — {{ $agent->nom_carte }} ({{ $agent->matricule_carte }})</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @if(!empty($police['google_fonts']))
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{ $police['google_fonts'] }}&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #e2e8f0;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
        }

        /* ===== Barre d'outils (écran uniquement) ===== */
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            padding: 20px 16px 4px;
        }
        .toolbar .info {
            width: 100%;
            text-align: center;
            font-size: 13px;
            color: #475569;
            margin-bottom: 6px;
        }
        .toolbar .info strong { color: #0f172a; }
        .badge-modele {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: {{ $inspecteur ? '#fef3c7' : '#dcfce7' }};
            color: {{ $inspecteur ? '#92400e' : '#166534' }};
        }
        .badge-etat {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: #f1f5f9;
            color: #334155;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            background: #0b1a13;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background: #00843f; }
        .btn:disabled { opacity: .6; cursor: wait; }
        .btn-secondary { background: #475569; }
        .btn-secondary:hover { background: #334155; }
        .btn-primary { background: #00843f; }
        .btn-primary:hover { background: #006b34; }

        /* ===== Planche (écran) ===== */
        .planche {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            justify-content: center;
            padding: 24px 16px 48px;
        }
        .face-wrap { text-align: center; }
        .face-wrap .label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin-bottom: 10px;
        }

        /* ===== Carte : rendue en unités design (1123 × 1587) ===== */
        .face {
            position: relative;
            width: 1123px;
            height: 1587px;
            overflow: hidden;
            background: #fff;
            zoom: 0.4;
            box-shadow: 0 30px 60px rgba(15, 23, 42, .25);
            border-radius: 24px;
        }
        @media (max-width: 1000px) { .face { zoom: 0.3; } }

        .face .fond {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
        }

        .face .photo {
            position: absolute;
            overflow: hidden;
            border: 1px solid #000;
            border-radius: 36px;
            background: #f1f5f9;
        }
        .face .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 20%;
            display: block;
        }

        .face .valeur {
            position: absolute;
            width: {{ 1123 - $layout['valeurs_left'] - 30 }}px;
            font-family: {!! $police['famille'] !!}, 'Segoe UI', sans-serif;
            font-weight: {{ $police['graisse'] }};
            font-size: 36px;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #001554;
            text-align: left;
            white-space: nowrap;
        }
        .face .valeur.multi { white-space: normal; }

        .face .qr {
            position: absolute;
            display: block;
        }

        /* ===== Impression : une face par page, au format réel ===== */
        @media print {
            @page {
                size: {{ $largeurMm }}mm {{ $hauteurMm }}mm;
                margin: 0;
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            .no-print { display: none !important; }
            .planche {
                display: block;
                padding: 0;
            }
            .face-wrap .label { display: none; }
            .face {
                zoom: {{ $zoomPrint }};
                box-shadow: none;
                border-radius: 0;
                break-after: page;
                page-break-after: always;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .face-wrap:last-child .face {
                break-after: auto;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>

<div class="toolbar no-print">
    <div class="info">
        <strong>{{ $agent->nom_carte }}</strong> · {{ $agent->matricule_carte }}
        · Modèle <span class="badge-modele">{{ $inspecteur ? 'Inspecteur / Contrôleur' : 'Standard' }}</span>
        · Format {{ $largeurMm }} × {{ $hauteurMm }} mm
        · État : <span class="badge-etat" id="etat-carte">@switch($agent->etat_carte)
            @case(Agent::ETAT_REMISE)Remise le {{ $agent->carte_remise_le->format('d/m/Y') }}@break
            @case(Agent::ETAT_A_REMETTRE)Imprimée le {{ $agent->carte_imprimee_le->format('d/m/Y') }} — à remettre@break
            @default Non imprimée
        @endswitch</span>
    </div>
    <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-secondary"><i class="fa-solid fa-pen"></i> Modifier l'agent</a>
    <button type="button" class="btn" id="btn-pdf" onclick="exporterCarte('pdf')"><i class="fa-solid fa-file-pdf"></i> Télécharger PDF</button>
    <button type="button" class="btn" id="btn-png" onclick="exporterCarte('png')"><i class="fa-solid fa-image"></i> Télécharger PNG</button>
    <button type="button" class="btn btn-primary" onclick="imprimerCarte()"><i class="fa-solid fa-print"></i> Imprimer</button>
</div>

<main class="planche">

    {{-- ===== RECTO ===== --}}
    <div class="face-wrap">
        <div class="label no-print">Recto</div>
        <section class="face" id="carte-recto" aria-label="Recto de la carte">
            <img class="fond" src="{{ asset("images/carte/recto_{$modele}.png") }}" alt="">

            <div class="photo" style="top: {{ $layout['photo']['top'] }}px; left: {{ $layout['photo']['left'] }}px; width: {{ $layout['photo']['width'] }}px; height: {{ $layout['photo']['height'] }}px;">
                <img src="{{ $agent->photo_url }}" alt="Photo de {{ $agent->nom_carte }}" crossorigin="anonymous">
            </div>

            @foreach($layout['lignes'] as $ligne)
                <div class="valeur {{ $ligne['lignes_max'] > 1 ? 'multi' : '' }}"
                     data-lignes-max="{{ $ligne['lignes_max'] }}"
                     style="top: {{ $ligne['top'] }}px; left: {{ $layout['valeurs_left'] }}px;">{{ $ligne['valeur'] }}</div>
            @endforeach
        </section>
    </div>

    {{-- ===== VERSO ===== --}}
    <div class="face-wrap">
        <div class="label no-print">Verso</div>
        <section class="face" id="carte-verso" aria-label="Verso de la carte">
            <img class="fond" src="{{ asset("images/carte/verso_{$modele}.png") }}" alt="">
            <img class="qr" src="{{ $qrDataUri }}" alt="QR code {{ $agent->matricule_carte }}"
                 style="top: {{ $layout['qr']['top'] }}px; left: {{ $layout['qr']['left'] }}px; width: {{ $layout['qr']['size'] }}px; height: {{ $layout['qr']['size'] }}px;">
        </section>
    </div>

</main>

<script>
/**
 * Ajuste les textes trop longs : réduit la police jusqu'à 80 %, puis
 * passe sur 2 lignes si autorisé, puis réduit encore si nécessaire.
 */
function ajusterTextes() {
    document.querySelectorAll('.face .valeur').forEach(function (el) {
        const lignesMax = parseInt(el.dataset.lignesMax || '1', 10);
        const tailleBase = 36;
        let taille = tailleBase;
        el.style.fontSize = taille + 'px';

        const hauteurMax = () => taille * 1.2 * lignesMax + 2;
        const deborde = () => el.scrollWidth > el.clientWidth + 1 || el.scrollHeight > hauteurMax();

        if (lignesMax === 1) {
            while (deborde() && taille > 22) {
                taille -= 1;
                el.style.fontSize = taille + 'px';
            }
            return;
        }

        // Plusieurs lignes : si le texte ne tient pas sur une ligne, on passe à 30 px
        // pour que les 2 lignes restent dans l'espace d'une ligne du design.
        if (el.scrollHeight > taille * 1.2 + 2) {
            taille = 30;
            el.style.fontSize = taille + 'px';
        }
        while (deborde() && taille > 24) {
            taille -= 1;
            el.style.fontSize = taille + 'px';
        }
    });
}

document.fonts ? document.fonts.ready.then(ajusterTextes) : window.addEventListener('load', ajusterTextes);

/**
 * html2canvas ne gère pas « object-fit: cover » : il étire la photo.
 * On recadre donc la photo nous-mêmes (même cadrage qu'à l'écran :
 * centrée horizontalement, 20 % depuis le haut) avant l'export.
 */
async function recadrerPhotos(original, clone) {
    const sources = original.querySelectorAll('.photo img');
    const copies = clone.querySelectorAll('.photo img');

    for (let i = 0; i < sources.length; i++) {
        const src = sources[i];
        const dst = copies[i];
        if (!src.complete || !src.naturalWidth) continue;

        const cadre = src.parentElement;
        const largeur = parseFloat(cadre.style.width) * 2;   // export en x2
        const hauteur = parseFloat(cadre.style.height) * 2;

        const ratio = Math.max(largeur / src.naturalWidth, hauteur / src.naturalHeight);
        const lSource = largeur / ratio;
        const hSource = hauteur / ratio;
        const x = (src.naturalWidth - lSource) * 0.5;
        const y = (src.naturalHeight - hSource) * 0.2;

        const canvas = document.createElement('canvas');
        canvas.width = Math.round(largeur);
        canvas.height = Math.round(hauteur);
        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(src, x, y, lSource, hSource, 0, 0, canvas.width, canvas.height);

        try {
            dst.src = canvas.toDataURL('image/jpeg', 0.95);
            dst.style.objectFit = 'fill';
            await dst.decode();
        } catch (e) {
            // Image d'un autre site sans autorisation CORS : on la laisse telle quelle
            console.warn('Recadrage photo impossible', e);
        }
    }
}

/**
 * Rend une face en image à pleine résolution (2246 × 3174 px, comme l'export Canva).
 */
async function rendreFace(face) {
    const zone = document.createElement('div');
    zone.style.cssText = 'position:fixed;left:-20000px;top:0;';
    const clone = face.cloneNode(true);
    clone.style.zoom = '1';
    clone.style.borderRadius = '0';
    clone.style.boxShadow = 'none';
    zone.appendChild(clone);
    document.body.appendChild(zone);
    await recadrerPhotos(face, clone);
    try {
        return await html2canvas(clone, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            width: 1123,
            height: 1587,
            windowWidth: 1400,
        });
    } finally {
        zone.remove();
    }
}

/**
 * Enregistre la carte comme « imprimée » (date du jour).
 */
async function marquerImprimee() {
    try {
        const rep = await fetch(@json(route('admin.agents.carte-imprimee', $agent)), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        if (!rep.ok) throw new Error('HTTP ' + rep.status);
        const data = await rep.json();
        const etat = document.getElementById('etat-carte');
        if (etat && data.etat !== 'remise') {
            etat.textContent = 'Imprimée le ' + data.imprimee_le.split(' ')[0] + ' — à remettre';
        }
    } catch (e) {
        console.error(e);
        alert("La carte n'a pas pu être enregistrée comme imprimée. Vous pourrez la marquer depuis la liste des agents.");
    }
}

/**
 * Ouvre l'impression puis demande confirmation : la boîte d'impression
 * peut être annulée, on ne marque donc « imprimée » qu'après accord.
 */
function imprimerCarte() {
    window.addEventListener('afterprint', function apres() {
        window.removeEventListener('afterprint', apres);
        setTimeout(() => {
            if (confirm('La carte a-t-elle bien été imprimée ?\nElle sera enregistrée comme « Imprimée — à remettre ».')) {
                marquerImprimee();
            }
        }, 200);
    });
    window.print();
}

async function exporterCarte(format) {
    const boutons = [document.getElementById('btn-pdf'), document.getElementById('btn-png')];
    boutons.forEach(b => b.disabled = true);
    try {
        const recto = await rendreFace(document.getElementById('carte-recto'));
        const verso = await rendreFace(document.getElementById('carte-verso'));
        const base = 'carte_{{ $agent->matricule }}';

        if (format === 'png') {
            [[recto, '_recto'], [verso, '_verso']].forEach(([canvas, suffixe], i) => {
                setTimeout(() => {
                    const lien = document.createElement('a');
                    lien.download = base + suffixe + '.png';
                    lien.href = canvas.toDataURL('image/png');
                    lien.click();
                }, i * 400);
            });
        } else {
            const { jsPDF } = window.jspdf;
            const l = {{ $largeurMm }}, h = {{ $hauteurMm }};
            const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: [l, h] });
            pdf.addImage(recto.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, l, h);
            pdf.addPage([l, h], 'portrait');
            pdf.addImage(verso.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, l, h);
            pdf.save(base + '.pdf');
            await marquerImprimee();
        }
    } catch (e) {
        console.error(e);
        alert("Une erreur est survenue lors de l'export.");
    } finally {
        boutons.forEach(b => b.disabled = false);
    }
}
</script>

</body>
</html>
