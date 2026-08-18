<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-100 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Agent - {{ $agent->nom_complet }} (Matricule {{ $agent->matricule }})</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            800: '#92400e',
                        },
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 2px solid #059669 !important; background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-amber-50/30 to-emerald-50/40 flex flex-col justify-between items-center p-4 sm:p-6 font-sans">

    <!-- Top Security Status Bar -->
    <div class="w-full max-w-lg mb-6 no-print">
        <div class="bg-white/90 border border-emerald-200 rounded-2xl p-3.5 flex items-center justify-between shadow-md backdrop-blur-md">
            <div class="flex items-center space-x-3">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-600"></span>
                </span>
                <span class="text-xs font-extrabold tracking-wide text-emerald-800 uppercase">Fiche Officielle Vérifiée</span>
            </div>
            <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-full border border-amber-300 shadow-xs">Lecture seule</span>
        </div>
    </div>

    <!-- Agent Digital Card -->
    <div id="fiche-agent-card" class="w-full max-w-lg print-card bg-white border border-slate-200/80 rounded-3xl shadow-2xl overflow-hidden relative">
        
        <!-- Header Banner (Vert profond raffiné) -->
        <div class="relative overflow-hidden" style="background-color: #052e16; height: 9rem;">
            <!-- Subtle dot-pattern texture overlay -->
            <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 18px 18px;"></div>
            <!-- Very subtle inner vignette -->
            <div class="absolute inset-0" style="background: radial-gradient(ellipse at 60% 50%, #064e3b88 0%, #052e16cc 100%);"></div>

            <!-- Bottom gold accent line — two-tone, no orange -->
            <div class="absolute bottom-0 left-0 right-0 flex" style="height: 3px;">
                <div class="flex-1" style="background: linear-gradient(90deg, #92400e, #d97706);"></div>
                <div class="flex-1" style="background: linear-gradient(90deg, #d97706, #fbbf24);"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 h-full px-6 pt-6 flex justify-between items-start">
                <div>
                    <span style="font-size: 9px; letter-spacing: 0.18em;" class="font-bold uppercase text-emerald-300 block mb-1.5"></span>
                    <span class="text-[11px] font-extrabold tracking-widest text-amber-200 uppercase border border-amber-500/40 px-3 py-1 rounded-lg" style="background: rgba(0,0,0,0.25); backdrop-filter: blur(4px);">
                        Carte d'Agent Numérique
                    </span>
                </div>

            </div>
        </div>

        <!-- Agent Photo & Badge Info -->
        <div class="px-6 pb-6 pt-0 relative">
            
            <!-- Avatar + Matricule — layout vertical responsive -->
            <div class="flex items-end justify-between -mt-16 mb-1">
                <!-- Photo -->
                <div class="relative group flex-shrink-0">
                    <!-- Anneau extérieur vert profond -->
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl shadow-2xl overflow-hidden" style="padding: 3px; background-color: #052e16;">
                        <!-- Anneau intérieur doré -->
                        <div class="w-full h-full rounded-[13px] overflow-hidden" style="padding: 2px; background-color: #d97706;">
                            <!-- Photo — overflow:hidden + image-rendering pour netteté maximale -->
                            <div class="w-full h-full overflow-hidden" style="border-radius: 11px;">
                                <img
                                    src="{{ $agent->photo_url }}"
                                    alt="{{ $agent->nom_complet }}"
                                    class="w-full h-full object-cover"
                                    style="display: block; image-rendering: -webkit-optimize-contrast; image-rendering: high-quality; -ms-interpolation-mode: bicubic;"
                                >
                            </div>
                        </div>
                    </div>
                    <!-- Badge vérification -->
                    <span class="absolute bottom-1 right-1 text-white rounded-full p-1.5 shadow-md border-2 border-white" style="background-color: #052e16;">
                        <i class="fa-solid fa-check text-xs"></i>
                    </span>
                </div>

                <!-- Matricule — plaque premium alignée en bas à droite de la photo -->
                <div class="flex-shrink-0 ml-3">
                    <div class="rounded-xl overflow-hidden shadow-lg" style="border: 1.5px solid #d97706;">
                        <!-- Label -->
                        <div class="px-4 py-1.5 flex items-center gap-2" style="background-color: #052e16;">
                            <i class="fa-solid fa-id-badge text-amber-400" style="font-size: 10px;"></i>
                            <span style="font-size: 9px; letter-spacing: 0.14em; color: #fcd34d;" class="font-bold uppercase">Matricule de Solde</span>
                        </div>
                        <!-- Numéro -->
                        <div class="px-4 py-2 flex items-center justify-center" style="background: linear-gradient(135deg, #f9f5eb 0%, #fef9ee 100%);">
                            <span class="font-mono font-black tracking-widest" style="font-size: clamp(1rem, 4vw, 1.35rem); color: #052e16; letter-spacing: 0.12em;">
                                {{ $agent->matricule }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ligne décorative dorée sous la section photo/matricule -->
            <div class="mt-4 mb-4 h-px w-full" style="background: linear-gradient(90deg, #d97706 0%, #fbbf24 40%, transparent 100%); opacity: 0.35;"></div>

            <!-- Identity Header -->
            <div class="border-b border-slate-200 pb-5 mb-5">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ $agent->prenom }}
                </h1>
                <h2 class="text-xl sm:text-2xl font-black text-emerald-700 tracking-wider uppercase">
                    {{ $agent->nom }}
                </h2>
                
                @if ($agent->fonction)
                    <div class="mt-3 flex items-center text-slate-700 font-semibold text-sm sm:text-base">
                        <span class="p-1.5 rounded-lg bg-orange-100 text-orange-600 mr-2.5">
                            <i class="fa-solid fa-briefcase text-xs"></i>
                        </span>
                        <span>{{ $agent->fonction }}</span>
                    </div>
                @endif
            </div>

            <!-- Agent Details Grid -->
            <div class="space-y-3.5 text-sm">
                
                <!-- Structure d'affectation -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 flex items-start space-x-3.5">
                    <div class="p-2.5 bg-emerald-100 rounded-xl text-emerald-700 mt-0.5 shadow-xs">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 block">Structure d'affectation</span>
                        <span class="text-slate-900 font-extrabold text-sm">{{ $agent->structure_affectation ?? 'Non renseignée' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Date Integration FP -->
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 flex items-center space-x-3">
                        <div class="p-2.5 bg-amber-100 rounded-xl text-amber-700 shadow-xs">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 block">Intégration FP</span>
                            <span class="text-slate-900 font-bold text-xs">
                                {{ $agent->date_integration_fp ? $agent->date_integration_fp->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 flex items-center space-x-3">
                        <div class="p-2.5 rounded-xl text-white shadow-xs flex-shrink-0" style="background-color: #052e16;">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-500 block mb-0.5">Téléphone</span>
                            @if ($agent->telephone)
                                <a href="tel:{{ $agent->telephone }}"
                                   class="font-extrabold underline underline-offset-2 decoration-2 block truncate"
                                   style="color: #052e16; font-size: 0.85rem; text-decoration-color: #d97706;">
                                    {{ $agent->telephone }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-hand-pointer text-[9px]"></i>
                                    Appuyer pour appeler ou enregistrer
                                </span>
                            @else
                                <span class="text-slate-400 font-medium text-xs">N/A</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Email -->
                @if ($agent->email)
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 flex items-center space-x-3">
                        <div class="p-2.5 bg-orange-100 rounded-xl text-orange-600 shadow-xs">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="truncate">
                            <span class="text-xs font-bold text-slate-500 block">Email Professionnel</span>
                            <a href="mailto:{{ $agent->email }}" class="text-orange-600 hover:underline font-extrabold text-xs truncate block">
                                {{ $agent->email }}
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            <!-- QR Code Section -->
            <div class="mt-6 pt-5 border-t border-slate-200 flex flex-col items-center justify-center text-center">
                <div class="p-3.5 bg-white rounded-2xl shadow-md border-2 border-emerald-500/30 mb-2">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($agent->public_url) }}&color=064e3b" alt="QR Code {{ $agent->matricule }}" class="w-32 h-32">
                </div>
                <p class="text-[11px] font-semibold text-slate-500">Scannez ce QR Code pour vérifier l'authenticité de cette fiche</p>
            </div>

            <!-- Actions rapides -->
            <div class="mt-6 no-print">
                <a href="{{ route('agent.vcard', $agent->uuid) }}"
                    class="w-full py-3.5 px-6 rounded-2xl font-extrabold text-sm sm:text-base transition-all duration-200 flex items-center justify-center gap-3 shadow-lg active:scale-98 text-white hover:brightness-110"
                    style="background: linear-gradient(135deg, #052e16 0%, #064e3b 100%); border: 1.5px solid #d97706;">
                    <i class="fa-solid fa-user-plus text-amber-400 text-lg"></i>
                    <span class="tracking-wide">Enregistrer le contact</span>
                </a>
            </div>

        </div>

        <!-- Footer Seal -->
        <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-between items-center text-[10px] text-slate-500">
            <span>ID: <code class="font-mono text-slate-700 font-bold">{{ substr($agent->uuid, 0, 8) }}...</code></span>
            <span class="flex items-center gap-1.5 font-semibold text-emerald-700"><i class="fa-solid fa-lock"></i> Système Sécurisé Officiel</span>
        </div>

    </div>

    <!-- Footer -->
    <div class="mt-6 text-center text-xs text-slate-500 no-print">
        <p>&copy; {{ date('Y') }} MFPTRSP/DSI. Document numérique officiel.</p>
    </div>

</body>
</html>
