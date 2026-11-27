<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\CarteEvenement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Historique des actions sur les cartes : qui a imprimé / remis quelle carte, et quand.
 */
class HistoriqueCarteController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date'],
        ]);

        $query = CarteEvenement::query();

        // Recherche par nom ou matricule
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('agent_nom', 'like', "%{$search}%")
                  ->orWhere('agent_matricule', 'like', "%{$search}%");
            });
        }

        // Un agent précis (lien depuis la liste des agents)
        $agentFiltre = null;
        if ($request->filled('agent')) {
            $agentFiltre = Agent::find($request->agent);
            $query->where('agent_id', $request->agent);
        }

        // Type d'action
        if (array_key_exists((string) $request->action, CarteEvenement::ACTIONS)) {
            $query->where('action', $request->action);
        }

        // Période
        if ($request->filled('du')) {
            $query->where('created_at', '>=', Carbon::parse($request->du)->startOfDay());
        }
        if ($request->filled('au')) {
            $query->where('created_at', '<=', Carbon::parse($request->au)->endOfDay());
        }

        // Résumé de la sélection (par type d'action)
        $resume = (clone $query)
            ->selectRaw('action, count(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action');

        $evenements = $query->latest('created_at')->latest('id')
            ->paginate(50)
            ->withQueryString();

        return view('admin.historique.index', compact('evenements', 'resume', 'agentFiltre'));
    }
}
