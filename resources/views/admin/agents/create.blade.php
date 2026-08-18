@extends('layouts.app')

@section('title', 'Nouveau Agent - Administration')

@section('content')

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.agents.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Retour à la liste
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ajouter un Agent</h1>
            <p class="text-xs text-slate-500 mt-1">Remplissez les informations ci-dessous pour créer une nouvelle fiche agent.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        
        <!-- Top Tricolor Accent Line -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-amber-400 to-orange-500"></div>

        <form method="POST" action="{{ route('admin.agents.store') }}" enctype="multipart/form-data" class="space-y-6 pt-2">
            @csrf

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
                        <input type="text" name="matricule" value="{{ old('matricule') }}" placeholder="ex: 123456A" required uppercase class="w-full px-3.5 py-2.5 bg-amber-50/50 border @error('matricule') border-rose-500 @else border-amber-300 @enderror rounded-xl text-slate-900 font-mono font-bold text-sm uppercase focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                        <p class="text-[10px] text-slate-500 mt-1">6 chiffres + 1 lettre (ex: 123456A)</p>
                        @error('matricule')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Prénom <span class="text-rose-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Prénom" required class="w-full px-3.5 py-2.5 bg-slate-50 border @error('prenom') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                        @error('prenom')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nom -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Nom <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" placeholder="NOM" required class="w-full px-3.5 py-2.5 bg-slate-50 border @error('nom') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 text-sm uppercase focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
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
                    Affectation & Poste (Optionnel)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Structure -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Structure d'affectation</label>
                        <input type="text" name="structure_affectation" value="{{ old('structure_affectation') }}" placeholder="Direction / Service / Ministère" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Fonction -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Fonction / Intitulé de poste</label>
                        <input type="text" name="fonction" value="{{ old('fonction') }}" placeholder="ex: Analyste Développeur" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Date d'intégration FP -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Date d'intégration à la FP</label>
                        <input type="date" name="date_integration_fp" value="{{ old('date_integration_fp') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                </div>
            </div>

            <!-- Section 3 : Contact & Photo -->
            <div>
                <h3 class="text-xs font-extrabold text-amber-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-200 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-address-book"></i>
                    </span>
                    Contact & Photo (Optionnel)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="agent@gouv.ci" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+225 07 00 00 00 00" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Photo -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-800 mb-1">Photo d'identité</label>
                        <input type="file" name="photo" accept="image/*" class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer bg-slate-50 rounded-xl border border-slate-200">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('admin.agents.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Enregistrer l'Agent
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
