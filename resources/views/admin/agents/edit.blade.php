@extends('layouts.app')

@section('title', 'Édition Agent - Administration')

@section('content')

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.agents.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Retour à la liste
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Modifier la Fiche Agent</h1>
            <p class="text-xs text-slate-500 mt-1">Mise à jour des informations de <strong>{{ $agent->nom_complet }}</strong> (Matricule: {{ $agent->matricule }}).</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        
        <!-- Top Tricolor Accent Line -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-amber-400 to-orange-500"></div>

        <form method="POST" action="{{ route('admin.agents.update', $agent) }}" enctype="multipart/form-data" class="space-y-6 pt-2">
            @csrf
            @method('PUT')

            <!-- Current Avatar Preview -->
            <div class="flex items-center space-x-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <img src="{{ $agent->photo_url }}" alt="{{ $agent->nom_complet }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-emerald-500/30">
                <div>
                    <h4 class="font-extrabold text-slate-900 text-base">{{ $agent->nom_complet }}</h4>
                    <span class="text-xs text-amber-900 bg-amber-100 px-2.5 py-0.5 rounded-lg border border-amber-300 font-mono font-bold">Matricule: {{ $agent->matricule }}</span>
                </div>
            </div>

            <!-- Section 1 : Identité & Matricule -->
            <div>
                <h3 class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-200 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    Informations Obligatoires
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Matricule -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Matricule <span class="text-rose-500">*</span></label>
                        <input type="text" name="matricule" value="{{ old('matricule', $agent->matricule) }}" required class="w-full px-3.5 py-2.5 bg-amber-50/50 border @error('matricule') border-rose-500 @else border-amber-300 @enderror rounded-xl text-slate-900 font-mono font-bold text-sm uppercase focus:outline-none focus:border-emerald-600">
                        @error('matricule')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Prénom <span class="text-rose-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom', $agent->prenom) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border @error('prenom') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                        @error('prenom')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nom -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Nom <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $agent->nom) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border @error('nom') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 text-sm uppercase focus:outline-none focus:border-emerald-600">
                        @error('nom')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2 : Affectation & Poste -->
            <div>
                <h3 class="text-xs font-extrabold text-orange-700 uppercase tracking-wider mb-4 pb-2 border-b border-slate-200 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-orange-100 text-orange-600">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>
                    Affectation & Poste
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Structure (Ministère) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Structure / Ministère</label>
                        <input type="text" name="structure_affectation" value="{{ old('structure_affectation', $agent->structure_affectation) }}" placeholder="ex: Ministère de la Fonction Publique" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <!-- Service / Direction -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Service / Direction</label>
                        <input type="text" name="service" value="{{ old('service', $agent->service) }}" placeholder="ex: Direction des Systèmes d'Information" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <!-- Fonction (Profession / Corps de métier) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Fonction (Corps de métier)</label>
                        <input type="text" name="fonction" value="{{ old('fonction', $agent->fonction) }}" placeholder="ex: Analyste Programmeur, Gestionnaire RH" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <!-- Poste (Responsabilité / Titre) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Poste (Titre / Responsabilité)</label>
                        <input type="text" name="poste" value="{{ old('poste', $agent->poste) }}" placeholder="ex: Directeur, Chef de Service, Inspecteur" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <!-- Date d'intégration FP -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-800 mb-1">Date d'intégration à la FP</label>
                        <input type="date" name="date_integration_fp" value="{{ old('date_integration_fp', $agent->date_integration_fp ? $agent->date_integration_fp->format('Y-m-d') : '') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>
                </div>
            </div>

            <!-- Section 3 : Contact & Photo -->
            <div>
                <h3 class="text-xs font-extrabold text-amber-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-200 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-address-book"></i>
                    </span>
                    Contact & Photo
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email', $agent->email) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $agent->telephone) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <!-- Photo -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-800 mb-1">Remplacer la photo d'identité</label>
                        <input type="file" name="photo" accept="image/*" class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer bg-slate-50 rounded-xl border border-slate-200">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('admin.agents.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition-all">
                    <i class="fa-solid fa-save mr-1.5"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
