@extends('layouts.app')

@section('title', 'Importation Massive des Agents (Excel / CSV) - Administration')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.agents.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-800 hover:text-emerald-950 transition-colors mb-2">
                <i class="fa-solid fa-arrow-left"></i> Retour au répertoire des agents
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-2xl bg-emerald-100 text-emerald-800 text-xl shadow-xs">
                    <i class="fa-solid fa-file-excel"></i>
                </span>
                Importation Massive des Agents
            </h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">Ajoutez ou actualisez un volume important de dossiers agents en un clic via un fichier Excel ou CSV.</p>
        </div>

        <!-- Boutons Téléchargement Modèles dans le Header -->
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.agents.template-xlsx') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white text-xs sm:text-sm font-extrabold shadow-sm transition-all active:scale-98">
                <i class="fa-solid fa-file-excel text-emerald-300 text-base"></i>
                <span>Modèle Excel (.xlsx)</span>
            </a>
            <a href="{{ route('admin.agents.template-csv') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-extrabold shadow-xs transition-all active:scale-98">
                <i class="fa-solid fa-file-csv text-slate-500 text-base"></i>
                <span>Modèle CSV</span>
            </a>
        </div>
    </div>

    <!-- Rapport Détaillé Post-Importation -->
    @if(session('import_report'))
        @php $report = session('import_report'); @endphp
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-xl space-y-5 animate-fade-in relative overflow-hidden">
            <!-- Accent Top Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 {{ $report['failed'] > 0 ? 'bg-gradient-to-r from-emerald-600 via-amber-500 to-rose-500' : 'bg-emerald-600' }}"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-square-poll-vertical text-emerald-700"></i>
                        Rapport d'importation terminé
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Bilan détaillé du traitement de votre fichier.</p>
                </div>
                <span class="text-xs font-extrabold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700">
                    Total traité : {{ $report['total'] }} ligne(s)
                </span>
            </div>

            <!-- Statistiques Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <!-- Nouveaux Agents -->
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-center gap-3.5 shadow-xs">
                    <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-emerald-950 block leading-tight">{{ $report['added'] }}</span>
                        <span class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wide">Nouveau(x) créé(s)</span>
                    </div>
                </div>

                <!-- Agents Mis à jour -->
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center gap-3.5 shadow-xs">
                    <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-blue-950 block leading-tight">{{ $report['updated'] }}</span>
                        <span class="text-[11px] font-extrabold text-blue-700 uppercase tracking-wide">Mis à jour</span>
                    </div>
                </div>

                <!-- Échecs / Rejets -->
                <div class="p-4 rounded-2xl {{ $report['failed'] > 0 ? 'bg-rose-50/80 border-rose-200' : 'bg-slate-50 border-slate-200' }} border flex items-center gap-3.5 shadow-xs">
                    <div class="w-11 h-11 rounded-xl {{ $report['failed'] > 0 ? 'bg-rose-600 text-white' : 'bg-slate-300 text-slate-600' }} flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black {{ $report['failed'] > 0 ? 'text-rose-950' : 'text-slate-700' }} block leading-tight">{{ $report['failed'] }}</span>
                        <span class="text-[11px] font-extrabold {{ $report['failed'] > 0 ? 'text-rose-700' : 'text-slate-500' }} uppercase tracking-wide">Ligne(s) rejetée(s)</span>
                    </div>
                </div>
            </div>

            <!-- Tableau des Échecs avec Motifs -->
            @if($report['failed'] > 0)
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2.5">
                    <h3 class="text-xs font-black text-rose-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                        Détail des erreurs et motifs de rejet ({{ $report['failed'] }})
                    </h3>
                    <div class="border border-rose-200 rounded-2xl overflow-hidden shadow-xs">
                        <div class="overflow-x-auto max-h-64">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-rose-100/60 text-rose-950 font-black uppercase text-[10px] tracking-wider border-b border-rose-200 sticky top-0">
                                    <tr>
                                        <th class="py-2.5 px-3.5 text-center w-16">Ligne</th>
                                        <th class="py-2.5 px-3.5 w-32">Matricule</th>
                                        <th class="py-2.5 px-3.5 w-44">Agent</th>
                                        <th class="py-2.5 px-3.5">Raison du rejet / Erreur détectée</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-100 bg-white font-medium text-slate-700">
                                    @foreach($report['failures'] as $fail)
                                        <tr class="hover:bg-rose-50/40 transition-colors">
                                            <td class="py-2 px-3.5 text-center font-mono font-bold text-rose-700 bg-rose-50/30">
                                                #{{ $fail['line'] }}
                                            </td>
                                            <td class="py-2 px-3.5 font-mono font-bold text-slate-900">
                                                {{ $fail['matricule'] }}
                                            </td>
                                            <td class="py-2 px-3.5 text-slate-800 font-bold">
                                                {{ $fail['nom'] }}
                                            </td>
                                            <td class="py-2 px-3.5 text-rose-700 font-semibold">
                                                <i class="fa-solid fa-xmark text-[10px] mr-1"></i> {{ $fail['reason'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic">
                        Astuce : Vous pouvez corriger uniquement les lignes indiquées dans votre fichier ou télécharger notre modèle officiel ci-dessus pour vous assurer de respecter la nomenclature.
                    </p>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Colonne Gauche : Formulaire de Téléversement (7 cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
            <!-- Accent Bar -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-800 via-emerald-600 to-amber-500"></div>

            <form method="POST" action="{{ route('admin.agents.import-excel') }}" enctype="multipart/form-data" class="space-y-6 pt-2" id="importForm">
                @csrf

                <!-- Upload Area Agrandie & Interactive -->
                <div>
                    <label class="block text-sm font-black text-slate-800 mb-2">
                        Fichier de données agents <span class="text-rose-500">*</span>
                    </label>
                    <div id="dropZone" class="border-2 border-dashed border-emerald-300 rounded-3xl p-8 sm:p-10 bg-emerald-50/30 text-center hover:border-emerald-600 hover:bg-emerald-50/60 transition-all cursor-pointer relative group">
                        <input type="file" id="excelFile" name="excel_file" accept=".xlsx,.xls,.csv,.txt" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-100/80 text-emerald-800 flex items-center justify-center text-3xl mb-3 shadow-inner group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <h3 class="text-sm sm:text-base font-extrabold text-slate-800 mb-1" id="fileLabel">
                                Cliquez pour choisir ou glissez-déposez le fichier ici
                            </h3>
                            <p class="text-xs text-slate-500 mb-3" id="fileSubtext">
                                Formats supportés : <strong>.CSV, .XLSX, .XLS</strong> (Max : 5 Mo)
                            </p>
                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-[11px] font-extrabold bg-white border border-emerald-200 text-emerald-800 shadow-xs">
                                <i class="fa-solid fa-folder-open text-amber-600"></i> Parcourir les documents
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Conseils d'importation -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-2">
                    <div class="flex items-center gap-2 font-extrabold text-emerald-900">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Fonctionnement du traitement :
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-[11.5px] text-slate-600 ml-1">
                        <li>Si le <strong>matricule</strong> existe déjà, les informations de l'agent seront automatiquement mises à jour.</li>
                        <li>S'il n'existe pas, un nouvel agent sera créé avec génération d'un QR code sécurisé.</li>
                        <li>Les séparateurs acceptés pour les fichiers CSV sont la virgule (<code>,</code>) ou le point-virgule (<code>;</code>).</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.agents.index') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs sm:text-sm transition-all">
                        Annuler
                    </a>
                    <button type="submit" class="px-7 py-3 rounded-xl text-white font-extrabold text-xs sm:text-sm shadow-md transition-all flex items-center gap-2.5 active:scale-98" style="background-color: #052e16;">
                        <i class="fa-solid fa-file-import text-amber-400"></i> Lancer l'importation
                    </button>
                </div>
            </form>
        </div>

        <!-- Colonne Droite : Modèle Exemplaire & Structure des Colonnes (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Carte Modèle Exemplaire -->
            <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden border border-emerald-800">
                <div class="relative z-10">
                    <span class="text-[10px] font-extrabold tracking-widest text-amber-300 uppercase px-3 py-1 rounded-lg border border-amber-400/30 bg-black/20 backdrop-blur-xs inline-block mb-3">
                        Gabarit Officiel
                    </span>
                    <h2 class="text-lg font-black text-white mb-2">Modèle de fichier prêt à l'emploi</h2>
                    <p class="text-xs text-emerald-200/80 mb-5 leading-relaxed">
                        Pour éviter toute erreur de structure, nous vous recommandons de télécharger le fichier modèle pré-rempli avec des exemples concrets.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <a href="{{ route('admin.agents.template-xlsx') }}" class="py-2.5 px-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs transition-all flex items-center justify-center gap-1.5 shadow-md">
                            <i class="fa-solid fa-file-excel"></i> Format Excel (.xlsx)
                        </a>
                        <a href="{{ route('admin.agents.template-csv') }}" class="py-2.5 px-3 rounded-xl bg-emerald-800 hover:bg-emerald-700 text-white font-black text-xs transition-all flex items-center justify-center gap-1.5 shadow-md border border-emerald-600">
                            <i class="fa-solid fa-file-csv"></i> Format CSV
                        </a>
                    </div>
                </div>
            </div>

            <!-- Carte Détail des Colonnes -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-md space-y-4">
                <h3 class="font-black text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-table-columns text-emerald-700"></i> Structure exacte des colonnes attendues
                </h3>

                <div class="space-y-2.5 text-xs max-h-96 overflow-y-auto pr-1">
                    <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 flex justify-between items-start">
                        <div>
                            <span class="font-extrabold text-slate-900 block">matricule <span class="text-rose-600 text-[10px] uppercase font-black tracking-wide ml-1">(Obligatoire)</span></span>
                            <span class="text-[11px] text-slate-500">Format : 6 chiffres + 1 lettre (ex: <code>123456A</code>)</span>
                        </div>
                    </div>

                    <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 flex justify-between items-start">
                        <div>
                            <span class="font-extrabold text-slate-900 block">prenom <span class="text-rose-600 text-[10px] uppercase font-black tracking-wide ml-1">(Obligatoire)</span></span>
                            <span class="text-[11px] text-slate-500">Prénom(s) de l'agent (ex: <code>Kouassi Jean</code>)</span>
                        </div>
                    </div>

                    <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 flex justify-between items-start">
                        <div>
                            <span class="font-extrabold text-slate-900 block">nom <span class="text-rose-600 text-[10px] uppercase font-black tracking-wide ml-1">(Obligatoire)</span></span>
                            <span class="text-[11px] text-slate-500">Nom de famille (ex: <code>KOUAME</code>)</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-800 block">fonction</span>
                        <span class="text-[11px] text-slate-500">Poste ou titre (ex: <code>Chef de Service</code>)</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-800 block">structure_affectation</span>
                        <span class="text-[11px] text-slate-500">Ministère / Direction (ex: <code>DSI</code>)</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-800 block">telephone</span>
                        <span class="text-[11px] text-slate-500">Numéro direct (ex: <code>+2250707070707</code>)</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-800 block">email</span>
                        <span class="text-[11px] text-slate-500">Email professionnel (ex: <code>agent@gouv.ci</code>)</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-800 block">date_integration_fp</span>
                        <span class="text-[11px] text-slate-500">Format : <code>AAAA-MM-JJ</code> (ex: <code>2020-05-15</code>)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('excelFile');
    const fileLabel = document.getElementById('fileLabel');
    const fileSubtext = document.getElementById('fileSubtext');
    const dropZone = document.getElementById('dropZone');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileLabel.innerHTML = `<span class="text-emerald-800"><i class="fa-solid fa-file-circle-check text-emerald-600 mr-1.5"></i> ${file.name}</span>`;
            fileSubtext.innerHTML = `Taille : ${(file.size / 1024).toFixed(1)} Ko — Prêt pour l'importation`;
            dropZone.classList.add('border-emerald-600', 'bg-emerald-50');
        }
    });
</script>

@endsection
