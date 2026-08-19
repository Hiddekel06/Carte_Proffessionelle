<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Agent extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'matricule',
        'prenom',
        'nom',
        'email',
        'telephone',
        'structure_affectation',
        'service',
        'fonction',
        'poste',
        'date_integration_fp',
        'photo',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_integration_fp' => 'date',
    ];

    /**
     * Auto-génération de l'UUID à la création de l'agent.
     */
    protected static function booted(): void
    {
        static::creating(function ($agent) {
            if (empty($agent->uuid)) {
                $agent->uuid = (string) Str::uuid();
            }
            // Nettoyage et mise en majuscule du matricule (ex: 123456a -> 123456A)
            if (!empty($agent->matricule)) {
                $agent->matricule = strtoupper(trim($agent->matricule));
            }
        });
    }

    /**
     * Accesseur pour le nom complet.
     */
    public function getNomCompletAttribute(): string
    {
        return mb_strtoupper($this->nom) . ' ' . ucwords(mb_strtolower($this->prenom));
    }

    /**
     * Accesseur pour l'URL de la photo ou un avatar par défaut.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && (Storage::disk('public')->exists($this->photo) || file_exists(public_path('storage/' . $this->photo)))) {
            return asset('storage/' . $this->photo);
        }
        
        // Initiales pour l'avatar SVG par défaut
        $p = mb_substr($this->prenom ?? '', 0, 1);
        $n = mb_substr($this->nom ?? '', 0, 1);
        $initials = urlencode(($p ?: 'A') . ($n ?: 'G'));
        return "https://ui-avatars.com/api/?name={$initials}&background=00853f&color=ffffff&size=200&bold=true";
    }

    /**
     * Accesseur pour l'URL de la fiche publique liée au QR Code.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('agent.public.show', ['uuid' => $this->uuid]);
    }
}
