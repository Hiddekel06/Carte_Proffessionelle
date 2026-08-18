@extends('layouts.app')

@section('title', 'Importation Photos (ZIP) - Administration')

@section('content')

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.agents.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Retour au répertoire
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span class="p-2 rounded-2xl bg-amber-100 text-amber-700 text-xl">
                    <i class="fa-solid fa-file-archive"></i>
                </span>
                Importation Massives des Photos (Fichier ZIP)
            </h1>
            <p class="text-xs text-slate-500 mt-1">Téléversez un dossier compressé ZIP contenant les photos d'identité des agents pour les associer automatiquement.</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        
        <!-- Top Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-emerald-500 to-teal-600"></div>

        <form method="POST" action="{{ route('admin.agents.import-zip') }}" enctype="multipart/form-data" class="space-y-6 pt-2">
            @csrf

            <!-- Upload Area -->
            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-2">Sélectionnez l'archive ZIP contenant les photos</label>
                <div class="border-2 border-dashed border-amber-300 rounded-3xl p-8 bg-amber-50/40 text-center hover:border-amber-500 transition-colors">
                    <i class="fa-solid fa-folder-closed text-4xl text-amber-500 mb-3"></i>
                    <p class="text-xs font-bold text-slate-700 mb-1">Glissez-déposez votre fichier .ZIP ici</p>
                    <p class="text-[11px] text-slate-400 mb-4">Format pris en charge : .zip (Taille max: 50Mo)</p>
                    
                    <input type="file" name="zip_file" accept=".zip" required class="block w-full max-w-xs mx-auto text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer bg-white rounded-xl border border-slate-200 shadow-xs">
                </div>
            </div>

            <!-- Instructions & Naming Rules -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-extrabold text-xs text-amber-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600"></i> Règle de nommage des fichiers photos dans le ZIP
                </h4>
                <p class="text-xs text-slate-600">Pour que le système associe chaque photo au bon agent, nommez chaque image avec le <strong>matricule exact</strong> de l'agent :</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                    <div class="p-3 bg-white rounded-xl border border-emerald-200 text-center">
                        <span class="font-mono font-extrabold text-emerald-700 block text-sm">123456A.jpg</span>
                        <span class="text-[10px] text-slate-500">Matricule: 123456A</span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-emerald-200 text-center">
                        <span class="font-mono font-extrabold text-emerald-700 block text-sm">654321B.png</span>
                        <span class="text-[10px] text-slate-500">Matricule: 654321B</span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-emerald-200 text-center">
                        <span class="font-mono font-extrabold text-emerald-700 block text-sm">987654C.webp</span>
                        <span class="text-[10px] text-slate-500">Matricule: 987654C</span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('admin.agents.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-sm shadow-md shadow-amber-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-file-archive"></i> Extraire et Associer les Photos
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
