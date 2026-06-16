<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Fiu\ArchiveController as FiuArchiveController;
use App\Http\Controllers\Fiu\ComplianceReportController;
use App\Http\Controllers\Fiu\ComplianceTrackController;
use App\Http\Controllers\Fiu\DocumentReviewController;
use App\Http\Controllers\Fiu\FolderController as FiuFolderController;
use App\Http\Controllers\Fiu\ImmediateOutcomeController as FiuImmediateOutcomeController;
use App\Http\Controllers\Fiu\InstitutionController as FiuInstitutionController;
use App\Http\Controllers\Fiu\OutcomeAssignmentController;
use App\Http\Controllers\Fiu\UserController as FiuUserController;
use App\Http\Controllers\Institution\ArchiveController as InstitutionArchiveController;
use App\Http\Controllers\Institution\DocumentController as InstitutionDocumentController;
use App\Http\Controllers\Institution\FeedbackController;
use App\Http\Controllers\Institution\FolderController as InstitutionFolderController;
use App\Http\Controllers\Institution\ImmediateOutcomeController as InstitutionImmediateOutcomeController;
use App\Http\Controllers\Institution\UploadController;
use App\Http\Controllers\Fiu\EffectivenessFolderController;
use Illuminate\Support\Facades\Route;

// 1. Root Gateway Intercept
Route::redirect('/', '/login');

// 2. Authentication Core Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// =========================================================================
// 🏢 3. FIU COMMAND DESK DOMAIN (Protected Administration Spaces)
// =========================================================================
Route::middleware(['auth', 'role:fiu_admin,fiu_reviewer'])->prefix('fiu')->as('fiu.')->group(function (): void {
    
    // Dashboard route with array dependencies
    Route::get('/dashboard', function () {
        $stats = [
            'users' => 14, 
            'institutions' => 3, 
            'technicalFolders' => 20, 
            'effectivenessFolders' => 11, 
            'pending_reviews'  => 3,
            'changes_requested' => 2,
            'archived_documents' => 25
        ];
        $recentSubmissions = [
            [
                'institution' => 'ZIMRA', 
                'folder_name' => 'Tax Laundering Protocols', 
                'track' => 'Technical',
                'document' => 'Q2_AntiMoneyLaundering_Report.pdf',
                'immediate_outcome' => 'Immediate Outcome 1', 
                'submitted_at' => '10 mins ago',
                'status' => 'Pending Review'
            ]
        ];
        return view('fiu.dashboard', compact('stats', 'recentSubmissions'));
    })->name('dashboard');

    //  HIGH PRIORITY SPECIFIC TRACK GROUPS (Placed BEFORE resource wildcards)
    
    // Effectiveness Tracks Routing
      Route::prefix('tracks/effectiveness')->name('effectiveness.folders')->group(function (): void {
        Route::get('/', [EffectivenessFolderController::class, 'index'])->name('.index');
        Route::get('/{code}/documents/create', [EffectivenessFolderController::class, 'create'])->name('.documents.create');
        Route::post('/{code}/documents', [EffectivenessFolderController::class, 'store'])->name('.documents.store');
        Route::get('/{code}', [EffectivenessFolderController::class, 'show'])->name('.show');
    });

  // =========================================================================
    // Technical Compliance Tracks Routing
    // =========================================================================
    Route::prefix('tracks/technical-compliance')->name('technical-compliance.folders.')->group(function (): void {
        
        // Matches: GET /fiu/tracks/technical-compliance -> Name: fiu.technical-compliance.folders.index
        Route::get('/', [ComplianceTrackController::class, 'technicalIndex'])->name('index');
        
        // Matches: GET /fiu/tracks/technical-compliance/create -> Name: fiu.technical-compliance.folders.create
        Route::get('/create', [ComplianceTrackController::class, 'create'])->name('create');
        
        // Matches: POST /fiu/tracks/technical-compliance -> Name: fiu.technical-compliance.folders.store
        Route::post('/', [ComplianceTrackController::class, 'store'])->name('store');
        
        // Matches: GET /fiu/tracks/technical-compliance/{slug} -> Name: fiu.technical-compliance.folders.show
        Route::get('/{slug}', [ComplianceTrackController::class, 'show'])->name('show');
        
    });

    // Fallback Layout for base track definitions
    Route::get('tracks/{track}', [ComplianceTrackController::class, 'show'])->name('tracks.show');

    // Shared Resource Maps
    Route::resource('institutions', FiuInstitutionController::class);
    Route::resource('users', FiuUserController::class);
    Route::resource('tracks', ComplianceTrackController::class)->except(['show']); // Except handled above
    Route::resource('folders', FiuFolderController::class);
    Route::resource('outcomes', FiuImmediateOutcomeController::class);
    Route::resource('documents', DocumentReviewController::class);

    // Shared Operational Controls
    Route::get('outcome-assignments', [OutcomeAssignmentController::class, 'index'])->name('outcomes.assignments.index');
    Route::get('outcome-assignments/create', [OutcomeAssignmentController::class, 'create'])->name('outcomes.assignments.create');
    Route::post('outcome-assignments', [OutcomeAssignmentController::class, 'store'])->name('outcomes.assignments.store');

    Route::get('archive', [FiuArchiveController::class, 'index'])->name('archive.index');
    Route::get('reports/compliance', [ComplianceReportController::class, 'index'])->name('reports.compliance');

    
    //  Folders Architecture Routing Nodes (Linked to your original methods)
    Route::get('technical-compliance/folders/create', [ComplianceTrackController::class, 'create'])->name('technical-compliance.folders.create');
    Route::post('technical-compliance/folders/store', [ComplianceTrackController::class, 'store'])->name('technical-compliance.folders.store');

    //  Center Document Management Routing Matrix Nodes (Linked to the new operations)
    Route::get('documents/create', [ComplianceTrackController::class, 'createDocument'])->name('documents.create');
    Route::post('documents/store', [ComplianceTrackController::class, 'storeDocument'])->name('documents.store');
    
});



