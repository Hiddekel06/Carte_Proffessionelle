@extends('layouts.app')

@section('title', 'Gestion des Fiches Agents - Administration')

@section('content')

<!-- Header & Quick Actions -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
            <span class="p-2.5 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow-md shadow-emerald-600/20 text-xl">
                <i class="fa-solid fa-users"></i>
            </span>
            Répertoire des Agents
        </h1>
        <p class="text-xs text-slate-500 mt-1">Liste globale et gestion des fiches agents de la Fonction Publique.</p>
    </div>

    <!-- Actions Bar -->
    <div class="flex flex-wrap items-center gap-3">
        <!-- Button Import Excel -->
        <a href="{{ route('admin.agents.show-import-excel') }}" class="px-4 py-2.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-file-excel text-orange-500 text-base"></i> Import Excel/CSV
        </a>

        <!-- Button Import ZIP Photos -->
        <a href="{{ route('admin.agents.show-import-zip') }}" class="px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-file-archive text-amber-500 text-base"></i> Import ZIP Photos
        </a>

        <!-- Button Nouveau Agent -->
        <a href="{{ route('admin.agents.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Ajouter un Agent
        </a>
    </div>
</div>

<!-- Search & Multi-Criteria Filter Bar -->
<div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 mb-6 shadow-sm">
    <form method="GET" action="{{ route('admin.agents.index') }}" id="filterForm">
        @if($etatCarte)
            <input type="hidden" name="etat" value="{{ $etatCarte }}">
        @endif

        <!-- Ligne 1 : Recherche principale + Tri + Boutons d'action -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 mb-4">
            
            <!-- Recherche globale -->
            <div class="lg:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-emerald-600"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Recherche : matricule (123456A), nom, prénom, email, téléphone..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                >
            </div>

            <!-- Tri des résultats -->
            <div class="lg:col-span-3 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-arrow-down-a-z text-slate-400"></i>
                </div>
                <select 
                    name="sort" 
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm font-semibold focus:outline-none focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition-all appearance-none cursor-pointer"
                >
                    <option value="nom_asc" {{ request('sort', 'nom_asc') == 'nom_asc' ? 'selected' : '' }}>Nom (A → Z)</option>
                    <option value="nom_desc" {{ request('sort') == 'nom_desc' ? 'selected' : '' }}>Nom (Z → A)</option>
                    <option value="matricule_asc" {{ request('sort') == 'matricule_asc' ? 'selected' : '' }}>Matricule (Croissant)</option>
                    <option value="matricule_desc" {{ request('sort') == 'matricule_desc' ? 'selected' : '' }}>Matricule (Décroissant)</option>
                    <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Plus récents d'abord</option>
                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Plus anciens d'abord</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            <!-- Boutons : Filtrer & Réinitialiser -->
            <div class="lg:col-span-3 flex gap-2">
                <button 
                    type="submit" 
                    class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-extrabold shadow-sm shadow-emerald-600/20 transition-all duration-200 flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-filter text-xs"></i> Appliquer
                </button>
                @if(request()->hasAny(['search', 'structure', 'service', 'fonction', 'poste', 'has_photo', 'sort']) && (request('search') || request('structure') || request('service') || request('fonction') || request('poste') || request('has_photo') || (request('sort') && request('sort') != 'nom_asc')))
                    <a 
                        href="{{ route('admin.agents.index') }}" 
                        title="Effacer tous les filtres" 
                        class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                    >
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Ligne 2 : Filtres avancés par critères spécifiques -->
        <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- 1. Structure / Ministère -->
            <div class="relative">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Ministère / Structure</label>
                <select 
                    name="structure" 
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full px-3 py-2 bg-slate-50 border {{ request('structure') ? 'border-emerald-500 bg-emerald-50/30 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 font-medium' }} rounded-xl text-xs focus:outline-none focus:border-emerald-600 focus:bg-white transition-all appearance-none cursor-pointer pr-7"
                >
                    <option value="">Toutes les structures</option>
                    @foreach($structures as $struct)
                        <option value="{{ $struct }}" {{ request('structure') == $struct ? 'selected' : '' }}>
                            {{ Str::limit($struct, 28) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute bottom-2.5 right-2.5 pointer-events-none text-slate-400 text-[10px]">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            <!-- 2. Service / Direction -->
            <div class="relative">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Direction / Service</label>
                <select 
                    name="service" 
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full px-3 py-2 bg-slate-50 border {{ request('service') ? 'border-emerald-500 bg-emerald-50/30 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 font-medium' }} rounded-xl text-xs focus:outline-none focus:border-emerald-600 focus:bg-white transition-all appearance-none cursor-pointer pr-7"
                >
                    <option value="">Tous les services</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv }}" {{ request('service') == $srv ? 'selected' : '' }}>
                            {{ Str::limit($srv, 28) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute bottom-2.5 right-2.5 pointer-events-none text-slate-400 text-[10px]">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            <!-- 3. Fonction (Corps de métier) -->
            <div class="relative">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Fonction / Métier</label>
                <select 
                    name="fonction" 
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full px-3 py-2 bg-slate-50 border {{ request('fonction') ? 'border-emerald-500 bg-emerald-50/30 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 font-medium' }} rounded-xl text-xs focus:outline-none focus:border-emerald-600 focus:bg-white transition-all appearance-none cursor-pointer pr-7"
                >
                    <option value="">Toutes les fonctions</option>
                    @foreach($fonctions as $fnc)
                        <option value="{{ $fnc }}" {{ request('fonction') == $fnc ? 'selected' : '' }}>
                            {{ Str::limit($fnc, 28) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute bottom-2.5 right-2.5 pointer-events-none text-slate-400 text-[10px]">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            <!-- 4. Poste (Titre / Rôle) -->
            <div class="relative">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Poste / Titre</label>
                <select 
                    name="poste" 
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full px-3 py-2 bg-slate-50 border {{ request('poste') ? 'border-emerald-500 bg-emerald-50/30 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 font-medium' }} rounded-xl text-xs focus:outline-none focus:border-emerald-600 focus:bg-white transition-all appearance-none cursor-pointer pr-7"
                >
                    <option value="">Tous les postes</option>
                    @foreach($postes as $pst)
                        <option value="{{ $pst }}" {{ request('poste') == $pst ? 'selected' : '' }}>
                            {{ Str::limit($pst, 28) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute bottom-2.5 right-2.5 pointer-events-none text-slate-400 text-[10px]">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            <!-- 5. Statut Photo -->
            <div class="relative">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Photo de profil</label>
                <select 
                    name="has_photo" 
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full px-3 py-2 bg-slate-50 border {{ request('has_photo') ? 'border-emerald-500 bg-emerald-50/30 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 font-medium' }} rounded-xl text-xs focus:outline-none focus:border-emerald-600 focus:bg-white transition-all appearance-none cursor-pointer pr-7"
                >
                    <option value="">Toutes les fiches</option>
                    <option value="yes" {{ request('has_photo') == 'yes' ? 'selected' : '' }}>Avec photo uniquement ({{ $totalWithPhoto }})</option>
                    <option value="no" {{ request('has_photo') == 'no' ? 'selected' : '' }}>Sans photo uniquement ({{ $totalWithoutPhoto }})</option>
                </select>
                <div class="absolute bottom-2.5 right-2.5 pointer-events-none text-slate-400 text-[10px]">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

        </div>

        <!-- Badges des filtres actifs (Tags cliquables pour retirer un filtre individuel) -->
        @if(request('structure') || request('service') || request('fonction') || request('poste') || request('has_photo') || request('search'))
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mr-1">
                    <i class="fa-solid fa-sliders text-emerald-600"></i> Filtres actifs :
                </span>

                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors group">
                        <span>Recherche : "{{ request('search') }}"</span>
                        <i class="fa-solid fa-xmark text-[10px] text-emerald-600 group-hover:text-rose-600"></i>
                    </a>
                @endif

                @if(request('structure'))
                    <a href="{{ request()->fullUrlWithQuery(['structure' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors group">
                        <span>Structure : {{ Str::limit(request('structure'), 20) }}</span>
                        <i class="fa-solid fa-xmark text-[10px] text-emerald-600 group-hover:text-rose-600"></i>
                    </a>
                @endif

                @if(request('service'))
                    <a href="{{ request()->fullUrlWithQuery(['service' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors group">
                        <span>Service : {{ Str::limit(request('service'), 20) }}</span>
                        <i class="fa-solid fa-xmark text-[10px] text-emerald-600 group-hover:text-rose-600"></i>
                    </a>
                @endif

                @if(request('fonction'))
                    <a href="{{ request()->fullUrlWithQuery(['fonction' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors group">
                        <span>Fonction : {{ Str::limit(request('fonction'), 20) }}</span>
                        <i class="fa-solid fa-xmark text-[10px] text-emerald-600 group-hover:text-rose-600"></i>
                    </a>
                @endif

                @if(request('poste'))
                    <a href="{{ request()->fullUrlWithQuery(['poste' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors group">
                        <span>Poste : {{ Str::limit(request('poste'), 20) }}</span>
                        <i class="fa-solid fa-xmark text-[10px] text-emerald-600 group-hover:text-rose-600"></i>
                    </a>
                @endif

                @if(request('has_photo'))
                    <a href="{{ request()->fullUrlWithQuery(['has_photo' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors group">
                        <span>{{ request('has_photo') == 'yes' ? 'Avec photo' : 'Sans photo' }}</span>
                        <i class="fa-solid fa-xmark text-[10px] text-emerald-600 group-hover:text-rose-600"></i>
                    </a>
                @endif

                <a href="{{ route('admin.agents.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 ml-1 underline decoration-dotted">
                    Tout effacer
                </a>
            </div>
        @endif

    </form>
</div>

<!-- Onglets : état de la carte professionnelle -->
@php
    $ongletsEtat = [
        null => ['label' => 'Toutes', 'icone' => 'fa-layer-group', 'compte' => $compteursEtat['tous']],
        \App\Models\Agent::ETAT_NON_IMPRIMEE => ['label' => 'Non imprimées', 'icone' => 'fa-print', 'compte' => $compteursEtat[\App\Models\Agent::ETAT_NON_IMPRIMEE]],
        \App\Models\Agent::ETAT_A_REMETTRE => ['label' => 'À remettre', 'icone' => 'fa-hourglass-half', 'compte' => $compteursEtat[\App\Models\Agent::ETAT_A_REMETTRE]],
        \App\Models\Agent::ETAT_REMISE => ['label' => 'Remises', 'icone' => 'fa-circle-check', 'compte' => $compteursEtat[\App\Models\Agent::ETAT_REMISE]],
    ];
@endphp
<div class="flex flex-wrap items-center gap-2 mb-4">
    @foreach($ongletsEtat as $cle => $onglet)
        @php($actif = ($etatCarte ?? '') === ($cle ?? ''))
        <a href="{{ request()->fullUrlWithQuery(['etat' => $cle ?: null, 'page' => null]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border transition-all {{ $actif ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm shadow-emerald-600/20' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="fa-solid {{ $onglet['icone'] }} text-[11px]"></i>
            {{ $onglet['label'] }}
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $actif ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}">{{ $onglet['compte'] }}</span>
        </a>
    @endforeach
</div>

<!-- Barre d'actions sur la sélection -->
<form method="POST" action="{{ route('admin.agents.etat-carte') }}" id="etatForm"
      class="hidden items-center flex-wrap gap-2 mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-2xl"
      onsubmit="return confirmerChangementEtat(this);">
    @csrf
    <span class="text-xs font-bold text-emerald-900 mr-2">
        <i class="fa-solid fa-square-check"></i> <span id="nbSelection">0</span> agent(s) sélectionné(s)
    </span>
    <select name="action" class="px-3 py-2 bg-white border border-emerald-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-emerald-600">
        <option value="remise">Marquer comme remises</option>
        <option value="imprimee">Marquer comme imprimées</option>
        <option value="annuler_remise">Annuler la remise (→ À remettre)</option>
        <option value="non_imprimee">Repasser en « Non imprimée »</option>
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold transition-all">
        Appliquer
    </button>
    <p class="w-full text-[11px] text-emerald-800/80 mt-1">
        Une carte ne peut être marquée remise que si elle a déjà été imprimée.
    </p>
</form>

<!-- Total Stats Counter -->
<div class="flex items-center justify-between mb-4 px-1">
    <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">
        Affichage de <span class="text-slate-900 font-extrabold">{{ $agents->firstItem() ?? 0 }}</span> à <span class="text-slate-900 font-extrabold">{{ $agents->lastItem() ?? 0 }}</span> sur <span class="text-emerald-700 font-extrabold">{{ $agents->total() }}</span> agents enregistrés
    </span>
</div>

<!-- Agents Table View -->
@if ($agents->count() > 0)
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 pl-5 pr-1 w-8">
                            <input type="checkbox" id="toutSelectionner" title="Tout sélectionner" class="w-4 h-4 rounded border-slate-300 accent-emerald-600 cursor-pointer">
                        </th>
                        <th class="py-4 px-5">Agent</th>
                        <th class="py-4 px-4">Matricule</th>
                        <th class="py-4 px-4">Poste / Fonction</th>
                        <th class="py-4 px-4">Ministère / Service</th>
                        <th class="py-4 px-4">Intégration FP</th>
                        <th class="py-4 px-4">Contact</th>
                        <th class="py-4 px-4">État carte</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach ($agents as $agent)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Sélection -->
                            <td class="py-3.5 pl-5 pr-1">
                                <input type="checkbox" name="agents[]" value="{{ $agent->id }}" form="etatForm" class="case-agent w-4 h-4 rounded border-slate-300 accent-emerald-600 cursor-pointer">
                            </td>

                            <!-- Agent Photo & Name -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $agent->photo_url }}" alt="{{ $agent->nom_complet }}" class="w-10 h-10 rounded-xl object-cover border border-emerald-500/30 bg-slate-100">
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-sm group-hover:text-emerald-700 transition-colors">
                                             {{ $agent->nom_complet }}
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-medium">ID: {{ substr($agent->uuid, 0, 8) }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Matricule Pill -->
                            <td class="py-3.5 px-4">
                                <span class="inline-block bg-amber-50 border border-amber-300 text-amber-900 font-mono font-extrabold text-xs px-3 py-1 rounded-xl shadow-xs">
                                    {{ $agent->matricule }}
                                </span>
                            </td>

                            <!-- Poste & Fonction -->
                            <td class="py-3.5 px-4">
                                @if($agent->poste)
                                    <span class="font-extrabold text-emerald-800 block text-xs">{{ $agent->poste }}</span>
                                @endif
                                <span class="text-[11px] text-slate-600 font-medium">{{ $agent->fonction ?? ($agent->poste ? '' : '—') }}</span>
                            </td>

                            <!-- Ministère & Service -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block text-xs">{{ $agent->structure_affectation ?? '—' }}</span>
                                @if($agent->service)
                                    <span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-turn-up fa-rotate-90 text-[9px] text-slate-400"></i> {{ $agent->service }}
                                    </span>
                                @endif
                            </td>

                            <!-- Date Intégration FP -->
                            <td class="py-3.5 px-4 text-slate-600 font-medium whitespace-nowrap">
                                {{ $agent->date_integration_fp ? $agent->date_integration_fp->format('d/m/Y') : '—' }}
                            </td>

                            <!-- Contact -->
                            <td class="py-3.5 px-4">
                                @if ($agent->telephone)
                                    <div class="font-bold text-emerald-700 whitespace-nowrap">{{ $agent->telephone }}</div>
                                @endif
                                @if ($agent->email)
                                    <div class="text-[11px] text-slate-500 truncate max-w-[140px]">{{ $agent->email }}</div>
                                @endif
                                @if (!$agent->telephone && !$agent->email)
                                    <span class="text-slate-400 font-medium">—</span>
                                @endif
                            </td>

                            <!-- État de la carte -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @switch($agent->etat_carte)
                                    @case(\App\Models\Agent::ETAT_REMISE)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold">
                                            <i class="fa-solid fa-circle-check"></i> Remise
                                        </span>
                                        <a href="{{ route('admin.historique.index', ['agent' => $agent->id]) }}" class="block text-[10px] text-slate-400 hover:text-emerald-700 mt-1" title="Voir l'historique">le {{ $agent->carte_remise_le->format('d/m/Y') }} <i class="fa-solid fa-clock-rotate-left"></i></a>
                                        @break
                                    @case(\App\Models\Agent::ETAT_A_REMETTRE)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-300 text-[11px] font-bold">
                                            <i class="fa-solid fa-hourglass-half"></i> À remettre
                                        </span>
                                        <a href="{{ route('admin.historique.index', ['agent' => $agent->id]) }}" class="block text-[10px] text-slate-400 hover:text-emerald-700 mt-1" title="Voir l'historique">imprimée le {{ $agent->carte_imprimee_le->format('d/m/Y') }} <i class="fa-solid fa-clock-rotate-left"></i></a>
                                        @break
                                    @default
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-bold">
                                            <i class="fa-solid fa-print"></i> Non imprimée
                                        </span>
                                @endswitch
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Carte PVC Printable Badge (Recto / Verso) -->
                                    <a href="{{ route('admin.agents.badge', $agent) }}" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-extrabold shadow-xs transition-all flex items-center gap-1.5" title="Concevoir & Imprimer la Carte PVC (Recto/Verso)">
                                        <i class="fa-solid fa-id-badge"></i> Carte PVC
                                    </a>

                                    <!-- View Public QR Card -->
                                    <a href="{{ $agent->public_url }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition-all flex items-center gap-1.5 shadow-xs" title="Voir la Fiche Numérique Publique">
                                        <i class="fa-solid fa-eye text-emerald-600"></i> Voir Fiche
                                    </a>

                                    <!-- Télécharger l'image stylisée PNG du QR Code -->
                                    <button type="button" onclick="downloadStyledQR('{{ $agent->public_url }}', '{{ $agent->matricule }}', this)" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-xs cursor-pointer" title="Télécharger l'image PNG stylisée du QR Code">
                                        <i class="fa-solid fa-download text-amber-600"></i> Télécharger QR
                                    </button>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.agents.edit', $agent) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-orange-100 text-slate-600 hover:text-orange-700 text-xs transition-all" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer la fiche de cet agent ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition-all" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Styled Pagination -->
    <div class="mt-6">
        {{ $agents->links() }}
    </div>

@else
    <!-- Empty State -->
    <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-xl mx-auto my-12 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Aucun agent trouvé</h3>
        <p class="text-sm text-slate-500 mb-6">Aucune fiche agent ne correspond à vos critères ou le répertoire est vide.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('admin.agents.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all shadow-sm">
                Créer un Agent
            </a>
            <a href="{{ route('admin.agents.show-import-excel') }}" class="px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 font-bold text-sm transition-all">
                Importer Excel
            </a>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
// ===== Sélection multiple pour changer l'état des cartes =====
(function () {
    const form = document.getElementById('etatForm');
    const tout = document.getElementById('toutSelectionner');
    const cases = () => Array.from(document.querySelectorAll('.case-agent'));

    function majBarre() {
        const n = cases().filter(c => c.checked).length;
        document.getElementById('nbSelection').textContent = n;
        form.classList.toggle('hidden', n === 0);
        form.classList.toggle('flex', n > 0);
        if (tout) {
            tout.checked = n > 0 && n === cases().length;
            tout.indeterminate = n > 0 && n < cases().length;
        }
    }

    cases().forEach(c => c.addEventListener('change', majBarre));
    if (tout) {
        tout.addEventListener('change', () => {
            cases().forEach(c => c.checked = tout.checked);
            majBarre();
        });
    }
})();

function confirmerChangementEtat(form) {
    const n = document.getElementById('nbSelection').textContent;
    const action = form.querySelector('select[name="action"]');
    const libelle = action.options[action.selectedIndex].text;
    return confirm(`${libelle} pour ${n} agent(s) ?`);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.1/lib/qr-code-styling.min.js"></script>
<script>
/**
 * Génère et télécharge le QR Code avec le design personnalisé :
 * - Points ronds (dots)
 * - Anneaux de coins circulaires (extra-rounded)
 * - Centre des coins carrés (square)
 */
async function downloadStyledQR(url, matricule, btnElement) {
    const originalHTML = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-amber-600"></i> Téléchargement...';
    btnElement.disabled = true;

    try {
        const qrCode = new QRCodeStyling({
            width: 1000,
            height: 1000,
            type: 'canvas',
            data: url,
            margin: 20,
            qrOptions: {
                typeNumber: 0,
                mode: 'Byte',
                errorCorrectionLevel: 'Q'
            },
            dotsOptions: {
                color: '#000000',
                type: 'dots'
            },
            cornersSquareOptions: {
                color: '#000000',
                type: 'extra-rounded'
            },
            cornersDotOptions: {
                color: '#000000',
                type: 'square'
            },
            backgroundOptions: {
                color: '#ffffff'
            }
        });

        await qrCode.download({
            name: `QR_${matricule}`,
            extension: 'png'
        });
    } catch (error) {
        console.error('Erreur lors du téléchargement du QR Code:', error);
        alert('Une erreur est survenue lors de la génération du QR Code.');
    } finally {
        btnElement.innerHTML = originalHTML;
        btnElement.disabled = false;
    }
}
</script>
@endpush
