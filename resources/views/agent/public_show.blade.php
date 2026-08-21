<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fiche Agent - {{ $agent->nom_complet }} (Matricule {{ $agent->matricule }}) | Ministère de la Fonction Publique</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ministry: '#00843F',
                        ministryDark: '#006B34',
                        yellow: '#FCC207',
                        secondary: '#575a7b',
                        green: '#198754'
                    }
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system,
                         BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7f8;
            color: #1e293b;
        }

        /* =========================
           HERO
        ========================= */
        .hero {
            background:
                radial-gradient(
                    circle at 90% 20%,
                    rgba(252,194,7,.16),
                    transparent 25%
                ),
                radial-gradient(
                    circle at 5% 90%,
                    rgba(255,255,255,.08),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #006B34 0%,
                    #00843F 55%,
                    #198754 100%
                );
        }

        /* =========================
           CARDS
        ========================= */
        .info-card {
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .07);
        }

        .quick-card {
            transition: all .2s ease;
        }

        .quick-card:hover {
            transform: translateY(-3px);
        }

        /* =========================
           PHOTO
        ========================= */
        .photo-wrapper {
            background:
                linear-gradient(
                    145deg,
                    rgba(0,132,63,.12),
                    rgba(252,194,7,.15)
                );
        }

        .agent-photo-img {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: high-quality;
            image-rendering: crisp-edges;
            backface-visibility: hidden;
            transform: translateZ(0);
        }

        /* =========================
           MOBILE
        ========================= */
        @media (max-width: 640px) {
            .quick-card:hover,
            .info-card:hover {
                transform: none;
            }
        }
    </style>
</head>

<body>

<!-- =========================================================
     HEADER
========================================================= -->
<header class="bg-white border-b border-slate-100">

    <!-- Bande institutionnelle fine et subtile (Vert, Jaune, Rouge) -->
    <div class="h-[2px] flex opacity-75">
        <div class="flex-1 bg-[#00853f]"></div>
        <div class="flex-1 bg-[#fcd116]"></div>
        <div class="flex-1 bg-[#e31b23]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="h-[68px] flex items-center justify-between">

            <!-- LOGO + NOM -->
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 flex items-center justify-center shrink-0">
                    <img
                        src="{{ asset('images/MFPremove.png') }}"
                        alt="Logo du Ministère"
                        class="max-w-full max-h-full object-contain"
                        onerror="this.src='{{ asset('favicon.png') }}'"
                    >
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[8px] sm:text-[9px] uppercase tracking-[.14em] font-bold text-ministry">
                            République du Sénégal
                        </span>
                    </div>

                    <h1 class="text-[11px] sm:text-sm font-extrabold text-slate-900 leading-tight truncate">
                        Ministère de la Fonction Publique
                    </h1>

                    <p class="text-[9px] sm:text-[11px] text-secondary leading-tight">
                        et de la Réforme du Service Public
                    </p>
                </div>
            </div>

            <!-- Annuaire badge -->
            <div class="hidden md:flex items-center gap-3">
                <div class="text-right">
                    <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">
                        ANNUAIRE
                    </p>
                    <p class="text-xs font-semibold text-secondary">
                        Agents de l'État
                    </p>
                </div>

                <div class="w-10 h-10 rounded-xl bg-ministry/10 flex items-center justify-center">
                    <i data-lucide="contact-round" class="w-5 h-5 text-ministry"></i>
                </div>
            </div>

        </div>
    </div>
</header>

<!-- =========================================================
     HERO
========================================================= -->
<section class="hero relative overflow-hidden">

    <!-- Décorations -->
    <div class="absolute -right-20 -top-24 w-64 h-64 rounded-full border border-white/10"></div>
    <div class="absolute right-10 -top-10 w-32 h-32 rounded-full border border-white/10"></div>
    <div class="absolute -left-20 -bottom-24 w-60 h-60 rounded-full bg-white/5"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
        <div class="py-7 sm:py-9">

            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-white text-[10px] sm:text-xs font-semibold mb-3">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-yellow"></i>
                Fiche de contact officielle
            </div>

            <!-- Titre -->
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                Annuaire des agents <span class="text-yellow">de l'État</span>
            </h2>

            <p class="mt-2 max-w-2xl text-xs sm:text-sm leading-relaxed text-white/70">
                Consultez les informations professionnelles et les coordonnées vérifiées des agents du Ministère de la Fonction Publique et de la Réforme du Service Public.
            </p>

        </div>
    </div>
</section>

