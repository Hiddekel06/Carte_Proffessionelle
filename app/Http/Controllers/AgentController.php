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

        // 1. Recherche globale (texte libre)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('structure_affectation', 'like', "%{$search}%")
                  ->orWhere('service', 'like', "%{$search}%")
                  ->orWhere('fonction', 'like', "%{$search}%")
                  ->orWhere('poste', 'like', "%{$search}%");
            });
        }

        // 2. Filtre par Structure / Ministère
        if ($request->filled('structure')) {
            $query->where('structure_affectation', $request->structure);
        }

        // 3. Filtre par Service / Direction
        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }

        // 4. Filtre par Fonction
        if ($request->filled('fonction')) {
            $query->where('fonction', $request->fonction);
        }

        // 5. Filtre par Poste
        if ($request->filled('poste')) {
            $query->where('poste', $request->poste);
        }

        // 6. Filtre par Présence de photo
        if ($request->filled('has_photo')) {
            if ($request->has_photo === 'yes') {
                $query->whereNotNull('photo')->where('photo', '!=', '');
            } elseif ($request->has_photo === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('photo')->orWhere('photo', '');
                });
            }
        }

        // 7. Tri personnalisé
        $sortBy = $request->get('sort', 'nom_asc');
        switch ($sortBy) {
            case 'nom_desc':
                $query->orderBy('nom', 'desc')->orderBy('prenom', 'desc');
                break;
            case 'matricule_asc':
                $query->orderBy('matricule', 'asc');
                break;
            case 'matricule_desc':
                $query->orderBy('matricule', 'desc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nom_asc':
            default:
                $query->orderBy('nom', 'asc')->orderBy('prenom', 'asc');
                break;
        }

        $agents = $query->paginate(15)->withQueryString();

        // Récupération des listes d'options distinctes pour les sélecteurs
        $structures = Agent::whereNotNull('structure_affectation')
            ->where('structure_affectation', '!=', '')
            ->distinct()
            ->orderBy('structure_affectation')
            ->pluck('structure_affectation');

        $services = Agent::whereNotNull('service')
            ->where('service', '!=', '')
            ->distinct()
            ->orderBy('service')
            ->pluck('service');

        $fonctions = Agent::whereNotNull('fonction')
            ->where('fonction', '!=', '')
            ->distinct()
            ->orderBy('fonction')
            ->pluck('fonction');

        $postes = Agent::whereNotNull('poste')
            ->where('poste', '!=', '')
            ->distinct()
            ->orderBy('poste')
            ->pluck('poste');

        // Compteurs rapides pour les badges de statut
        $totalAgents = Agent::count();
        $totalWithPhoto = Agent::whereNotNull('photo')->where('photo', '!=', '')->count();
        $totalWithoutPhoto = $totalAgents - $totalWithPhoto;

        return view('admin.agents.index', compact(
            'agents',
            'structures',
            'services',
            'fonctions',
            'postes',
            'totalAgents',
            'totalWithPhoto',
            'totalWithoutPhoto'
        ));
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'matricule.regex' => 'Le matricule doit comporter exactement 6 chiffres suivis d\'une lettre (ex: 123456A).',
            'matricule.unique' => 'Ce matricule existe déjà dans la base de données.',
            'photo.max' => 'La photo ne doit pas dépasser 10 Mo.',
        ]);

        $validated['matricule'] = strtoupper(trim($validated['matricule']));

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->processPhoto($request->file('photo'));
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'matricule.regex' => 'Le matricule doit comporter exactement 6 chiffres suivis d\'une lettre (ex: 123456A).',
            'photo.max' => 'La photo ne doit pas dépasser 10 Mo.',
        ]);

        $validated['matricule'] = strtoupper(trim($validated['matricule']));

        if ($request->hasFile('photo')) {
            // Suppression de l'ancienne photo si existante
            if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
                Storage::disk('public')->delete($agent->photo);
            }
            $validated['photo'] = $this->processPhoto($request->file('photo'));
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
     * Traitement de l'importation massive des photos via fichier ZIP avec rapport détaillé.
     */
    public function importZip(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|file|max:51200', // 50 Mo
        ], [
            'zip_file.required' => 'Veuillez sélectionner un fichier archive ZIP.',
            'zip_file.max' => 'L\'archive ZIP ne doit pas dépasser 50 Mo.',
        ]);

        $zipFile = $request->file('zip_file');
        $zip = new ZipArchive;

        if ($zip->open($zipFile->getRealPath()) !== TRUE) {
            return redirect()->route('admin.agents.show-import-zip')
                ->with('error', "Impossible d'ouvrir le fichier ZIP. Assurez-vous qu'il s'agit d'une archive ZIP valide.");
        }

        $matchedList = [];
        $failedList = [];
        $ignoredList = [];
        $totalFiles = $zip->numFiles;

        $extractPath = storage_path('app/public/photos');
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        // Cache des matricules existants en base (clé: MATRICULE_UPPER, valeur: Agent Model)
        $agentsMap = Agent::all()->keyBy(function ($item) {
            return strtoupper(trim($item->matricule));
        });

        for ($i = 0; $i < $totalFiles; $i++) {
            $entryName = $zip->getNameIndex($i);

            // Ignorer les dossiers ou les fichiers cachés macOS (__MACOSX, .DS_Store, etc.)
            if (str_ends_with($entryName, '/') || str_contains($entryName, '__MACOSX') || str_starts_with(basename($entryName), '.')) {
                continue;
            }

            $basename = pathinfo($entryName, PATHINFO_BASENAME);
            $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));

            // Filtrer uniquement les extensions d'image autorisées
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $ignoredList[] = [
                    'file' => $basename,
                    'reason' => 'Format non supporté (seuls JPG, PNG, WEBP sont acceptés)',
                ];
                continue;
            }

            // Récupérer le nom du fichier sans extension et le nettoyer
            $rawName = pathinfo($basename, PATHINFO_FILENAME);
            // Nettoyage de base : suppression des espaces et mise en majuscule
            $cleanName = strtoupper(trim(preg_replace('/\s+/', '', $rawName)));
            // Nettoyage agressif : suppression de tous caractères non alphanumériques (tirets, underscores, etc.)
            $alphaNumName = preg_replace('/[^0-9A-Z]/', '', $cleanName);

            // Stratégies de recherche du matricule :
            $matchedMatricule = null;

            // Stratégie 1 : Correspondance directe avec le nom nettoyé
            if (isset($agentsMap[$cleanName])) {
                $matchedMatricule = $cleanName;
            }
            // Stratégie 2 : Correspondance avec le nom alphanumérique pur (ex: 123456_A -> 123456A)
            elseif (!empty($alphaNumName) && isset($agentsMap[$alphaNumName])) {
                $matchedMatricule = $alphaNumName;
            }
            // Stratégie 3 : Détection d'un motif 6 chiffres + 1 lettre n'importe où dans le nom (ex: Photo_123456A_final.jpg)
            elseif (preg_match('/([0-9]{6}[A-Z])/i', $cleanName, $matches)) {
                $candidate = strtoupper($matches[1]);
                if (isset($agentsMap[$candidate])) {
                    $matchedMatricule = $candidate;
                }
            }
            // Stratégie 4 : Recherche par matricule contenu dans le nom de fichier
            if (!$matchedMatricule) {
                foreach ($agentsMap as $dbMatricule => $agentObj) {
                    if (str_contains($cleanName, $dbMatricule) || str_contains($alphaNumName, $dbMatricule)) {
                        $matchedMatricule = $dbMatricule;
                        break;
                    }
                }
            }

            if (!$matchedMatricule) {
                $failedList[] = [
                    'file' => $basename,
                    'matricule_extrait' => $cleanName,
                    'reason' => 'Aucun agent trouvé avec ce matricule dans la base de données',
                ];
                continue;
            }

            // L'agent existe, on extrait l'image
            $agent = $agentsMap[$matchedMatricule];
            $imageContent = $zip->getFromIndex($i);

            if ($imageContent === false) {
                $failedList[] = [
                    'file' => $basename,
                    'matricule_extrait' => $matchedMatricule,
                    'reason' => 'Impossible de lire le contenu de l\'image dans l\'archive',
                ];
                continue;
            }

            // Optimisation de l'image (via GD si disponible, sinon stockage brut)
            $newFileName = 'photos/' . \Illuminate\Support\Str::uuid() . '.jpg';
            $destPath = storage_path('app/public/' . $newFileName);

            $processed = false;
            if (extension_loaded('gd')) {
                $src = @imagecreatefromstring($imageContent);
                if ($src) {
                    $origW = imagesx($src);
                    $origH = imagesy($src);

                    $maxWidth = 800;
                    $maxHeight = 1000;
                    $ratio = min($maxWidth / $origW, $maxHeight / $origH, 1.0);
                    $newW = (int) round($origW * $ratio);
                    $newH = (int) round($origH * $ratio);

                    $dst = imagecreatetruecolor($newW, $newH);
                    $white = imagecolorallocate($dst, 255, 255, 255);
                    imagefill($dst, 0, 0, $white);
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                    imagedestroy($src);

                    imagejpeg($dst, $destPath, 92);
                    imagedestroy($dst);
                    $processed = true;
                }
            }

            if (!$processed) {
                $newFileName = 'photos/' . $matchedMatricule . '.' . $extension;
                file_put_contents(storage_path('app/public/' . $newFileName), $imageContent);
            }

            // Supprimer l'ancienne photo si existante
            if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
                Storage::disk('public')->delete($agent->photo);
            }

            // Mettre à jour l'agent
            $agent->update(['photo' => $newFileName]);

            $matchedList[] = [
                'matricule' => $matchedMatricule,
                'nom' => $agent->nom_complet,
                'file' => $basename,
            ];
        }

        $zip->close();

        $zipReport = [
            'total' => count($matchedList) + count($failedList) + count($ignoredList),
            'matched' => count($matchedList),
            'failed' => count($failedList),
            'ignored' => count($ignoredList),
            'matched_list' => $matchedList,
            'failures' => $failedList,
            'ignored_list' => $ignoredList,
        ];

        return redirect()->route('admin.agents.show-import-zip')
            ->with('zip_report', $zipReport);
    }

    /**
     * Traite et optimise une photo uploadée :
     * - Corrige l'orientation EXIF (évite les rotations parasites)
     * - Redimensionne à max 800×1000 px (portrait) en conservant les proportions
     * - Sauvegarde en JPEG qualité 92 (net + léger)
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  Chemin relatif au disk 'public' (ex: photos/xxxxxx.jpg)
     */
    private function processPhoto(\Illuminate\Http\UploadedFile $file): string
    {
        $maxWidth  = 800;
        $maxHeight = 1000;
        $quality   = 92;

        $mime    = $file->getMimeType();
        $tmpPath = $file->getRealPath();

        // Chargement de l'image source selon son type MIME
        $src = match (true) {
            str_contains($mime, 'jpeg') => @imagecreatefromjpeg($tmpPath),
            str_contains($mime, 'png')  => @imagecreatefrompng($tmpPath),
            str_contains($mime, 'webp') => @imagecreatefromwebp($tmpPath),
            default                     => @imagecreatefromjpeg($tmpPath),
        };

        // Si GD échoue, fallback sur le stockage brut
        if (!$src) {
            return $file->store('photos', 'public');
        }

        // Correction de l'orientation EXIF (photos prises au téléphone souvent mal orientées)
        if (function_exists('exif_read_data') && str_contains($mime, 'jpeg')) {
            $exif        = @exif_read_data($tmpPath);
            $orientation = $exif['Orientation'] ?? 1;
            $src = match ($orientation) {
                3       => imagerotate($src, 180, 0),
                6       => imagerotate($src, -90, 0),
                8       => imagerotate($src,  90, 0),
                default => $src,
            };
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Calcul du ratio pour ne pas dépasser les dimensions maximales (jamais d'agrandissement)
        $ratio = min($maxWidth / $origW, $maxHeight / $origH, 1.0);
        $newW  = (int) round($origW * $ratio);
        $newH  = (int) round($origH * $ratio);

        // Création de l'image redimensionnée avec rééchantillonnage de haute qualité
        $dst   = imagecreatetruecolor($newW, $newH);

        // Fond blanc pour éviter les zones noires sur les PNG avec transparence
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        // Nom unique et chemin de sauvegarde
        $filename    = 'photos/' . \Illuminate\Support\Str::uuid() . '.jpg';
        $storagePath = storage_path('app/public/' . $filename);

        if (!is_dir(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }

        imagejpeg($dst, $storagePath, $quality);
        imagedestroy($dst);

        return $filename;
    }
}
