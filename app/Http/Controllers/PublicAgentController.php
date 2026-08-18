<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;

class PublicAgentController extends Controller
{
    /**
     * Affiche la fiche publique d'un agent en lecture seule via son UUID (QR Code).
     */
    public function show(string $uuid)
    {
        $agent = Agent::where('uuid', $uuid)->firstOrFail();

        return view('agent.public_show', compact('agent'));
    }

    /**
     * Génère et télécharge la fiche de contact vCard (.vcf) de l'agent.
     */
    public function vcard(string $uuid)
    {
        $agent = Agent::where('uuid', $uuid)->firstOrFail();

        $nom    = mb_strtoupper($agent->nom);
        $prenom = ucwords(mb_strtolower($agent->prenom));

        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            "FN:{$prenom} {$nom}",
            "N:{$nom};{$prenom};;;",
        ];

        if ($agent->telephone) {
            $lines[] = "TEL;TYPE=WORK,VOICE:{$agent->telephone}";
        }
        if ($agent->email) {
            $lines[] = "EMAIL;TYPE=WORK:{$agent->email}";
        }
        if ($agent->structure_affectation) {
            $lines[] = "ORG:{$agent->structure_affectation}";
        }
        if ($agent->fonction) {
            $lines[] = "TITLE:{$agent->fonction}";
        }

        $lines[] = "NOTE:Matricule: {$agent->matricule}";
        $lines[] = 'END:VCARD';

        $vcf = implode("\r\n", $lines) . "\r\n";

        $filename = 'Agent_' . $agent->matricule . '.vcf';

        return response($vcf, 200, [
            'Content-Type'        => 'text/vcard; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
