<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicAgentController;
use Illuminate\Support\Facades\Route;

// Redirection par défaut
Route::get('/', function () {
    return redirect()->route('admin.agents.index');
});

// Authentification Super Admin (Invités uniquement)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
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
    Route::resource('agents', AgentController::class);
    Route::post('agents/import-excel', [AgentController::class, 'importExcel'])->name('agents.import-excel');
    Route::post('agents/import-zip', [AgentController::class, 'importZip'])->name('agents.import-zip');
});
