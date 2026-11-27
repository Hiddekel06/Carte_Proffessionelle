<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoriqueCarteController;
use App\Http\Controllers\PublicAgentController;
use Illuminate\Support\Facades\Route;

// Redirection par défaut
Route::get('/', function () {
    return redirect()->route('admin.agents.index');
});

// Authentification Super Admin (Invités uniquement)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // 20 tentatives max par minute et par adresse IP (en plus du blocage par compte)
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
});

// Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Vue Publique Fiche Agent (Lecture Seule via QR Code - Sans authentification)
Route::get('/agent/{uuid}', [PublicAgentController::class, 'show'])->name('agent.public.show');
Route::get('/agent/{uuid}/vcard', [PublicAgentController::class, 'vcard'])->name('agent.vcard');

// Espace Administration (Protégé par l'authentification Super Admin)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('import-excel', [AgentController::class, 'showImportExcel'])->name('agents.show-import-excel');
    Route::get('import-excel/template-xlsx', [AgentController::class, 'downloadTemplateXlsx'])->name('agents.template-xlsx');
    Route::get('import-excel/template-csv', [AgentController::class, 'downloadTemplateCsv'])->name('agents.template-csv');
    Route::get('import-excel/template', [AgentController::class, 'downloadTemplateXlsx'])->name('agents.template-excel'); // Rétrocompatibilité
    Route::get('import-zip', [AgentController::class, 'showImportZip'])->name('agents.show-import-zip');
    Route::get('agents/{agent}/download-qr', [AgentController::class, 'downloadQr'])->name('agents.download-qr');
    Route::get('agents/{agent}/badge', [AgentController::class, 'showBadge'])->name('agents.badge');
    Route::post('agents/{agent}/carte-imprimee', [AgentController::class, 'marquerImprimee'])->name('agents.carte-imprimee');
    Route::post('agents/etat-carte', [AgentController::class, 'changerEtatCarte'])->name('agents.etat-carte');
    Route::get('historique', [HistoriqueCarteController::class, 'index'])->name('historique.index');
    Route::resource('agents', AgentController::class)->except('show');
    // Pas de page « détail » : on redirige vers la fiche de modification
    Route::get('agents/{agent}', fn (\App\Models\Agent $agent) => redirect()->route('admin.agents.edit', $agent))
        ->name('agents.show');
    Route::post('agents/import-excel', [AgentController::class, 'importExcel'])->name('agents.import-excel');
    Route::post('agents/import-zip', [AgentController::class, 'importZip'])->name('agents.import-zip');
});