// =========================================================================
// 🏦 4. REPORTING INSTITUTIONS DOMAIN (ZIMRA, ZRP, JSC)
// =========================================================================
Route::middleware(['auth', 'role:institution_representative'])->prefix('institution')->name('institution.')->group(function () {
        
    Route::get('/dashboard', function () {
        $institution = [
            'name' => 'Zimbabwe Revenue Authority', 
            'portal_title' => 'ZIMRA Compliance Gateway',
            'short_name' => 'ZIMRA'
        ];
        $assignedImmediateOutcomes = [
            ['number' => 1, 'short_title' => 'Policy Framework Collaboration'],
            ['number' => 2, 'short_title' => 'Targeted Financial Sanctions Interception']
        ];
        $folders = [
            ['name' => 'Core Audits', 'track' => 'Technical', 'description' => 'Standard technical compliance reporting modules.'],
            ['name' => 'Asset Tracking Registry', 'track' => 'Effectiveness', 'description' => 'Targeted reporting modules tied directly to IO 2.', 'immediate_outcome' => 'Immediate Outcome 2']
        ];
        $submissions = [
            ['document' => 'Q2_AML_Filing_v1.pdf', 'folder' => 'Core Audits', 'status' => 'Pending Review', 'updated_at' => '2 hours ago']
        ];
        return view('institution.dashboard', compact('institution', 'assignedImmediateOutcomes', 'folders', 'submissions'));
    })->name('dashboard');

    Route::resource('folders', InstitutionFolderController::class)->only(['index', 'show']);
    Route::resource('outcomes', InstitutionImmediateOutcomeController::class)->only(['index', 'show']);
    Route::resource('documents', InstitutionDocumentController::class)->only(['index', 'show']);

    Route::prefix('tracks/technical-compliance')->name('tracks.technical-compliance')->group(function () {
        Route::get('/', [ComplianceTrackController::class, 'technicalIndex'])->name('.index');
        Route::get('/{slug}', [ComplianceTrackController::class, 'showTechnicalFolder'])->name('.show');
    });

    Route::get('uploads/create', [UploadController::class, 'create'])->name('uploads.create');
    Route::post('uploads', [UploadController::class, 'store'])->name('uploads.store');

    Route::get('feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('archive', [InstitutionArchiveController::class, 'index'])->name('archive.index');
});


// =========================================================================
// 👤 5. STANDARD USER ACCOUNT PROFILE MANAGEMENT ROUTING
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});