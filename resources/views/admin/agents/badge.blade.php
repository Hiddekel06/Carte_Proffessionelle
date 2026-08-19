<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte Professionnelle PVC - {{ $agent->nom_complet }} ({{ $agent->matricule }})</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Baloo 2', 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .badge-card, .badge-card * {
            font-family: 'Baloo 2', 'Plus Jakarta Sans', sans-serif;
        }

        /* ===== 3 BANDES DIAGONALES FINES traversant la carte de bord en bord ===== */
        .diagonal-stripes-wrapper {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
            border-radius: inherit;
        }

        /* Bande verte — la plus proche du bord supérieur */
        .band-1 {
            position: absolute;
            top: -90px;
            left: -50px;
            width: 800px;
            height: 16px;
            background: #00853f;
            opacity: 0.55;
            transform: rotate(35deg);
            transform-origin: top left;
        }

        /* Bande jaune — juste en-dessous */
        .band-2 {
            position: absolute;
            top: -118px;
            left: -50px;
            width: 800px;
            height: 13px;
            background: #fcd116;
            opacity: 0.60;
            transform: rotate(35deg);
            transform-origin: top left;
        }

        /* Bande rouge — la plus proche du coin */
        .band-3 {
            position: absolute;
            top: -143px;
            left: -50px;
            width: 800px;
            height: 13px;
            background: #e31b23;
            opacity: 0.50;
            transform: rotate(35deg);
            transform-origin: top left;
        }

        .badge-title-italic {
            font-style: italic;
        }

        /* Container pour l'aperçu */
        .badge-print-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            max-width: 1200px;
            width: 100%;
        }

        .badge-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            align-items: flex-start;
        }

        .badge-side {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .badge-label {
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-label i {
            color: #00853f;
        }

        /* Carte Recto */
        .badge-card {
            width: 340px;
            height: 540px;
            background-color: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            user-select: none;
            transition: transform 0.2s;
        }

        .badge-card:hover {
            transform: scale(1.01);
        }

        .badge-card-verso {
            background: #f8fafc;
        }

        /* RECTO Content */
        .badge-recto-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            padding: 14px 16px;
            color: #152a55;
        }

        /* Header */
        .badge-header {
            text-align: center;
            padding-top: 4px;
        }

        .flag {
            display: flex;
            justify-content: center;
            margin-bottom: 6px;
        }

        .flag-inner {
            width: 40px;
            height: 24px;
            display: flex;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .flag-green {
            width: 33.33%;
            height: 100%;
            background-color: #00853f;
        }

        .flag-yellow {
            width: 33.33%;
            height: 100%;
            background-color: #fdef42;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            color: #00853f;
            line-height: 1;
        }

        .flag-red {
            width: 33.33%;
            height: 100%;
            background-color: #e31b23;
        }

        .badge-ministry {
            font-size: 10.5px;
            font-weight: 700;
            color: #152a55;
            text-transform: uppercase;
            letter-spacing: -0.02em;
            line-height: 1.3;
            padding: 0 4px;
        }

        /* Photo */
        .badge-photo {
            margin: 4px 0 6px;
            display: flex;
            justify-content: center;
            position: relative;
        }

        .badge-photo .watermark {
            position: absolute;
            width: 100px;
            height: 100px;
            object-fit: contain;
            opacity: 0.06;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .photo-frame {
            width: 128px;
            height: 144px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            background: #ffffff;
            padding: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            background: #f1f5f9;
            display: block;
        }

        /* Black Pill Banner */
        .badge-pill {
            background: #0b1f18;
            color: #ffffff;
            border-radius: 9999px;
            padding: 6px 14px;
            margin: 4px 0 8px;
            text-align: center;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-pill span {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: -0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .badge-pill .dots {
            position: absolute;
            right: 8px;
            top: 0;
            bottom: 0;
            width: 24px;
            opacity: 0.3;
            background: radial-gradient(#10b981 1px, transparent 1px);
            background-size: 4px 4px;
        }

        /* Infos Agent */
        .badge-infos {
            display: flex;
            flex-direction: column;
            gap: 7px;
            padding: 0 4px;
            margin: 6px 0 4px;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 0;
            background: rgba(0,133,63,0.04);
            border-radius: 8px;
            padding: 3px 6px 3px 3px;
            border: 1px solid rgba(0,133,63,0.10);
        }

        .info-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 50%;
            background: #00853f;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,133,63,0.30);
        }

        .info-divider {
            width: 1px;
            height: 20px;
            background: #cbd5e1;
            margin: 0 7px;
            flex-shrink: 0;
        }

        .info-label {
            color: #152a55;
            font-weight: 800;
            font-size: 10px;
            width: 58px;
            flex-shrink: 0;
            letter-spacing: -0.01em;
        }

        .info-sep {
            color: #00853f;
            font-weight: 900;
            font-size: 11px;
            margin: 0 4px 0 0;
            flex-shrink: 0;
        }

        .info-value {
            color: #152a55;
            font-weight: 800;
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .info-value-light {
            font-weight: 600;
        }

        /* Footer Banner */
        .badge-footer {
            background: #0b1f18;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: 700;
            text-align: center;
            padding: 6px 8px;
            border-radius: 0 0 20px 20px;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: -0.01em;
        }

        /* ===== VERSO ===== */
        .badge-verso-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            padding: 14px 16px;
            color: #152a55;
        }

        .verso-top {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .verso-title {
            font-size: 12px;
            font-weight: 800;
            color: #0b1f18;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            text-align: center;
        }

        .verso-subtitle {
            font-size: 9.5px;
            font-weight: 700;
            color: #00853f;
            text-align: center;
            margin-top: 1px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* QR Code Section */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 4px 0;
        }

        .qr-box {
            padding: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .qr-box img {
            width: 160px;
            height: 160px;
            display: block;
        }

        .qr-matricule {
            margin-top: 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 12px;
            padding: 4px 16px;
            border-radius: 12px;
        }

        .qr-instruction {
            text-align: center;
            padding: 0 8px;
            margin-top: 6px;
        }

        .qr-instruction p {
            font-size: 9.5px;
            font-weight: 600;
            color: #475569;
            line-height: 1.4;
        }

        /* Verso Footer */
        .badge-footer-verso {
            background: #0b1f18;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: 700;
            text-align: center;
            padding: 6px 8px;
            border-radius: 0 0 20px 20px;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        /* Bouton Impression */
        .print-btn {
            margin-top: 30px;
            padding: 14px 32px;
            background: #0b1f18;
            color: #fff;
            border: none;
            border-radius: 16px;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transition: 0.2s;
        }

        .print-btn:hover {
            background: #00853f;
            transform: scale(1.02);
        }

        .print-btn i {
            font-size: 18px;
        }

        /* ===================================================
           IMPRESSION — Badge seul, sans aucune légende
           @page en format carte PVC : 85.6mm × 54mm
           Recto page 1, Verso page 2
        =================================================== */
        @media print {
            @page {
                size: 85.6mm 54mm landscape;
                margin: 0;
            }

            html, body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 85.6mm !important;
                height: 54mm !important;
                overflow: hidden !important;
                display: block !important;
                min-height: unset !important;
            }

            /* Masquer TOUT sauf les cartes elles-mêmes */
            .no-print,
            .badge-label,
            .badge-side > span {
                display: none !important;
            }

            .badge-print-container {
                all: unset !important;
                display: block !important;
            }

            .badge-wrapper {
                all: unset !important;
                display: block !important;
            }

            .badge-side {
                all: unset !important;
                display: block !important;
            }

            /* Chaque carte prend exactement la taille de la page */
            .badge-card {
                width: 85.6mm !important;
                height: 54mm !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
                transform: none !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                break-after: page !important;
                position: relative !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }

            .badge-card-verso {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body>

<div class="badge-print-container">

    <!-- Badges Recto & Verso -->
    <div class="badge-wrapper">

        <!-- ===== RECTO ===== -->
        <div class="badge-side">
            <span class="badge-label no-print">
                <i class="fa-regular fa-square-check"></i> RECTO (Face Avant)
            </span>

            <div class="badge-card">
                <!-- 3 Bandes Diagonales -->
                <div class="diagonal-stripes-wrapper">
                    <div class="band-1"></div>
                    <div class="band-2"></div>
                    <div class="band-3"></div>
                </div>

                <!-- Contenu Recto -->
                <div class="badge-recto-content">

                    <!-- Header -->
                    <div class="badge-header">
                        <div class="flag">
                            <div class="flag-inner">
                                <div class="flag-green"></div>
                                <div class="flag-yellow">★</div>
                                <div class="flag-red"></div>
                            </div>
                        </div>
                        <h2 class="badge-ministry badge-title-italic">
                            MINISTÈRE DE LA FONCTION PUBLIQUE, DU TRAVAIL ET DE LA REFORME DU SERVICE PUBLIC
                        </h2>
                    </div>

                    <!-- Photo -->
                    <div class="badge-photo">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%230b1f18' opacity='0.08'/%3E%3Ctext x='50' y='55' font-family='Arial' font-size='12' text-anchor='middle' fill='%230b1f18' opacity='0.15'%3EMFP%3C/text%3E%3C/svg%3E" alt="" class="watermark">
                        <div class="photo-frame">
                            <img src="{{ $agent->photo_url }}" alt="{{ $agent->nom_complet }}">
                        </div>
                    </div>

                    <!-- Pill -->
                    <div class="badge-pill">
                        <span>{{ $agent->poste ?? $agent->fonction ?? $agent->service ?? $agent->structure_affectation ?? 'AGENT DE L\'ÉTAT' }}</span>
                        <div class="dots"></div>
                    </div>

                    <!-- Infos -->
                    <div class="badge-infos">
                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-user-tie"></i></div>
                            <div class="info-divider"></div>
                            <span class="info-label">Agent</span>
                            <span class="info-sep">:</span>
                            <span class="info-value">{{ $agent->nom_complet }}</span>
                        </div>
                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-id-badge"></i></div>
                            <div class="info-divider"></div>
                            <span class="info-label">Matricule</span>
                            <span class="info-sep">:</span>
                            <span class="info-value">{{ $agent->matricule }}</span>
                        </div>
                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-building-columns"></i></div>
                            <div class="info-divider"></div>
                            <span class="info-label">Service</span>
                            <span class="info-sep">:</span>
                            <span class="info-value info-value-light" title="{{ $agent->service ?? $agent->structure_affectation ?? 'Non renseigné' }}">{{ $agent->service ?? $agent->structure_affectation ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-briefcase"></i></div>
                            <div class="info-divider"></div>
                            <span class="info-label">Fonction</span>
                            <span class="info-sep">:</span>
                            <span class="info-value info-value-light" title="{{ $agent->poste ? ($agent->fonction ? $agent->poste . ' / ' . $agent->fonction : $agent->poste) : ($agent->fonction ?? 'Non renseigné') }}">{{ $agent->poste ? ($agent->fonction ? $agent->poste . ' / ' . $agent->fonction : $agent->poste) : ($agent->fonction ?? 'Non renseigné') }}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="badge-footer">
                        CARTE PROFESSIONNELLE / EN CAS DE PERTE, CONTACTEZ LE MINISTERE AU 338396600
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== VERSO (QR CODE) ===== -->
        <div class="badge-side">
            <span class="badge-label no-print">
                <i class="fa-solid fa-qrcode text-amber-600"></i> VERSO (Face Arrière - QR Code)
            </span>

            <div class="badge-card badge-card-verso">
                <!-- 3 Bandes Diagonales (légèrement atténuées au verso) -->
                <div class="diagonal-stripes-wrapper">
                    <div class="band-1" style="opacity:0.25;"></div>
                    <div class="band-2" style="opacity:0.28;"></div>
                    <div class="band-3" style="opacity:0.22;"></div>
                </div>

                <!-- Contenu Verso -->
                <div class="badge-verso-content">

                    <!-- Top Header -->
                    <div class="verso-top">
                        <div class="flag">
                            <div class="flag-inner">
                                <div class="flag-green"></div>
                                <div class="flag-yellow">★</div>
                                <div class="flag-red"></div>
                            </div>
                        </div>
                        <div class="verso-title">RÉPUBLIQUE DU SÉNÉGAL</div>
                        <div class="verso-subtitle">VÉRIFICATION D'AUTHENTICITÉ DE LA CARTE</div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="qr-section">
                        <div class="qr-box">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode($agent->public_url) }}&color=0a5738" alt="QR Code {{ $agent->matricule }}">
                        </div>
                        <div class="qr-matricule">MATRICULE: {{ $agent->matricule }}</div>
                        <div class="qr-instruction">
                            <p>Scannez ce QR Code pour accéder à la fiche numérique officielle et vérifier la validité de la carte professionnelle.</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="badge-footer-verso">
                        DOCUMENT OFFICIEL VÉRIFIÉ / REPUBLIQUE DU SENEGAL
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===== BOUTONS D'ACTION (cachés à l'impression) ===== -->
    <div class="no-print" id="action-bar" style="
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: center;
        margin-top: 32px;
        padding-bottom: 40px;
    ">
        <!-- Retour -->
        <a href="{{ route('admin.agents.index') }}" style="
            padding: 12px 22px;
            background: #475569;
            color: #fff;
            border-radius: 14px;
            text-decoration: none;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        " onmouseover="this.style.background='#334155'" onmouseout="this.style.background='#475569'">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>

        <!-- Télécharger PDF -->
        <button id="btn-pdf" onclick="exportBadge('pdf')" style="
            padding: 13px 26px;
            background: #0b1f18;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            transition: background 0.2s;
        " onmouseover="this.style.background='#00853f'" onmouseout="this.style.background='#0b1f18'">
            <i class="fa-solid fa-file-pdf" style="font-size:16px; color:#f87171;"></i>
            Télécharger PDF
        </button>

        <!-- Télécharger PNG -->
        <button id="btn-png" onclick="exportBadge('png')" style="
            padding: 13px 26px;
            background: #0b1f18;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            transition: background 0.2s;
        " onmouseover="this.style.background='#00853f'" onmouseout="this.style.background='#0b1f18'">
            <i class="fa-solid fa-image" style="font-size:16px; color:#34d399;"></i>
            Télécharger PNG
        </button>

        <!-- Imprimer (dialogue navigateur) -->
        <button onclick="window.print()" style="
            padding: 13px 22px;
            background: #334155;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        " onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#334155'">
            <i class="fa-solid fa-print"></i> Imprimer
        </button>
    </div>

</div><!-- /.badge-print-container -->

<script>
/**
 * Exporte les deux cartes (Recto + Verso) en PDF ou PNG haute résolution.
 * Utilise html2canvas pour capturer chaque carte individuellement.
 */
async function exportBadge(format) {
    const btnPdf = document.getElementById('btn-pdf');
    const btnPng = document.getElementById('btn-png');
    const activeBtn = format === 'pdf' ? btnPdf : btnPng;

    // Feedback visuel
    const originalHTML = activeBtn.innerHTML;
    activeBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Génération…';
    activeBtn.disabled = true;

    // Masquer la barre d'actions pendant la capture
    const actionBar = document.getElementById('action-bar');
    actionBar.style.visibility = 'hidden';

    try {
        const cards = document.querySelectorAll('.badge-card');
        const canvases = [];

        const opts = {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
        };

        for (const card of cards) {
            const canvas = await html2canvas(card, opts);
            canvases.push(canvas);
        }

        const agentName = '{{ addslashes($agent->nom_complet) }}';
        const matricule = '{{ $agent->matricule }}';
        const filename = `Badge_PVC_${matricule}`;

        if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            // Dimensions PVC carte ID1 : 85.6 × 54 mm (paysage)
            const W = 85.6, H = 54;
            const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: [W, H] });

            canvases.forEach((canvas, idx) => {
                if (idx > 0) pdf.addPage([W, H], 'landscape');
                const imgData = canvas.toDataURL('image/png', 1.0);
                pdf.addImage(imgData, 'PNG', 0, 0, W, H);
            });

            pdf.save(`${filename}.pdf`);

        } else {
            // PNG : colle les deux cartes côte à côte
            const scale = 3;
            const gap = 20 * scale;
            const totalW = canvases.reduce((sum, c) => sum + c.width, 0) + gap * (canvases.length - 1);
            const maxH = Math.max(...canvases.map(c => c.height));

            const merged = document.createElement('canvas');
            merged.width = totalW;
            merged.height = maxH;
            const ctx = merged.getContext('2d');
            ctx.fillStyle = '#f1f5f9';
            ctx.fillRect(0, 0, totalW, maxH);

            let x = 0;
            for (const canvas of canvases) {
                ctx.drawImage(canvas, x, 0);
                x += canvas.width + gap;
            }

            const link = document.createElement('a');
            link.download = `${filename}.png`;
            link.href = merged.toDataURL('image/png', 1.0);
            link.click();
        }

    } catch (err) {
        console.error('Erreur export:', err);
        alert('Une erreur est survenue lors de la génération. Veuillez réessayer.');
    } finally {
        actionBar.style.visibility = 'visible';
        activeBtn.innerHTML = originalHTML;
        activeBtn.disabled = false;
    }
}
</script>

</body>
</html>