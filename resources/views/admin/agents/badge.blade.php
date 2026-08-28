<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte Professionnelle — {{ $agent->nom_complet }} ({{ $agent->matricule }})</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;1,700;1,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            /* Palette officielle du template implementation_carte */
            --flag-green: #00853F;
            --flag-yellow: #FDEF42;
            --flag-red: #E31B23;
            --navy: #142850;
            --navy-strong: #0f1f3d;
            --dark-band: #0b1a13;
            --dark-band-2: #12241b;
            --icon-green: #1f8a54;
            --pale-green: #bfe0cd;
            --pale-green-soft: #dcf0e4;
            --divider-green: #a9d6bc;
            --photo-border: #262b28;
            --card-border-color: #d9dcd8;

            --font-display: 'Poppins', 'Montserrat', Arial, sans-serif;
            --font-body: 'Inter', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: #eef1ee;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 32px 16px 64px;
        }

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
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-label i {
            color: var(--icon-green);
        }

        /* ===== CARTE PVC (Dimensions proportionnelles 380px x 535px) ===== */
        .card {
            position: relative;
            width: 380px;
            height: 535px;
            background: #ffffff;
            border: 1.5px solid var(--card-border-color);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(20, 40, 30, 0.14);
            font-family: var(--font-display);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            user-select: none;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* ===== DÉCOR : RUBAN DIAGONAL TRICOLORE SÉNÉGAL ===== */
        .senegal-ribbon {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 0;
            width: 900px;
            height: calc(5px * 3 + 1.5px * 2);
            display: flex;
            flex-direction: column;
            gap: 1.5px;
            transform: translate(-50%, -50%) rotate(54.6deg);
            transform-origin: center;
            opacity: 1;
            filter: drop-shadow(0 2px 3px rgba(20, 40, 30, 0.10));
            pointer-events: none;
        }

        .senegal-ribbon span {
            display: block;
            width: 100%;
            height: 5px;
            border-radius: 1px;
        }

        .stripe-green  { background: #00853F; }
        .stripe-yellow { background: #FDEF42; }
        .stripe-red    { background: #E2596B; }

        /* ===== DÉCOR : MOTIFS DE POINTS PÂLES ===== */
        .dot-pattern {
            position: absolute;
            z-index: 0;
            width: 120px;
            height: 120px;
            background-image: radial-gradient(var(--pale-green) 1.5px, transparent 1.6px);
            background-size: 10px 10px;
            opacity: 0.45;
            pointer-events: none;
        }

        .dot-pattern--left {
            left: -40px;
            top: 310px;
            -webkit-mask-image: radial-gradient(circle at 30% 30%, black 55%, transparent 78%);
            mask-image: radial-gradient(circle at 30% 30%, black 55%, transparent 78%);
        }

        .dot-pattern--right {
            right: -45px;
            top: 380px;
            -webkit-mask-image: radial-gradient(circle at 70% 40%, black 55%, transparent 78%);
            mask-image: radial-gradient(circle at 70% 40%, black 55%, transparent 78%);
        }

        /* ===== DÉCOR : FILIGRANE CIRCULAIRE ===== */
        .watermark {
            position: absolute;
            z-index: 0;
            top: 98px;
            left: 50%;
            width: 180px;
            height: 180px;
            transform: translateX(-50%);
            border-radius: 50%;
            opacity: 0.55;
            pointer-events: none;
            background:
                radial-gradient(circle at 30% 32%, rgba(0, 133, 63, 0.10), transparent 42%),
                radial-gradient(circle at 68% 30%, rgba(253, 200, 66, 0.12), transparent 40%),
                radial-gradient(circle at 50% 68%, rgba(230, 60, 76, 0.09), transparent 45%);
            border: 1px solid rgba(20, 40, 40, 0.06);
        }

        .watermark::before {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: 50%;
            border: 1px dashed rgba(20, 40, 40, 0.10);
        }

        /* ===== 1. DRAPEAU ===== */
        .flag {
            position: relative;
            z-index: 2;
            display: flex;
            width: 95px;
            height: 10px;
            margin: 14px auto 0;
            border-radius: 1.5px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.12);
        }

        .flag-stripe {
            flex: 1;
            position: relative;
        }

        .flag-stripe--green {
            background: var(--flag-green);
        }

        .flag-stripe--yellow {
            background: var(--flag-yellow);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .flag-stripe--red {
            background: var(--flag-red);
        }

        .flag-star {
            color: var(--flag-green);
            font-size: 7px;
            line-height: 1;
            transform: translateY(-0.3px);
        }

        /* ===== 2. TITRE DU MINISTÈRE ===== */
        .ministry-title {
            position: relative;
            z-index: 2;
            margin: 8px auto 0;
            padding: 0 18px;
            text-align: center;
            color: var(--navy);
            font-size: 10.5px;
            font-weight: 800;
            font-style: italic;
            line-height: 1.35;
            letter-spacing: 0.1px;
            text-transform: uppercase;
        }

        /* ===== 5. PHOTO DE PROFIL ===== */
        .photo-wrap {
            position: relative;
            z-index: 2;
            width: 130px;
            height: 145px;
            margin: 12px auto 0;
            border-radius: 18px;
            border: 2px solid var(--photo-border);
            background: #eef1ee;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(20, 30, 25, 0.15);
        }

        .photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            display: block;
        }

        /* ===== 6. BANDEAU DU POSTE ===== */
        .position-banner {
            position: relative;
            z-index: 2;
            width: 86%;
            margin: 12px auto 0;
            padding: 7px 14px;
            background: linear-gradient(135deg, var(--dark-band), var(--dark-band-2));
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(11, 26, 19, 0.22);
        }

        .position-text {
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 700;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            text-align: center;
        }

        .banner-dots {
            flex-shrink: 0;
            width: 32px;
            height: 100%;
            min-height: 12px;
            background-image: radial-gradient(rgba(60, 200, 130, 0.85) 1.2px, transparent 1.4px);
            background-size: 5px 5px;
            opacity: 0.8;
        }

        /* ===== 7. INFORMATIONS DE L'AGENT ===== */
        .info-list {
            position: relative;
            z-index: 2;
            list-style: none;
            margin: 10px 0 0;
            padding: 0 24px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 24px 10px 72px 1fr;
            align-items: center;
            column-gap: 6px;
            padding: 5px 0;
        }

        .info-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--icon-green);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(31, 138, 84, 0.35);
        }

        .info-icon svg {
            width: 12px;
            height: 12px;
        }

        .info-divider {
            width: 1.5px;
            height: 18px;
            background: var(--divider-green);
            border-radius: 2px;
            justify-self: center;
        }

        .info-label {
            color: var(--navy);
            font-weight: 800;
            font-size: 9.5px;
            white-space: nowrap;
        }

        .info-label::after {
            content: " :";
        }

        .info-colon {
            display: none;
        }

        .info-value {
            color: var(--navy-strong);
            font-weight: 600;
            font-size: 9.5px;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== 8. PIED DE CARTE ===== */
        .card-footer {
            position: relative;
            left: 0;
            right: 0;
            bottom: 0;
            height: 32px;
            background: var(--dark-band);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            margin-top: 8px;
        }

        .card-footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--icon-green), #57d191, var(--icon-green));
        }

        .card-footer p {
            margin: 0;
            padding: 0 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: 7.5px;
            letter-spacing: 0.2px;
            text-transform: uppercase;
            text-align: center;
            white-space: normal;
        }

        /* ===== VERSO (FACE ARRIÈRE) ===== */
        .card-verso {
            background: #ffffff;
        }

        .verso-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .verso-header {
            text-align: center;
            padding-top: 14px;
        }

        .verso-title {
            color: var(--navy);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        .verso-subtitle {
            color: var(--icon-green);
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 2px;
        }

        .verso-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 6px 20px;
        }

        .qr-frame {
            padding: 8px;
            background: #ffffff;
            border: 1.5px solid var(--card-border-color);
            border-radius: 16px;
            box-shadow: 0 6px 16px rgba(20, 40, 30, 0.08);
        }

        .qr-frame img {
            width: 145px;
            height: 145px;
            display: block;
        }

        .matricule-pill {
            margin-top: 10px;
            background: linear-gradient(135deg, var(--dark-band), var(--dark-band-2));
            color: #ffffff;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 10.5px;
            padding: 4px 18px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 8px rgba(11, 26, 19, 0.2);
        }

        .verso-instruction {
            text-align: center;
            margin-top: 8px;
            padding: 0 10px;
        }

        .verso-instruction p {
            font-size: 8px;
            font-weight: 600;
            color: #64748b;
            line-height: 1.35;
        }

        /* ===== IMPRESSION ===== */
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
            }

            .no-print,
            .badge-label {
                display: none !important;
            }

            .badge-print-container,
            .badge-wrapper,
            .badge-side {
                all: unset !important;
                display: block !important;
            }

            .card {
                width: 85.6mm !important;
                height: 54mm !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
                transform: none !important;
                margin: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .card-verso {
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

        <!-- ===== RECTO (Face Avant) ===== -->
        <div class="badge-side">
            <span class="badge-label no-print">
                <i class="fa-regular fa-square-check"></i> RECTO (Face Avant)
            </span>

            <div class="card" id="badge-recto">
                <!-- Décor : ruban diagonal tricolore Sénégal -->
                <div class="senegal-ribbon" aria-hidden="true">
                    <span class="stripe-red"></span>
                    <span class="stripe-yellow"></span>
                    <span class="stripe-green"></span>
                </div>

                <!-- Décor : motifs de points pâles -->
                <div class="dot-pattern dot-pattern--left" aria-hidden="true"></div>
                <div class="dot-pattern dot-pattern--right" aria-hidden="true"></div>

                <!-- Décor : filigrane circulaire derrière la photo -->
                <div class="watermark" aria-hidden="true"></div>

                <div style="position:relative; z-index:2; display:flex; flex-direction:column; height:100%; justify-content:space-between;">
                    
                    <div>
                        <!-- 1. Drapeau du Sénégal -->
                        <div class="flag" role="img" aria-label="Drapeau du Sénégal">
                            <span class="flag-stripe flag-stripe--green"></span>
                            <span class="flag-stripe flag-stripe--yellow">
                                <span class="flag-star">&#9733;</span>
                            </span>
                            <span class="flag-stripe flag-stripe--red"></span>
                        </div>

                        <!-- 2. Titre du ministère -->
                        <h1 class="ministry-title">
                            MINISTÈRE DE LA FONCTION PUBLIQUE,<br />
                            DU TRAVAIL ET DE LA REFORME<br />
                            DU SERVICE PUBLIC
                        </h1>

                        <!-- 5. Photo de profil -->
                        <div class="photo-wrap">
                            <img class="photo" src="{{ $agent->photo_url }}" alt="Photo de {{ $agent->nom_complet }}">
                        </div>

                        <!-- 6. Bandeau du poste occupé -->
                        <div class="position-banner">
                            <span class="position-text">{{ $agent->poste ?? $agent->fonction ?? 'Agent de l\'État' }}</span>
                            <span class="banner-dots" aria-hidden="true"></span>
                        </div>

                        <!-- 7. Informations de l'agent -->
                        <ul class="info-list">
                            <li class="info-row">
                                <span class="info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="4"/>
                                        <path d="M4 20c0-4.4 3.6-7 8-7s8 2.6 8 7"/>
                                    </svg>
                                </span>
                                <span class="info-divider"></span>
                                <span class="info-label">Agent</span>
                                <span class="info-value">{{ $agent->nom_complet }}</span>
                            </li>

                            <li class="info-row">
                                <span class="info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                                        <circle cx="8.5" cy="10.5" r="1.5"/>
                                        <path d="M6 15c.6-1.4 1.8-2 2.5-2s1.9.6 2.5 2M14 9h4M14 13h4M14 16h2.5"/>
                                    </svg>
                                </span>
                                <span class="info-divider"></span>
                                <span class="info-label">Matricule</span>
                                <span class="info-value">{{ $agent->matricule }}</span>
                            </li>

                            <li class="info-row">
                                <span class="info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="12" rx="1.5"/>
                                        <path d="M8 20h8M12 16v4"/>
                                    </svg>
                                </span>
                                <span class="info-divider"></span>
                                <span class="info-label">Service</span>
                                <span class="info-value" title="{{ $agent->service ?? $agent->structure_affectation ?? 'Non renseigné' }}">{{ $agent->service ?? ($agent->structure_affectation ?? 'Non renseigné') }}</span>
                            </li>

                            <li class="info-row">
                                <span class="info-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 10l8-5 8 5"/>
                                        <path d="M5 10v9M9 10v9M15 10v9M19 10v9"/>
                                        <path d="M3 19h18"/>
                                    </svg>
                                </span>
                                <span class="info-divider"></span>
                                <span class="info-label">Fonction</span>
                                <span class="info-value" title="{{ $agent->fonction ?? 'Non renseigné' }}">{{ $agent->fonction ?? 'Non renseigné' }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- 8. Pied de carte -->
                    <footer class="card-footer">
                        <p>Carte professionnelle / En cas de perte, contactez le ministère au 338396600</p>
                    </footer>

                </div>
            </div>
        </div>

        <!-- ===== VERSO (Face Arrière - QR Code) ===== -->
        <div class="badge-side">
            <span class="badge-label no-print">
                <i class="fa-solid fa-qrcode text-emerald-700"></i> VERSO (Face Arrière)
            </span>

            <div class="card card-verso" id="badge-verso">
                <!-- Décor : bande diagonale tricolore (légèrement atténuée) -->
                <div class="stripe-decoration" style="opacity: 0.5;" aria-hidden="true"></div>

                <!-- Décor : motifs de points pâles -->
                <div class="dot-pattern dot-pattern--left" style="top: 140px; left: -50px;" aria-hidden="true"></div>
                <div class="dot-pattern dot-pattern--right" style="top: 260px; right: -50px;" aria-hidden="true"></div>

                <div class="verso-content">
                    
                    <div>
                        <!-- Header Verso -->
                        <div class="verso-header">
                            <div class="flag" role="img" aria-label="Drapeau du Sénégal">
                                <span class="flag-stripe flag-stripe--green"></span>
                                <span class="flag-stripe flag-stripe--yellow">
                                    <span class="flag-star">★</span>
                                </span>
                                <span class="flag-stripe flag-stripe--red"></span>
                            </div>
                            <h2 class="verso-title">RÉPUBLIQUE DU SÉNÉGAL</h2>
                            <p class="verso-subtitle">VÉRIFICATION D'AUTHENTICITÉ DE LA CARTE</p>
                        </div>

                        <!-- Corps du Verso (QR Code & Matricule) -->
                        <div class="verso-body">
                            <div class="qr-frame">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode($agent->public_url) }}&color=0b1a13" alt="QR Code {{ $agent->matricule }}">
                            </div>
                            <div class="matricule-pill">
                                MATRICULE : {{ $agent->matricule }}
                            </div>
                            <div class="verso-instruction">
                                <p>Scannez ce QR Code pour accéder à la fiche numérique officielle et vérifier la validité de la carte professionnelle.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pied de carte Verso -->
                    <footer class="card-footer">
                        <p>DOCUMENT OFFICIEL VÉRIFIÉ / RÉPUBLIQUE DU SÉNÉGAL</p>
                    </footer>

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
            font-family: var(--font-display);
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
            background: var(--dark-band);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            transition: background 0.2s;
        " onmouseover="this.style.background='var(--icon-green)'" onmouseout="this.style.background='var(--dark-band)'">
            <i class="fa-solid fa-file-pdf" style="font-size:16px; color:#f87171;"></i>
            Télécharger PDF
        </button>

        <!-- Télécharger PNG -->
        <button id="btn-png" onclick="exportBadge('png')" style="
            padding: 13px 26px;
            background: var(--dark-band);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            transition: background 0.2s;
        " onmouseover="this.style.background='var(--icon-green)'" onmouseout="this.style.background='var(--dark-band)'">
            <i class="fa-solid fa-image" style="font-size:16px; color:#34d399;"></i>
            Télécharger PNG
        </button>

        <!-- Imprimer -->
        <button id="btn-print" onclick="window.print()" style="
            padding: 13px 26px;
            background: linear-gradient(135deg, #00853f, #059669);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(0,133,63,0.25);
            transition: opacity 0.2s;
        " onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <i class="fa-solid fa-print" style="font-size:16px; color:#fff;"></i>
            Imprimer la carte
        </button>
    </div>

</div>

<script>
async function exportBadge(format) {
    const btnPdf = document.getElementById('btn-pdf');
    const btnPng = document.getElementById('btn-png');
    btnPdf.disabled = true;
    btnPng.disabled = true;

    try {
        const rectoEl = document.getElementById('badge-recto');
        const versoEl = document.getElementById('badge-verso');

        const options = {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff'
        };

        const canvasRecto = await html2canvas(rectoEl, options);
        const canvasVerso = await html2canvas(versoEl, options);

        if (format === 'png') {
            const linkRecto = document.createElement('a');
            linkRecto.download = 'badge_{{ $agent->matricule }}_recto.png';
            linkRecto.href = canvasRecto.toDataURL('image/png');
            linkRecto.click();

            setTimeout(() => {
                const linkVerso = document.createElement('a');
                linkVerso.download = 'badge_{{ $agent->matricule }}_verso.png';
                linkVerso.href = canvasVerso.toDataURL('image/png');
                linkVerso.click();
            }, 500);
        } else if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: [85.6, 54] // Format badge standard PVC
            });

            const imgRecto = canvasRecto.toDataURL('image/png');
            pdf.addImage(imgRecto, 'PNG', 0, 0, 54, 85.6);

            pdf.addPage([85.6, 54], 'portrait');
            const imgVerso = canvasVerso.toDataURL('image/png');
            pdf.addImage(imgVerso, 'PNG', 0, 0, 54, 85.6);

            pdf.save('carte_professionnelle_{{ $agent->matricule }}.pdf');
        }
    } catch (e) {
        console.error(e);
        alert('Une erreur est survenue lors de l\'exportation.');
    } finally {
        btnPdf.disabled = false;
        btnPng.disabled = false;
    }
}
</script>

</body>
</html>