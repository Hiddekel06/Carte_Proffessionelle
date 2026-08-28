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

    <!-- Rapport Détaillé Post-Importation ZIP -->
    @if(session('zip_report'))
        @php $report = session('zip_report'); @endphp
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-xl space-y-5 mb-8 animate-fade-in relative overflow-hidden">
            <!-- Accent Top Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 {{ $report['failed'] > 0 ? 'bg-gradient-to-r from-emerald-600 via-amber-500 to-rose-500' : 'bg-emerald-600' }}"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-square-poll-vertical text-emerald-700"></i>
                        Rapport d'importation des photos terminé
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Bilan détaillé de l'extraction et de l'association des photos du ZIP.</p>
                </div>
                <span class="text-xs font-extrabold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700">
                    Total images traitées : {{ $report['total'] }}
                </span>
            </div>

            <!-- Statistiques Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <!-- Photos Associées -->
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-center gap-3.5 shadow-xs">
                    <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-emerald-950 block leading-tight">{{ $report['matched'] }}</span>
                        <span class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wide">Photo(s) associée(s)</span>
                    </div>
                </div>

                <!-- Photos Non Associées (Échecs) -->
                <div class="p-4 rounded-2xl {{ $report['failed'] > 0 ? 'bg-rose-50/80 border-rose-200' : 'bg-slate-50 border-slate-200' }} border flex items-center gap-3.5 shadow-xs">
                    <div class="w-11 h-11 rounded-xl {{ $report['failed'] > 0 ? 'bg-rose-600 text-white' : 'bg-slate-300 text-slate-600' }} flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black {{ $report['failed'] > 0 ? 'text-rose-950' : 'text-slate-700' }} block leading-tight">{{ $report['failed'] }}</span>
                        <span class="text-[11px] font-extrabold {{ $report['failed'] > 0 ? 'text-rose-700' : 'text-slate-500' }} uppercase tracking-wide">Matricule(s) introuvable(s)</span>
                    </div>
                </div>

                <!-- Fichiers Ignorés -->
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center gap-3.5 shadow-xs">
                    <div class="w-11 h-11 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-amber-950 block leading-tight">{{ $report['ignored'] }}</span>
                        <span class="text-[11px] font-extrabold text-amber-800 uppercase tracking-wide">Fichier(s) ignoré(s)</span>
                    </div>
                </div>
            </div>

            <!-- Liste des Photos Associées avec Succès -->
            @if($report['matched'] > 0)
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                    <h3 class="text-xs font-black text-emerald-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        Photos associées avec succès ({{ $report['matched'] }})
                    </h3>
                    <div class="border border-emerald-200 rounded-2xl overflow-hidden shadow-xs">
                        <div class="overflow-x-auto max-h-48 divide-y divide-emerald-100 bg-emerald-50/20 text-xs">
                            <table class="w-full text-left">
                                <thead class="bg-emerald-100/60 text-[10px] font-black text-emerald-900 uppercase">
                                    <tr>
                                        <th class="py-2.5 px-4">Fichier ZIP</th>
                                        <th class="py-2.5 px-4">Matricule</th>
                                        <th class="py-2.5 px-4">Nom de l'agent</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-emerald-100">
                                    @foreach($report['matched_list'] as $item)
                                        <tr class="hover:bg-emerald-50">
                                            <td class="py-2 px-4 font-mono text-slate-700">{{ $item['file'] }}</td>
                                            <td class="py-2 px-4 font-mono font-bold text-emerald-800">{{ $item['matricule'] }}</td>
                                            <td class="py-2 px-4 font-bold text-slate-900">{{ $item['nom'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tableau des Échecs avec Motifs -->
            @if($report['failed'] > 0)
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                    <h3 class="text-xs font-black text-rose-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                        Photos non associées ({{ $report['failed'] }})
                    </h3>
                    <div class="border border-rose-200 rounded-2xl overflow-hidden shadow-xs">
                        <div class="overflow-x-auto max-h-48 bg-rose-50/20 text-xs">
                            <table class="w-full text-left">
                                <thead class="bg-rose-100/60 text-[10px] font-black text-rose-900 uppercase">
                                    <tr>
                                        <th class="py-2.5 px-4">Fichier</th>
                                        <th class="py-2.5 px-4">Matricule détecté</th>
                                        <th class="py-2.5 px-4">Cause du rejet</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-100">
                                    @foreach($report['failures'] as $failure)
                                        <tr class="hover:bg-rose-50/50">
                                            <td class="py-2 px-4 font-mono text-slate-700">{{ $failure['file'] }}</td>
                                            <td class="py-2 px-4 font-mono font-bold text-rose-700">{{ $failure['matricule_extrait'] ?? 'N/A' }}</td>
                                            <td class="py-2 px-4 font-semibold text-rose-600">{{ $failure['reason'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        
        <!-- Top Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-emerald-500 to-teal-600"></div>

        <!-- Affichage des erreurs de validation -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <ul class="text-xs font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.agents.import-zip') }}" enctype="multipart/form-data" class="space-y-6 pt-2" id="zipForm">
            @csrf

            <!-- Upload Area -->
            <div>
                <label class="block text-sm font-extrabold text-slate-800 mb-2">Sélectionnez l'archive ZIP contenant les photos</label>
                <div class="border-2 border-dashed border-amber-300 rounded-3xl p-8 bg-amber-50/40 text-center hover:border-amber-500 transition-colors cursor-pointer" onclick="document.getElementById('zipInput').click()">
                    <i class="fa-solid fa-folder-closed text-4xl text-amber-500 mb-3 block"></i>
                    <p class="text-xs font-bold text-slate-700 mb-1" id="zipLabel">Glissez-déposez votre fichier .ZIP ici ou cliquez pour choisir</p>
                    <p class="text-[11px] text-slate-400 mb-4">Format pris en charge : .zip</p>
                    
                    <input type="file" name="zip_file" id="zipInput" accept=".zip,application/zip,application/x-zip-compressed" required class="block w-full max-w-xs mx-auto text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer bg-white rounded-xl border border-slate-200 shadow-xs" onchange="if(this.files[0]) document.getElementById('zipLabel').innerText = 'Fichier sélectionné : ' + this.files[0].name + ' (' + (this.files[0].size/1024/1024).toFixed(2) + ' Mo)';">
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
