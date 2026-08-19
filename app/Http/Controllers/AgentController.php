<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use ZipArchive;

class AgentController extends Controller
{
    /**
     * Liste et recherche des agents pour l'Administration.
     */
    public function index(Request $request)
    {
        $query = Agent::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('structure_affectation', 'like', "%{$search}%")
                  ->orWhere('service', 'like', "%{$search}%")
                  ->orWhere('fonction', 'like', "%{$search}%")
                  ->orWhere('poste', 'like', "%{$search}%");
            });
        }

        $agents = $query->orderBy('nom', 'asc')->paginate(15)->withQueryString();

        return view('admin.agents.index', compact('agents'));
    }

    /**
     * Formulaire de création manuelle d'un agent.
     */
    public function create()
    {
        return view('admin.agents.create');
    }

    /**
     * Enregistrement d'un nouvel agent.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => [
                'required',
                'string',
                'regex:/^[0-9]{6}[A-Za-z]$/',
                'unique:agents,matricule',
            ],
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'structure_affectation' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'poste' => 'nullable|string|max:255',
            'date_integration_fp' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'matricule.regex' => 'Le matricule doit comporter exactement 6 chiffres suivis d\'une lettre (ex: 123456A).',
            'matricule.unique' => 'Ce matricule existe déjà dans la base de données.',
        ]);

        $validated['matricule'] = strtoupper(trim($validated['matricule']));

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
        }

        $agent = Agent::create($validated);

        return redirect()->route('admin.agents.index')
            ->with('success', "L'agent {$agent->nom_complet} (Matricule: {$agent->matricule}) a été créé avec succès.");
    }

    /**
     * Formulaire d'édition d'un agent.
     */
    public function edit(Agent $agent)
    {
        return view('admin.agents.edit', compact('agent'));
    }

    /**
     * Mise à jour des informations d'un agent.
     */
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'matricule' => [
                'required',
                'string',
                'regex:/^[0-9]{6}[A-Za-z]$/',
                Rule::unique('agents', 'matricule')->ignore($agent->id),
            ],
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'structure_affectation' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'poste' => 'nullable|string|max:255',
            'date_integration_fp' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'matricule.regex' => 'Le matricule doit comporter exactement 6 chiffres suivis d\'une lettre (ex: 123456A).',
        ]);

        $validated['matricule'] = strtoupper(trim($validated['matricule']));

        if ($request->hasFile('photo')) {
            // Suppression de l'ancienne photo si existante
            if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
                Storage::disk('public')->delete($agent->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
        }

        $agent->update($validated);

        return redirect()->route('admin.agents.index')
            ->with('success', "La fiche de l'agent {$agent->nom_complet} a été mise à jour.");
    }

    /**
     * Suppression d'un agent.
     */
    public function destroy(Agent $agent)
    {
        if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
            Storage::disk('public')->delete($agent->photo);
        }

        $nom = $agent->nom_complet;
        $agent->delete();

        return redirect()->route('admin.agents.index')
            ->with('success', "L'agent {$nom} a été supprimé.");
    }

    /**
     * Vue d'importation Excel / CSV.
     */
    public function showImportExcel()
    {
        return view('admin.agents.import_excel');
    }

    /**
     * Téléchargement du modèle officiel Excel (.xlsx).
     */
    public function downloadTemplateXlsx()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Modèle Import Agents');

        // Colonnes
        $headers = [
            'A1' => 'matricule',
            'B1' => 'prenom',
            'C1' => 'nom',
            'D1' => 'structure_affectation',
            'E1' => 'service',
            'F1' => 'fonction',
            'G1' => 'poste',
            'H1' => 'telephone',
            'I1' => 'email',
            'J1' => 'date_integration_fp',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style de l'en-tête (Vert institutionnel et texte blanc)
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '052E16'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        // Exemples d'agents
        $examples = [
            ['123456A', 'Kouassi Jean', 'KOUAME', 'Ministère de la Fonction Publique', 'Direction des Systèmes d\'Information', 'Analyste Développeur', 'Directeur', '+2250707070707', 'jean.kouame@fonctionpublique.gouv.ci', '2018-04-15'],
            ['654321B', 'Aminata', 'TRAORE', 'Ministère des Finances', 'Direction des Ressources Humaines', 'Gestionnaire RH', 'Chef de Service', '+2250505050505', 'aminata.traore@fonctionpublique.gouv.ci', '2020-10-01'],
            ['789123C', 'Marcelle Aya', 'N\'GUESSAN', 'Ministère de l\'Éducation', 'Direction de la Formation', 'Formateur', 'Inspecteur', '+2250101010101', 'marcelle.nguessan@fonctionpublique.gouv.ci', '2015-02-20'],
            ['456789D', 'Yao Patrick', 'KOUADIO', 'Ministère de la Santé', 'Service Courrier', 'Agent Administratif', '', '', '', ''], // Démonstration avec champs optionnels vides
        ];

        $rowNum = 2;
        foreach ($examples as $row) {
            $colLetter = 'A';
            foreach ($row as $val) {
                $sheet->setCellValueExplicit($colLetter . $rowNum, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $colLetter++;
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(20);
            $rowNum++;
        }

        // Auto-dimensionnement des colonnes
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'modele_importation_agents.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Téléchargement du fichier modèle CSV.
     */
    public function downloadTemplateCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modele_importation_agents.csv"',
        ];

        $columns = ['matricule', 'prenom', 'nom', 'structure_affectation', 'service', 'fonction', 'poste', 'telephone', 'email', 'date_integration_fp'];
        
        $examples = [
            ['123456A', 'Kouassi Jean', 'KOUAME', 'Ministère de la Fonction Publique', 'Direction des Systèmes d\'Information', 'Analyste Développeur', 'Directeur', '+2250707070707', 'jean.kouame@fonctionpublique.gouv.ci', '2018-04-15'],
            ['654321B', 'Aminata', 'TRAORE', 'Ministère des Finances', 'Direction des Ressources Humaines', 'Gestionnaire RH', 'Chef de Service', '+2250505050505', 'aminata.traore@fonctionpublique.gouv.ci', '2020-10-01'],
            ['789123C', 'Marcelle Aya', 'N\'GUESSAN', 'Ministère de l\'Éducation', 'Direction de la Formation', 'Formateur', 'Inspecteur', '+2250101010101', 'marcelle.nguessan@fonctionpublique.gouv.ci', '2015-02-20'],
            ['456789D', 'Yao Patrick', 'KOUADIO', 'Ministère de la Santé', 'Service Courrier', 'Agent Administratif', '', '', '', ''],
        ];

        $callback = function() use ($columns, $examples) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8
            fputcsv($file, $columns, ';');
            foreach ($examples as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Vue d'importation massive des photos ZIP.
     */
    public function showImportZip()
    {
        return view('admin.agents.import_zip');
    }

    /**
     * Vue de conception et impression du Badge PVC (Recto / Verso).
     */
    public function showBadge(Agent $agent)
    {
        return view('admin.agents.badge', compact('agent'));
    }

    /**
     * Téléchargement direct de l'image PNG brute du QR Code seul (pour Word, CV, etc.).
     */
    public function downloadQr(Agent $agent)
    {
        // QR Code brut haute résolution (800x800), noir sur blanc pur
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=800x800&data=" . urlencode($agent->public_url) . "&color=000000&bgcolor=ffffff";
        
        $imageData = @file_get_contents($qrUrl);

        if (!$imageData) {
            return back()->with('error', "Impossible de récupérer l'image du QR Code.");
        }

        $fileName = 'QR_' . $agent->matricule . '.png';

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Traitement de l'importation de fichier Excel/CSV.
     */
    /**
     * Traitement intelligent de l'importation de fichier Excel/CSV avec rapport détaillé.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240', // 10 Mo
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        $rows = [];

        // Lecture intelligente selon le type de fichier
        if (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);
            } catch (\Exception $e) {
                return redirect()->route('admin.agents.show-import-excel')
                    ->with('error', "Erreur de lecture du fichier Excel : " . $e->getMessage());
            }
        } else {
            // Lecture CSV avec détection de délimiteur (;, ,, tab)
            if (($handle = fopen($filePath, 'r')) !== FALSE) {
                $firstLine = fgets($handle);
                rewind($handle);

                $delimiter = ';';
                if (substr_count($firstLine, ',') > substr_count($firstLine, ';')) {
                    $delimiter = ',';
                } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ';')) {
                    $delimiter = "\t";
                }

                while (($data = fgetcsv($handle, 2000, $delimiter)) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        if (empty($rows) || count($rows) < 2) {
            return redirect()->route('admin.agents.show-import-excel')
                ->with('error', "Le fichier importé est vide ou ne contient aucune ligne de données exploitable.");
        }

        // Ligne d'en-tête (normalisation des noms de colonnes)
        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $index => $col) {
            if ($col === null) continue;
            // Nettoyage complet (minuscule, sans accents, sans caractères spéciaux)
            $cleanCol = mb_strtolower(trim($col));
            $cleanCol = preg_replace('/[éèêë]/u', 'e', $cleanCol);
            $cleanCol = preg_replace('/[àâä]/u', 'a', $cleanCol);
            $cleanCol = preg_replace('/[îï]/u', 'i', $cleanCol);
            $cleanCol = preg_replace('/[ôö]/u', 'o', $cleanCol);
            $cleanCol = preg_replace('/[ûü]/u', 'u', $cleanCol);
            $cleanCol = preg_replace('/[^a-z0-9_]/', '', $cleanCol);
            if (!empty($cleanCol)) {
                $headerMap[$cleanCol] = $index;
            }
        }

        $addedCount = 0;
        $updatedCount = 0;
        $failures = [];
        $lineNumber = 1; // 1 correspond à l'en-tête

        foreach ($rows as $data) {
            $lineNumber++;

            // Ignorer les lignes totalement vides
            if (empty(array_filter($data, fn($v) => !is_null($v) && trim((string)$v) !== ''))) {
                continue;
            }

            // Récupérateur intelligent de valeur
            $getVal = function ($possibleKeys) use ($headerMap, $data) {
                foreach ((array)$possibleKeys as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $val = trim((string)$data[$headerMap[$k]]);
                        return $val !== '' ? $val : null;
                    }
                }
                return null;
            };

            $matricule = $getVal(['matricule', 'mat', 'nummatricule', 'codeagent']);
            $prenom = $getVal(['prenom', 'prenoms', 'firstname', 'first_name']);
            $nom = $getVal(['nom', 'noms', 'lastname', 'last_name', 'nomdefamille']);
            $email = $getVal(['email', 'courriel', 'mail', 'emailprofessionnel']);
            $telephone = $getVal(['telephone', 'tel', 'phone', 'contact', 'mobile', 'cellulaire']);
            $structure = $getVal(['structureaffectation', 'structure', 'ministere', 'departement', 'affectation']);
            $service = $getVal(['service', 'direction', 'division', 'unite', 'departementservice']);
            $fonction = $getVal(['fonction', 'profession', 'metier', 'corps', 'grade']);
            $poste = $getVal(['poste', 'titre', 'emploi', 'role', 'responsabilite', 'charge']);
            $dateIntegration = $getVal(['dateintegrationfp', 'dateintegration', 'datepriseservice', 'integration', 'dateentree']);

            // Validation stricte uniquement sur les champs MANDATORY (Matricule, Nom, Prénom)
            $rowErrors = [];

            if (empty($matricule)) {
                $rowErrors[] = "Matricule manquant";
            } else {
                $cleanMatricule = strtoupper(preg_replace('/\s+/', '', (string)$matricule));
                if (!preg_match('/^[0-9]{6}[A-Z]$/', $cleanMatricule)) {
                    $rowErrors[] = "Format matricule invalide ('{$matricule}' attendu 6 chiffres + 1 lettre, ex: 123456A)";
                } else {
                    $matricule = $cleanMatricule;
                }
            }

            if (empty($nom)) {
                $rowErrors[] = "Nom manquant";
            }

            if (empty($prenom)) {
                $rowErrors[] = "Prénom manquant";
            }

            // Validation douce des champs optionnels (si non vides)
            $validatedEmail = null;
            if (!empty($email)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validatedEmail = $email;
                }
                // Si l'email est mal formaté, on le nettoie ou on l'ignore sans bloquer l'enregistrement
            }

            // Gestion intelligente de la date (formats: YYYY-MM-DD, DD/MM/YYYY, etc.)
            $formattedDate = null;
            if (!empty($dateIntegration)) {
                // Remplacer les slashs par des tirets si JJ/MM/AAAA
                if (preg_match('/^(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{4})$/', $dateIntegration, $matches)) {
                    $formattedDate = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
                } else {
                    $timestamp = strtotime($dateIntegration);
                    if ($timestamp !== false) {
                        $formattedDate = date('Y-m-d', $timestamp);
                    }
                }
            }

            // En cas d'erreur sur un champ OBLIGATOIRE
            if (!empty($rowErrors)) {
                $failures[] = [
                    'line' => $lineNumber,
                    'matricule' => $matricule ?? 'N/A',
                    'nom' => trim(($nom ?? '') . ' ' . ($prenom ?? '')) ?: 'N/A',
                    'reason' => implode(', ', $rowErrors),
                ];
                continue;
            }

            // Insertion ou Mise à jour
            $existingAgent = Agent::where('matricule', $matricule)->first();

            if ($existingAgent) {
                $existingAgent->update([
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $validatedEmail ?? $existingAgent->email,
                    'telephone' => $telephone ?? $existingAgent->telephone,
                    'structure_affectation' => $structure ?? $existingAgent->structure_affectation,
                    'service' => $service ?? $existingAgent->service,
                    'fonction' => $fonction ?? $existingAgent->fonction,
                    'poste' => $poste ?? $existingAgent->poste,
                    'date_integration_fp' => $formattedDate ?? $existingAgent->date_integration_fp,
                ]);
                $updatedCount++;
            } else {
                Agent::create([
                    'matricule' => $matricule,
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $validatedEmail,
                    'telephone' => $telephone,
                    'structure_affectation' => $structure,
                    'service' => $service,
                    'fonction' => $fonction,
                    'poste' => $poste,
                    'date_integration_fp' => $formattedDate,
                ]);
                $addedCount++;
            }
        }

        $importReport = [
            'added' => $addedCount,
            'updated' => $updatedCount,
            'failed' => count($failures),
            'failures' => $failures,
            'total' => $addedCount + $updatedCount + count($failures),
        ];

        return redirect()->route('admin.agents.show-import-excel')
            ->with('import_report', $importReport);
    }

    /**
     * Traitement de l'importation massive des photos via fichier ZIP.
     */
    public function importZip(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip|max:50000',
        ]);

        $zipFile = $request->file('zip_file');
        $zip = new ZipArchive;

        if ($zip->open($zipFile->getRealPath()) === TRUE) {
            $matchedCount = 0;
            $extractPath = storage_path('app/public/photos');

            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $basename = pathinfo($filename, PATHINFO_BASENAME);
                $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    // Extraire le matricule (ex: 123456A.jpg -> 123456A)
                    $matriculeCandidate = strtoupper(pathinfo($basename, PATHINFO_FILENAME));

                    if (preg_match('/^[0-9]{6}[A-Z]$/', $matriculeCandidate)) {
                        $agent = Agent::where('matricule', $matriculeCandidate)->first();
                        if ($agent) {
                            $newFileName = 'photos/' . $matriculeCandidate . '.' . $extension;
                            file_put_contents(storage_path('app/public/' . $newFileName), $zip->getFromIndex($i));
                            $agent->update(['photo' => $newFileName]);
                            $matchedCount++;
                        }
                    }
                }
            }

            $zip->close();

            return redirect()->route('admin.agents.index')
                ->with('success', "Extraction du ZIP réussie : {$matchedCount} photos associées à leurs agents.");
        }

        return redirect()->route('admin.agents.index')
            ->with('error', "Impossible d'ouvrir le fichier ZIP.");
    }
}
