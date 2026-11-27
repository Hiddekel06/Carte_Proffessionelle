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
        'modele_carte',
        'carte_imprimee_le',
        'carte_remise_le',
        'photo',
    ];

    public const MODELE_STANDARD = 'standard';
    public const MODELE_INSPECTEUR = 'inspecteur';

    /** États de la carte professionnelle */
    public const ETAT_NON_IMPRIMEE = 'non_imprimee';
    public const ETAT_A_REMETTRE = 'a_remettre';
    public const ETAT_REMISE = 'remise';

    public const ETATS_CARTE = [
        self::ETAT_NON_IMPRIMEE => 'Non imprimée',
        self::ETAT_A_REMETTRE   => 'À remettre',
        self::ETAT_REMISE       => 'Remise',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_integration_fp' => 'date',
        'carte_imprimee_le' => 'datetime',
        'carte_remise_le' => 'datetime',
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

    /**
     * Modèle de carte réellement utilisé : le choix forcé sur la fiche,
     * sinon déduit de la fonction / du poste (config/carte.php).
     */
    public function getModeleCarteEffectifAttribute(): string
    {
        if (in_array($this->modele_carte, [self::MODELE_STANDARD, self::MODELE_INSPECTEUR], true)) {
            return $this->modele_carte;
        }

        $texte = Str::lower(Str::ascii(($this->fonction ?? '') . ' ' . ($this->poste ?? '')));
        foreach (config('carte.mots_cles_inspecteur', []) as $motCle) {
            if (str_contains($texte, Str::lower(Str::ascii($motCle)))) {
                return self::MODELE_INSPECTEUR;
            }
        }

        return self::MODELE_STANDARD;
    }

    /**
     * Nom tel qu'affiché sur la carte : « Prénom NOM ».
     */
    public function getNomCarteAttribute(): string
    {
        $prenom = mb_convert_case(mb_strtolower($this->prenom ?? ''), MB_CASE_TITLE);

        return trim($prenom . ' ' . mb_strtoupper($this->nom ?? ''));
    }

    /**
     * État actuel de la carte : non_imprimee, a_remettre ou remise.
     */
    public function getEtatCarteAttribute(): string
    {
        if ($this->carte_remise_le) {
            return self::ETAT_REMISE;
        }

        return $this->carte_imprimee_le ? self::ETAT_A_REMETTRE : self::ETAT_NON_IMPRIMEE;
    }

    /**
     * Filtre les agents selon l'état de leur carte.
     */
    public function scopeEtatCarte($query, string $etat)
    {
        return match ($etat) {
            self::ETAT_NON_IMPRIMEE => $query->whereNull('carte_imprimee_le'),
            self::ETAT_A_REMETTRE   => $query->whereNotNull('carte_imprimee_le')->whereNull('carte_remise_le'),
            self::ETAT_REMISE       => $query->whereNotNull('carte_remise_le'),
            default                 => $query,
        };
    }

    /**
     * Matricule tel qu'affiché sur la carte : 123456A -> 123456/A.
     */
    public function getMatriculeCarteAttribute(): string
    {
        $m = strtoupper((string) $this->matricule);

        return preg_match('/^([0-9]{6})([A-Z])$/', $m, $r) ? "{$r[1]}/{$r[2]}" : $m;
    }
}
