@php($modeleActuel = old('modele_carte', $agent?->modele_carte))
<!-- Modèle de carte professionnelle -->
<div>
    <label class="block text-xs font-bold text-slate-800 mb-1">Modèle de carte</label>
    <select name="modele_carte" class="w-full px-3.5 py-2.5 bg-slate-50 border @error('modele_carte') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 text-sm focus:outline-none focus:border-emerald-600">
        <option value="" @selected(empty($modeleActuel))>Automatique (selon la fonction)</option>
        <option value="{{ \App\Models\Agent::MODELE_STANDARD }}" @selected($modeleActuel === \App\Models\Agent::MODELE_STANDARD)>Standard</option>
        <option value="{{ \App\Models\Agent::MODELE_INSPECTEUR }}" @selected($modeleActuel === \App\Models\Agent::MODELE_INSPECTEUR)>Inspecteur / Contrôleur du travail</option>
    </select>
    <p class="text-[11px] text-slate-500 mt-1">
        En automatique, le modèle inspecteur est appliqué si la fonction ou le poste contient « Inspecteur du travail » ou « Contrôleur du travail ».
        @if($agent)
            Modèle actuel : <strong>{{ $agent->modele_carte_effectif === \App\Models\Agent::MODELE_INSPECTEUR ? 'Inspecteur' : 'Standard' }}</strong>.
        @endif
    </p>
    @error('modele_carte')
        <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
    @enderror
</div>