<!-- =========================================================
     CONTENU PRINCIPAL
========================================================= -->
<main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <!-- =====================================================
         FICHE AGENT
    ===================================================== -->
    <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

        <!-- Bande supérieure -->
        <div class="h-1 bg-gradient-to-r from-ministry via-green to-yellow"></div>

        <div class="p-5 sm:p-7 lg:p-8">

            <!-- =================================================
                 PROFIL
            ================================================= -->
            <div class="grid grid-cols-1 lg:grid-cols-[auto_1fr_auto] gap-6 lg:gap-8 items-center">

                <!-- PHOTO -->
                <div class="flex justify-center lg:justify-start">
                    <div class="relative">
                        <div class="photo-wrapper w-36 h-36 sm:w-40 sm:h-40 rounded-[28px] p-1">
                            <div class="w-full h-full rounded-[24px] overflow-hidden bg-white">
                                <img
                                    src="{{ $agent->photo_url }}"
                                    alt="Photo de {{ $agent->nom_complet }}"
                                    class="w-full h-full object-cover agent-photo-img"
                                    loading="eager"
                                    decoding="sync"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IDENTITE -->
                <div class="text-center lg:text-left min-w-0">

                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-ministry/10 text-ministry text-[9px] sm:text-[10px] uppercase tracking-wider font-extrabold">
                        <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                        Agent de l'État
                    </div>

                    <h3 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        {{ $agent->nom_complet }}
                    </h3>

                    <!-- Matricule -->
                    <div class="mt-2 flex flex-wrap justify-center lg:justify-start items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-xs font-semibold text-secondary">
                            <i data-lucide="id-card" class="w-3.5 h-3.5"></i>
                            Matricule : {{ $agent->matricule }}
                        </span>
                    </div>

                    <!-- Fonction (Corps de métier) -->
                    @if($agent->fonction)
                        <p class="mt-3 text-sm font-semibold text-secondary">
                            {{ $agent->fonction }}
                        </p>
                    @endif

                    <!-- Poste (Titre / Responsabilité) -->
                    @if($agent->poste)
                        <div class="mt-2 inline-flex items-center gap-2 max-w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                            <i data-lucide="layers-3" class="w-4 h-4 text-ministry shrink-0"></i>
                            <span class="text-xs sm:text-sm font-bold text-slate-700">
                                {{ $agent->poste }}
                            </span>
                        </div>
                    @endif

                </div>

                <!-- ACTIONS PRINCIPALES -->
                <div class="grid grid-cols-3 lg:grid-cols-1 gap-2 w-full lg:w-44">

                    <!-- Enregistrer Contact (vCard) -->
                    <a
                        href="{{ route('agent.vcard', $agent->uuid) }}"
                        class="group flex items-center justify-center gap-2 rounded-xl bg-ministry px-3 py-3 text-white text-xs font-bold shadow-md shadow-ministry/20 hover:bg-ministryDark transition"
                    >
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span class="hidden sm:inline lg:inline">Enregistrer</span>
                    </a>

                    <!-- Message modal -->
                    <button
                        onclick="openMessage()"
                        class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-3 text-secondary text-xs font-bold hover:bg-slate-50 transition"
                    >
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        <span class="hidden sm:inline lg:inline">Message</span>
                    </button>

                    <!-- Partager -->
                    <button
                        onclick="shareContact()"
                        class="flex items-center justify-center gap-2 rounded-xl border border-ministry/20 bg-ministry/5 px-3 py-3 text-ministry text-xs font-bold hover:bg-ministry/10 transition"
                    >
                        <i data-lucide="share-2" class="w-4 h-4"></i>
                        <span class="hidden sm:inline lg:inline">Partager</span>
                    </button>

                </div>

            </div>

            <!-- =================================================
                 SEPARATEUR
            ================================================= -->
            <div class="my-8 border-t border-slate-100"></div>

            <!-- =================================================
                 TITRE INFORMATIONS
            ================================================= -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-5">
                <div>
                    <p class="text-[10px] uppercase tracking-[.16em] font-extrabold text-ministry">
                        Profil professionnel
                    </p>
                    <h4 class="mt-1 text-xl font-extrabold text-slate-900">
                        Coordonnées et affectation
                    </h4>
                </div>

                <span class="text-[10px] text-slate-400">
                    Informations professionnelles
                </span>
            </div>

            <!-- =================================================
                 INFORMATIONS GRID
            ================================================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <!-- DEPARTEMENT / SERVICE -->
                <div class="info-card rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="flex gap-3">
                        <div class="w-11 h-11 rounded-xl bg-ministry/10 flex items-center justify-center shrink-0">
                            <i data-lucide="building-2" class="w-5 h-5 text-ministry"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                Direction / Service
                            </p>
                            <p class="mt-1 text-sm font-bold leading-relaxed text-slate-800">
                                {{ $agent->service ?? ($agent->structure_affectation ?? 'Non renseigné') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FONCTION -->
                <div class="info-card rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="flex gap-3">
                        <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center shrink-0">
                            <i data-lucide="briefcase-business" class="w-5 h-5 text-secondary"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                Fonction (Métier)
                            </p>
                            <p class="mt-1 text-sm font-bold leading-relaxed text-slate-800">
                                {{ $agent->fonction ?? 'Non renseigné' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- POSTE -->
                <div class="info-card rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="flex gap-3">
                        <div class="w-11 h-11 rounded-xl bg-yellow/20 flex items-center justify-center shrink-0">
                            <i data-lucide="layers-3" class="w-5 h-5 text-yellow-700"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                Poste
                            </p>
                            <p class="mt-1 text-sm font-bold leading-relaxed text-slate-800">
                                {{ $agent->poste ?? 'Non renseigné' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TELEPHONE -->
                @if($agent->telephone)
                    <a
                        href="tel:{{ $agent->telephone }}"
                        class="info-card block rounded-2xl border border-slate-200 bg-white p-4 hover:border-green/40"
                    >
                        <div class="flex gap-3">
                            <div class="w-11 h-11 rounded-xl bg-green/10 flex items-center justify-center shrink-0">
                                <i data-lucide="phone" class="w-5 h-5 text-green"></i>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                    Téléphone
                                </p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $agent->telephone }}
                                </p>
                            </div>
                        </div>
                    </a>
                @else
                    <div class="info-card rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                        <div class="flex gap-3">
                            <div class="w-11 h-11 rounded-xl bg-slate-200 flex items-center justify-center shrink-0">
                                <i data-lucide="phone" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                    Téléphone
                                </p>
                                <p class="mt-1 text-sm font-bold text-slate-400">
                                    Non renseigné
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- EMAIL -->
                @if($agent->email)
                    <a
                        href="mailto:{{ $agent->email }}"
                        class="info-card block rounded-2xl border border-slate-200 bg-white p-4 hover:border-secondary/40"
                    >
                        <div class="flex gap-3">
                            <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center shrink-0">
                                <i data-lucide="mail" class="w-5 h-5 text-secondary"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                    Email professionnel
                                </p>
                                <p class="mt-1 text-sm font-bold text-slate-800 break-all">
                                    {{ $agent->email }}
                                </p>
                            </div>
                        </div>
                    </a>
                @else
                    <div class="info-card rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                        <div class="flex gap-3">
                            <div class="w-11 h-11 rounded-xl bg-slate-200 flex items-center justify-center shrink-0">
                                <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                    Email professionnel
                                </p>
                                <p class="mt-1 text-sm font-bold text-slate-400">
                                    Non renseigné
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ADMINISTRATION / MINISTERE -->
                <div class="info-card rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="flex gap-3">
                        <div class="w-11 h-11 rounded-xl bg-yellow/20 flex items-center justify-center shrink-0">
                            <i data-lucide="landmark" class="w-5 h-5 text-yellow-700"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">
                                Structure / Ministère
                            </p>
                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $agent->structure_affectation ?? 'Ministère de la Fonction Publique' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- =================================================
                 SERVICES RAPIDES
            ================================================= -->
            <div class="mt-9 pt-8 border-t border-slate-100">

                <div class="mb-5">
                    <p class="text-[10px] uppercase tracking-[.16em] font-extrabold text-ministry">
                        Actions rapides
                    </p>
                    <h4 class="mt-1 text-xl font-extrabold text-slate-900">
                        Contacter cet agent
                    </h4>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">
                        Utilisez l'une des options ci-dessous pour contacter ou enregistrer la fiche de l'agent.
                    </p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                    <!-- APPELER -->
                    @if($agent->telephone)
                        <a
                            href="tel:{{ $agent->telephone }}"
                            class="quick-card group rounded-2xl border border-slate-200 bg-white p-4 hover:border-ministry hover:shadow-lg hover:shadow-ministry/10"
                        >
                            <div class="flex items-center justify-between">
                                <div class="w-11 h-11 rounded-xl bg-ministry/10 flex items-center justify-center group-hover:bg-ministry transition">
                                    <i data-lucide="phone-call" class="w-5 h-5 text-ministry group-hover:text-white"></i>
                                </div>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-300 group-hover:text-ministry"></i>
                            </div>
                            <p class="mt-4 text-sm font-extrabold text-slate-800 group-hover:text-ministry">
                                Appeler
                            </p>
                            <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                                {{ $agent->telephone }}
                            </p>
                        </a>
                    @else
                        <div class="quick-card opacity-60 rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="phone-off" class="w-5 h-5 text-slate-400"></i>
                                </div>
                            </div>
                            <p class="mt-4 text-sm font-bold text-slate-500">Appeler</p>
                            <p class="mt-1 text-[10px] text-slate-400">Numéro non disponible</p>
                        </div>
                    @endif

                    <!-- EMAIL -->
                    @if($agent->email)
                        <a
                            href="mailto:{{ $agent->email }}"
                            class="quick-card group rounded-2xl border border-slate-200 bg-white p-4 hover:border-secondary hover:shadow-lg"
                        >
                            <div class="flex items-center justify-between">
                                <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center group-hover:bg-secondary transition">
                                    <i data-lucide="mail" class="w-5 h-5 text-secondary group-hover:text-white"></i>
                                </div>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-300 group-hover:text-secondary"></i>
                            </div>
                            <p class="mt-4 text-sm font-extrabold text-slate-800 group-hover:text-secondary">
                                Envoyer un email
                            </p>
                            <p class="mt-1 text-[10px] leading-relaxed text-slate-500 truncate">
                                {{ $agent->email }}
                            </p>
                        </a>
                    @else
                        <div class="quick-card opacity-60 rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center">
                                    <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
                                </div>
                            </div>
                            <p class="mt-4 text-sm font-bold text-slate-500">Envoyer un email</p>
                            <p class="mt-1 text-[10px] text-slate-400">Email non disponible</p>
                        </div>
                    @endif

                    <!-- ENREGISTRER (VCARD) -->
                    <a
                        href="{{ route('agent.vcard', $agent->uuid) }}"
                        class="quick-card group text-left rounded-2xl border border-slate-200 bg-white p-4 hover:border-yellow hover:shadow-lg block"
                    >
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-xl bg-yellow/20 flex items-center justify-center group-hover:bg-yellow transition">
                                <i data-lucide="contact" class="w-5 h-5 text-yellow-700"></i>
                            </div>
                            <i data-lucide="download" class="w-4 h-4 text-slate-300 group-hover:text-yellow-700"></i>
                        </div>
                        <p class="mt-4 text-sm font-extrabold text-slate-800 group-hover:text-yellow-700">
                            Enregistrer
                        </p>
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                            Ajouter aux contacts (.vcf)
                        </p>
                    </a>

                    <!-- PARTAGER -->
                    <button
                        onclick="shareContact()"
                        class="quick-card group text-left rounded-2xl border border-slate-200 bg-white p-4 hover:border-ministry hover:shadow-lg hover:shadow-ministry/10"
                    >
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-xl bg-ministry/10 flex items-center justify-center group-hover:bg-ministry transition">
                                <i data-lucide="share-2" class="w-5 h-5 text-ministry group-hover:text-white"></i>
                            </div>
                            <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-300 group-hover:text-ministry"></i>
                        </div>
                        <p class="mt-4 text-sm font-extrabold text-slate-800 group-hover:text-ministry">
                            Partager
                        </p>
                        <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                            Partager cette fiche
                        </p>
                    </button>

                </div>

            </div>

        </div>

    </section>

</main>

<!-- =========================================================
     FOOTER
========================================================= -->
<footer class="bg-white border-t border-slate-200 mt-12">

    <!-- Bande institutionnelle fine et subtile (Vert, Jaune, Rouge) -->
    <div class="h-[2px] flex opacity-75">
        <div class="flex-1 bg-[#00853f]"></div>
        <div class="flex-1 bg-[#fcd116]"></div>
        <div class="flex-1 bg-[#e31b23]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-ministry/10 flex items-center justify-center">
                    <i data-lucide="landmark" class="w-4 h-4 text-ministry"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">
                        Ministère de la Fonction Publique
                    </p>
                    <p class="text-[10px] text-slate-400">
                        et de la Réforme du Service Public
                    </p>
                </div>
            </div>

            <div class="text-center sm:text-right">
                <p class="text-[10px] text-slate-400">
                    République du Sénégal
                </p>
                <p class="mt-0.5 text-[10px] text-slate-400">
                    © {{ date('Y') }} — Tous droits réservés
                </p>
            </div>

        </div>
    </div>
</footer>

<!-- =========================================================
     MODAL MESSAGE
========================================================= -->
<div
    id="messageModal"
    class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm items-center justify-center p-4"
>
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">

        <!-- ENTETE MODAL -->
        <div class="hero relative px-6 py-6">
            <button
                onclick="closeMessage()"
                class="absolute right-4 top-4 w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center"
            >
                <i data-lucide="x" class="w-4 h-4 text-white"></i>
            </button>

            <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i data-lucide="message-circle" class="w-5 h-5 text-white"></i>
            </div>

            <p class="mt-4 text-[9px] uppercase tracking-widest font-bold text-white/60">
                Contact
            </p>

            <h3 class="mt-1 text-xl font-extrabold text-white">
                Envoyer un message
            </h3>

            <p class="mt-1 text-xs text-white/70">
                À {{ $agent->nom_complet }}
            </p>
        </div>

        <!-- FORMULAIRE -->
        <div class="p-6">
            <label class="block mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                Votre message
            </label>

            <textarea
                id="messageText"
                rows="5"
                placeholder="Saisissez votre message..."
                class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm outline-none focus:bg-white focus:border-ministry focus:ring-4 focus:ring-ministry/10 transition"
            ></textarea>

            <button
                onclick="sendMessage()"
                class="mt-4 w-full py-3.5 rounded-xl bg-ministry hover:bg-ministryDark text-white font-bold flex items-center justify-center gap-2 transition shadow-lg shadow-ministry/20"
            >
                <i data-lucide="send" class="w-4 h-4"></i>
                Envoyer le message
            </button>
        </div>

    </div>
</div>

<!-- =========================================================
     JAVASCRIPT
========================================================= -->
<script>
    lucide.createIcons();

    /* ============================
       PARTAGER
    ============================ */
    async function shareContact() {
        const shareData = {
            title: "{{ addslashes($agent->nom_complet) }}",
            text: "Fiche de contact de {{ addslashes($agent->nom_complet) }} (Matricule: {{ $agent->matricule }}) - Ministère de la Fonction Publique",
            url: window.location.href
        };

        if (navigator.share) {
            try {
                await navigator.share(shareData);
            } catch (error) {
                if (error.name !== "AbortError") {
                    console.error(error);
                }
            }
            return;
        }

        try {
            await navigator.clipboard.writeText(window.location.href);
            showToast("Lien de la fiche copié !");
        } catch (error) {
            prompt("Copiez le lien de cette fiche :", window.location.href);
        }
    }

    /* ============================
       MODAL
    ============================ */
    function openMessage() {
        const modal = document.getElementById("messageModal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        document.body.style.overflow = "hidden";
        lucide.createIcons();
    }

    function closeMessage() {
        const modal = document.getElementById("messageModal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        document.body.style.overflow = "";
    }

    /* ============================
       ENVOYER MESSAGE
    ============================ */
    function sendMessage() {
        const input = document.getElementById("messageText");
        const message = input.value.trim();

        if (!message) {
            showToast("Veuillez saisir votre message.");
            return;
        }

        @if($agent->email)
            window.location.href = "mailto:{{ $agent->email }}?subject=" + encodeURIComponent("Message concernant la fiche agent {{ $agent->matricule }}") + "&body=" + encodeURIComponent(message);
        @else
            showToast("Message préparé avec succès !");
        @endif

        input.value = "";
        closeMessage();
    }

    /* ============================
       TOAST
    ============================ */
    function showToast(message) {
        const oldToast = document.getElementById("toast");
        if (oldToast) {
            oldToast.remove();
        }

        const toast = document.createElement("div");
        toast.id = "toast";
        toast.className =
            "fixed bottom-5 left-1/2 -translate-x-1/2 z-[200] " +
            "bg-slate-900 text-white px-5 py-3 rounded-xl " +
            "shadow-xl text-xs sm:text-sm font-semibold " +
            "flex items-center gap-2";

        toast.innerHTML = `
            <span class="w-2 h-2 rounded-full bg-green"></span>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transition = "opacity .3s";
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 2500);
    }

    /* ============================
       EVENEMENTS MODAL
    ============================ */
    document.getElementById("messageModal").addEventListener("click", function(event) {
        if (event.target === this) {
            closeMessage();
        }
    });

    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeMessage();
        }
    });
</script>

</body>
</html>
