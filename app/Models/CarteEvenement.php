<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Une ligne de l'historique des cartes (impression, remise, annulation…).
 */
class CarteEvenement extends Model
{
    public const UPDATED_AT = null;

    public const IMPRIMEE = 'imprimee';
    public const REMISE = 'remise';
    public const ANNULATION_REMISE = 'annulation_remise';
    public const REINITIALISEE = 'reinitialisee';

    /** Libellé, icône et couleur de chaque action (pour l'affichage). */
    public const ACTIONS = [
        self::IMPRIMEE          => ['label' => 'Carte imprimée',         'icone' => 'fa-print',        'couleur' => 'amber'],
        self::REMISE            => ['label' => 'Carte remise',           'icone' => 'fa-circle-check', 'couleur' => 'emerald'],
        self::ANNULATION_REMISE => ['label' => 'Remise annulée',         'icone' => 'fa-rotate-left',  'couleur' => 'slate'],
        self::REINITIALISEE     => ['label' => 'Remise à « Non imprimée »', 'icone' => 'fa-eraser',    'couleur' => 'rose'],
    ];

    protected $table = 'carte_evenements';

    protected $fillable = [
        'agent_id', 'agent_matricule', 'agent_nom', 'action', 'user_id', 'user_nom',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Enregistre une action dans l'historique.
     */
    public static function enregistrer(Agent $agent, string $action): self
    {
        $user = Auth::user();

        return self::create([
            'agent_id'        => $agent->id,
            'agent_matricule' => $agent->matricule,
            'agent_nom'       => $agent->nom_complet,
            'action'          => $action,
            'user_id'         => $user?->id,
            'user_nom'        => $user?->name,
        ]);
    }
}
