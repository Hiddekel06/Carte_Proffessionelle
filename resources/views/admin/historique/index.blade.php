@extends('layouts.app')

@section('title', 'Historique des cartes - Administration')

@php
    use App\Models\CarteEvenement;

    $couleurs = [
        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'amber'   => 'bg-amber-50 text-amber-900 border-amber-300',
        'slate'   => 'bg-slate-100 text-slate-700 border-slate-200',
        'rose'    => 'bg-rose-50 text-rose-700 border-rose-200',
    ];
    $filtresActifs = request()->hasAny(['search', 'action', 'du', 'au', 'agent'])
        && (request('search') || request('action') || request('du') || request('au') || request('agent'));
@endphp

@section('content')

<!-- En-tête -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
            <span class="p-2.5 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow-md shadow-emerald-600/20 text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </span>
            Historique des cartes
        </h1>
        <p class="text-xs text-slate-500 mt-1">Toutes les impressions et remises de cartes, avec la date et la personne qui les a enregistrées.</p>
    </div>

    <!-- Raccourcis de période -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.historique.index', ['action' => CarteEvenement::REMISE, 'du' => today()->toDateString(), 'au' => today()->toDateString()]) }}"
           class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-sm font-bold transition-all flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-calendar-day text-emerald-600"></i> Remises du jour
        </a>
        <a href="{{ route('admin.historique.index', ['action' => CarteEvenement::REMISE, 'du' => today()->startOfWeek()->toDateString(), 'au' => today()->toDateString()]) }}"
           class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-sm font-bold transition-all flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-calendar-week text-emerald-600"></i> Remises de la semaine
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 mb-6 shadow-sm">
    <form method="GET" action="{{ route('admin.historique.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
        @if(request('agent'))
            <input type="hidden" name="agent" value="{{ request('agent') }}">
        @endif

        <div class="lg:col-span-4">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Agent</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-emerald-600"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou matricule..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium">
            </div>
        </div>

        <div class="lg:col-span-3">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Action</label>
            <select name="action" class="w-full px-3 py-2.5 bg-slate-50 border {{ request('action') ? 'border-emerald-500 bg-emerald-50/30 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 font-medium' }} rounded-xl text-sm focus:outline-none focus:border-emerald-600 cursor-pointer">
                <option value="">Toutes les actions</option>
                @foreach(CarteEvenement::ACTIONS as $cle => $infos)
                    <option value="{{ $cle }}" @selected(request('action') === $cle)>{{ $infos['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="lg:col-span-2">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Du</label>
            <input type="date" name="du" value="{{ request('du') }}" class="w-full px-3 py-2.5 bg-slate-50 border @error('du') border-rose-500 @else border-slate-200 @enderror rounded-xl text-sm text-slate-700 focus:outline-none focus:border-emerald-600">
        </div>

        <div class="lg:col-span-2">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Au</label>
            <input type="date" name="au" value="{{ request('au') }}" class="w-full px-3 py-2.5 bg-slate-50 border @error('au') border-rose-500 @else border-slate-200 @enderror rounded-xl text-sm text-slate-700 focus:outline-none focus:border-emerald-600">
        </div>

        <div class="lg:col-span-1 flex gap-2">
            <button type="submit" class="flex-1 px-3 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-extrabold shadow-sm shadow-emerald-600/20 transition-all" title="Filtrer">
                <i class="fa-solid fa-filter text-xs"></i>
            </button>
            @if($filtresActifs)
                <a href="{{ route('admin.historique.index') }}" title="Effacer les filtres" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 text-sm font-bold transition-all">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            @endif
        </div>
    </form>

    @if($agentFiltre || request('agent'))
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
            <span class="font-extrabold uppercase tracking-wider text-slate-400 text-[11px]">Agent :</span>
            <a href="{{ request()->fullUrlWithQuery(['agent' => null]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors">
                {{ $agentFiltre?->nom_complet ?? 'Agent supprimé' }} {{ $agentFiltre ? '(' . $agentFiltre->matricule . ')' : '' }}
                <i class="fa-solid fa-xmark text-[10px]"></i>
            </a>
        </div>
    @endif
</div>

<!-- Résumé de la sélection -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach(CarteEvenement::ACTIONS as $cle => $infos)
        <a href="{{ request()->fullUrlWithQuery(['action' => $cle, 'page' => null]) }}"
           class="bg-white border {{ request('action') === $cle ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200' }} rounded-2xl p-4 shadow-xs hover:border-emerald-300 transition-all">
            <div class="flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                <i class="fa-solid {{ $infos['icone'] }}"></i> {{ $infos['label'] }}
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $resume[$cle] ?? 0 }}</div>
        </a>
    @endforeach
</div>

<!-- Journal -->
@if($evenements->count() > 0)
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-5">Heure</th>
                        <th class="py-4 px-4">Action</th>
                        <th class="py-4 px-4">Agent</th>
                        <th class="py-4 px-4">Matricule</th>
                        <th class="py-4 px-4">Enregistré par</th>
                        <th class="py-4 px-5 text-right"></th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @php($jourPrecedent = null)
                    @foreach($evenements as $evt)
                        @php($jour = $evt->created_at->format('Y-m-d'))
                        @if($jour !== $jourPrecedent)
                            @php($jourPrecedent = $jour)
                            <tr class="bg-slate-50/70 border-y border-slate-200">
                                <td colspan="6" class="py-2.5 px-5 text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
                                    <i class="fa-regular fa-calendar text-emerald-600 mr-1"></i>
                                    {{ ucfirst($evt->created_at->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
                                </td>
                            </tr>
                        @endif
                        @php($infos = CarteEvenement::ACTIONS[$evt->action] ?? ['label' => $evt->action, 'icone' => 'fa-circle', 'couleur' => 'slate'])
                        <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-5 font-mono text-slate-500 whitespace-nowrap">{{ $evt->created_at->format('H:i') }}</td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11px] font-bold {{ $couleurs[$infos['couleur']] }}">
                                    <i class="fa-solid {{ $infos['icone'] }}"></i> {{ $infos['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-extrabold text-slate-900">
                                {{ $evt->agent_nom }}
                                @unless($evt->agent_id)
                                    <span class="ml-1 text-[10px] font-semibold text-rose-500">(fiche supprimée)</span>
                                @endunless
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block bg-amber-50 border border-amber-300 text-amber-900 font-mono font-extrabold text-xs px-3 py-1 rounded-xl">{{ $evt->agent_matricule }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">{{ $evt->user_nom ?? '—' }}</td>
                            <td class="py-3 px-5 text-right whitespace-nowrap">
                                @if($evt->agent_id)
                                    <a href="{{ route('admin.historique.index', ['agent' => $evt->agent_id]) }}" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900" title="Tout l'historique de cet agent">
                                        <i class="fa-solid fa-list-ul"></i> Son historique
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-6">
        {{ $evenements->links() }}
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-sm">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <h3 class="font-extrabold text-slate-900">Aucune action enregistrée</h3>
        <p class="text-xs text-slate-500 mt-1">
            {{ $filtresActifs ? 'Aucun résultat pour ces filtres.' : 'Les impressions et remises de cartes apparaîtront ici.' }}
        </p>
    </div>
@endif

@endsection
