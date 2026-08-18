<!DOCTYPE html>
<html lang="fr" class="bg-white text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Agent - {{ $agent->nom_complet }} ({{ $agent->matricule }})</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-box { border: 2px solid #059669 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body onload="window.print()" class="min-h-screen bg-slate-100 flex flex-col items-center justify-center p-6 font-sans">

    <!-- Print Action Controls (Hidden when printing) -->
    <div class="mb-6 flex gap-3 no-print">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Imprimer / Enregistrer en PDF
        </button>
        <a href="{{ route('admin.agents.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm">
            Retour
        </a>
    </div>

    <!-- Official Printable QR Sheet -->
    <div class="print-box bg-white border border-slate-300 rounded-3xl p-8 max-w-md w-full shadow-2xl text-center relative overflow-hidden">
        
        <!-- Top Accent Bar -->
        <div class="h-2 w-full bg-gradient-to-r from-emerald-600 via-amber-400 to-rose-600 absolute top-0 left-0 right-0"></div>

        <!-- Senegal Official Header -->
        <div class="mb-6 pt-2 pb-4 border-b border-slate-200 flex flex-col items-center">
            <img src="{{ asset('images/MFPremove.png') }}" alt="Logo Officiel" class="h-16 w-auto object-contain mb-2">
            <h2 class="font-black text-xs uppercase tracking-wider text-slate-900">RÉPUBLIQUE DU SÉNÉGAL</h2>
            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest">Ministère de la Fonction Publique</p>
        </div>

        <!-- Agent Identification -->
        <div class="mb-6">
            <div class="inline-block bg-amber-50 border border-amber-300 text-amber-900 font-mono font-extrabold text-sm px-4 py-1.5 rounded-xl mb-3 shadow-xs">
                MATRICULE: {{ $agent->matricule }}
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 leading-tight">
                {{ $agent->nom_complet }}
            </h3>
            @if($agent->fonction)
                <p class="text-xs text-slate-600 font-medium mt-1">{{ $agent->fonction }}</p>
            @endif
            @if($agent->structure_affectation)
                <p class="text-xs text-slate-500 font-semibold">{{ $agent->structure_affectation }}</p>
            @endif
        </div>

        <!-- QR Code Large -->
        <div class="p-4 bg-white border-2 border-emerald-600/40 rounded-3xl inline-block shadow-lg mb-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($agent->public_url) }}&color=064e3b" alt="QR Code {{ $agent->matricule }}" class="w-48 h-48 mx-auto">
        </div>

        <!-- Instructions -->
        <p class="text-xs font-bold text-slate-700 mt-2">Scannez ce QR Code pour accéder à la fiche officielle de l'agent</p>
        <p class="text-[10px] text-slate-400 font-mono mt-1">ID: {{ $agent->uuid }}</p>

    </div>

</body>
</html>
