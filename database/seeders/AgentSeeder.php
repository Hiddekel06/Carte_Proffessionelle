<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleAgents = [
            [
                'uuid' => (string) Str::uuid(),
                'matricule' => '123456A',
                'prenom' => 'Jean',
                'nom' => 'KOUASSI',
                'email' => 'jean.kouassi@gouv.ci',
                'telephone' => '+225 07 01 02 03 04',
                'structure_affectation' => 'Ministère de la Fonction Publique',
                'service' => 'Direction des Systèmes d\'Information',
                'fonction' => 'Ingénieur Informaticien',
                'poste' => 'Chef de Service',
                'date_integration_fp' => '2018-05-15',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'matricule' => '654321B',
                'prenom' => 'Aminata',
                'nom' => 'DIARRASSOUBA',
                'email' => 'aminata.diarra@gouv.ci',
                'telephone' => '+225 05 09 08 07 06',
                'structure_affectation' => 'Ministère de la Digitalisation',
                'service' => 'Direction de la Transformation Digitale',
                'fonction' => 'Analyste Développeur',
                'poste' => 'Responsable Projets',
                'date_integration_fp' => '2020-11-01',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'matricule' => '987654C',
                'prenom' => 'Koffi Marc',
                'nom' => 'YAO',
                'email' => 'marc.yao@gouv.ci',
                'telephone' => '+225 01 44 55 66 77',
                'structure_affectation' => 'Ministère de la Fonction Publique',
                'service' => 'Direction des Ressources Humaines',
                'fonction' => 'Gestionnaire des Carrières',
                'poste' => 'Directeur',
                'date_integration_fp' => '2015-02-10',
            ],
        ];

        foreach ($sampleAgents as $data) {
            Agent::updateOrCreate(
                ['matricule' => $data['matricule']],
                $data
            );
        }
    }
}
