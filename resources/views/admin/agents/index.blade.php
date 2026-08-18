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

<!-- Search & Filter Bar -->
<div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('admin.agents.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par matricule (ex: 123456A), nom, prénom, structure ou fonction..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition-all">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all shadow-sm">
                Filtrer
            </button>
            @if(request('search'))
                <a href="{{ route('admin.agents.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-all">
                    Réinitialiser
                </a>
            @endif
        </div>
    </form>
</div>

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
                        <th class="py-4 px-5">Agent</th>
                        <th class="py-4 px-4">Matricule</th>
                        <th class="py-4 px-4">Fonction</th>
                        <th class="py-4 px-4">Structure</th>
                        <th class="py-4 px-4">Intégration FP</th>
                        <th class="py-4 px-4">Contact</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach ($agents as $agent)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            
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

                            <!-- Fonction -->
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $agent->fonction ?? '—' }}
                            </td>

                            <!-- Structure -->
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ $agent->structure_affectation ?? '—' }}
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

                                    <!-- Télécharger l'image brute PNG du QR Code -->
                                    <a href="{{ route('admin.agents.download-qr', $agent) }}" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-xs" title="Télécharger l'image PNG brute du QR Code pour Word/CV">
                                        <i class="fa-solid fa-download text-amber-600"></i> Télécharger QR
                                    </a>

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
