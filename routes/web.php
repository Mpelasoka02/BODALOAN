<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverApiController;
use App\Http\Controllers\Api\LoanApiController;
use App\Http\Controllers\Api\MotorcycleApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverMarketController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PublicMarketplaceController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
});

Route::get('/home', [PublicMarketplaceController::class, 'index'])->name('home');
Route::get('/home/{motorcycle}', [PublicMarketplaceController::class, 'show'])->name('marketplace.show');

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'sw'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/verification', function () {
        return view('auth.pending-approval');
    })->name('verification.form');
    Route::post('/verification/submit', [ProfileController::class, 'submitVerification'])->name('verification.submit');
});

Route::middleware(['auth', 'approval'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/marketplace', [DashboardController::class, 'marketplace'])->name('marketplace');

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::post('/firebase/register-token', [FirebaseController::class, 'registerToken'])->name('firebase.registerToken');
    Route::post('/firebase/remove-token', [FirebaseController::class, 'removeToken'])->name('firebase.removeToken');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // ── DRIVER: Browse & Apply ──
    Route::middleware('role:driver')->prefix('driver')->group(function () {
        Route::get('/marketplace', [DriverMarketController::class, 'browse'])->name('driver.marketplace');
        Route::get('/marketplace/{motorcycle}', [DriverMarketController::class, 'viewVehicle'])->name('driver.marketplace.show');
        Route::get('/marketplace/{motorcycle}/apply', [DriverMarketController::class, 'showApplyForm'])->name('marketplace.apply');
        Route::post('/marketplace/{motorcycle}/apply', [DriverMarketController::class, 'submitApplication'])->name('marketplace.apply.submit');
        Route::get('/applications', [DriverMarketController::class, 'apps'])->name('driver.apps');
        Route::get('/profile', [DriverMarketController::class, 'profile'])->name('driver.profile');

        Route::get('/gps', [LocationController::class, 'driverGps'])->name('driver.gps');
        Route::post('/gps', [LocationController::class, 'driverGpsUpdate'])->name('driver.gps.update');

        Route::get('/contracts', [ContractController::class, 'driverContracts'])->name('driver.contracts');
    });

    // ── OWNER: Vehicle Management ──
    Route::middleware('role:owner')->prefix('owner')->group(function () {
        Route::get('/vehicles', [MotorcycleController::class, 'ownerIndex'])->name('owner.vehicles');
        Route::get('/vehicles/create', [MotorcycleController::class, 'ownerCreate'])->name('owner.vehicles.create');
        Route::post('/vehicles', [MotorcycleController::class, 'ownerStore'])->name('owner.vehicles.store');
        Route::get('/vehicles/{motorcycle}', [MotorcycleController::class, 'ownerShow'])->name('owner.vehicles.show');
        Route::get('/vehicles/{motorcycle}/edit', [MotorcycleController::class, 'ownerEdit'])->name('owner.vehicles.edit');
        Route::put('/vehicles/{motorcycle}', [MotorcycleController::class, 'ownerUpdate'])->name('owner.vehicles.update');
        Route::delete('/vehicles/{motorcycle}', [MotorcycleController::class, 'ownerDestroy'])->name('owner.vehicles.destroy');
        Route::get('/drivers-map', [LocationController::class, 'ownerMap'])->name('owner.map');
        Route::get('/vehicles/{motorcycle}/track', [LocationController::class, 'ownerTrackVehicle'])->name('owner.vehicles.track');
        Route::get('/vehicles/{motorcycle}/route', [LocationController::class, 'apiVehicleRoute'])->name('owner.vehicles.route');
        Route::post('/vehicles/{motorcycle}/applications/{application}/accept', [MotorcycleController::class, 'ownerAcceptApplication'])->name('owner.vehicles.accept');
        Route::post('/vehicles/{motorcycle}/applications/{application}/reject', [MotorcycleController::class, 'ownerRejectApplication'])->name('owner.vehicles.reject');

        Route::get('/contracts', [ContractController::class, 'ownerContracts'])->name('owner.contracts');
        Route::post('/contracts/{loan}/approve', [ContractController::class, 'ownerApproveContract'])->name('owner.contracts.approve');
        Route::post('/contracts/{loan}/reject', [ContractController::class, 'ownerRejectContract'])->name('owner.contracts.reject');
    });

    // ── ADMIN ──
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('admin.users.approve');
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{user}/toggle-active', [AdminController::class, 'toggleActive'])->name('admin.users.toggleActive');
        Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.resetPassword');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

        Route::get('/users/{user}/verify', [AdminController::class, 'reviewVerification'])->name('admin.users.verify');
        Route::post('/users/{user}/verify/approve', [AdminController::class, 'approveVerification'])->name('admin.users.verify.approve');
        Route::post('/users/{user}/verify/reject', [AdminController::class, 'rejectVerification'])->name('admin.users.verify.reject');

        Route::get('/vehicles', [AdminController::class, 'vehicles'])->name('admin.vehicles');
        Route::get('/vehicles/{motorcycle}', [AdminController::class, 'vehicleReview'])->name('admin.vehicles.review');
        Route::post('/vehicles/{motorcycle}/verify', [AdminController::class, 'verifyVehicle'])->name('admin.vehicles.verify');
        Route::post('/vehicles/{motorcycle}/reject', [AdminController::class, 'rejectVehicle'])->name('admin.vehicles.reject');

        Route::get('/drivers', [AdminController::class, 'pendingDrivers'])->name('admin.drivers');
        Route::post('/drivers/{user}/approve', [AdminController::class, 'approveDriver'])->name('admin.drivers.approve');
        Route::post('/drivers/{user}/reject', [AdminController::class, 'rejectDriver'])->name('admin.drivers.reject');

        Route::get('/owners', [AdminController::class, 'owners'])->name('admin.owners');
        Route::post('/owners/{user}/approve', [AdminController::class, 'approveOwner'])->name('admin.owners.approve');
        Route::post('/owners/{user}/reject', [AdminController::class, 'rejectOwner'])->name('admin.owners.reject');

        Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications');
        Route::post('/applications/{application}/review', [AdminController::class, 'reviewApplication'])->name('admin.applications.review');

        Route::get('/relationships', [AdminController::class, 'relationships'])->name('admin.relationships');

        Route::get('/payments', [PaymentController::class, 'adminIndex'])->name('admin.payments');

        Route::get('/overdue', [AdminController::class, 'overdueLoans'])->name('admin.overdue');
        Route::get('/drivers-map', [LocationController::class, 'adminMap'])->name('admin.map');
        Route::get('/vehicles/{motorcycle}/track', [LocationController::class, 'apiVehicleRoute'])->name('admin.vehicles.track');

        Route::get('/loans-progress', [AdminController::class, 'loansProgress'])->name('admin.loans.progress');
        Route::post('/loans/{loan}/force-stop', [AdminController::class, 'forceStopLoan'])->name('admin.loans.forceStop');
    });

    // ── SHARED: Loans & Payments ──
    Route::middleware('verified')->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{loan}/accept', [LoanController::class, 'acceptAgreement'])->name('loans.accept');
        Route::post('/loans/{loan}/complete', [LoanController::class, 'completeLoan'])->name('loans.complete');
        Route::get('/loans/{loan}/certificate', [LoanController::class, 'ownershipCertificate'])->name('loans.certificate');

        Route::get('/loans/{loan}/contract', [ContractController::class, 'show'])->name('contracts.show');
        Route::get('/loans/{loan}/contract/print', [ContractController::class, 'printContract'])->name('contracts.print');
        Route::get('/loans/{loan}/contract/download', [ContractController::class, 'downloadPdf'])->name('contracts.download');
        Route::get('/loans/{loan}/contract/upload', [ContractController::class, 'showUploadForm'])->name('contracts.upload.form');
        Route::post('/loans/{loan}/contract/upload', [ContractController::class, 'uploadSigned'])->name('contracts.upload');
        Route::post('/loans/{loan}/report-absconded', [LoanController::class, 'reportAbsconded'])->name('loans.reportAbsconded');
        Route::post('/loans/{loan}/recover', [LoanController::class, 'recoverVehicle'])->name('loans.recover');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        Route::get('/loans/{loan}/track', [LocationController::class, 'track'])->name('locations.track');
        Route::get('/loans/{loan}/location/latest', [LocationController::class, 'apiLatest'])->name('locations.api.latest');

        // GPS Fleet API endpoints
        Route::get('/api/fleet/positions', [LocationController::class, 'apiVehiclePositions'])->name('api.fleet.positions');
        Route::get('/api/fleet/stats', [LocationController::class, 'apiVehicleStats'])->name('api.fleet.stats');

        // Chat (loan-based group + direct messaging)
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/conversation/{conversationId}', [ChatController::class, 'showConversation'])->name('chat.show.conversation');
        Route::post('/chat/conversation/{conversationId}', [ChatController::class, 'sendToConversation'])->name('chat.send.conversation');
        Route::get('/chat/conversation/{conversationId}/fetch', [ChatController::class, 'fetchConversation'])->name('chat.fetch.conversation');
        Route::get('/chat/start/{userId}', [ChatController::class, 'startDirect'])->name('chat.start.direct');

        Route::middleware('role:admin,owner,verified')->group(function () {
            Route::resource('motorcycles', MotorcycleController::class)->except(['create', 'store']);
            Route::post('motorcycles/{motorcycle}/assign', [MotorcycleController::class, 'assign'])->name('motorcycles.assign');
            Route::resource('drivers', DriverController::class)->except('show')->parameters(['drivers' => 'driver']);
            Route::get('drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show');
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        });
    });
});

Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('motorcycles', MotorcycleApiController::class)->names('api.motorcycles');
    Route::apiResource('loans', LoanApiController::class)->only(['index', 'store', 'show'])->names('api.loans');
    Route::apiResource('payments', PaymentApiController::class)->only(['index', 'store'])->names('api.payments');
    Route::apiResource('drivers', DriverApiController::class)->only(['index', 'show'])->names('api.drivers');
    Route::post('/location', [LocationController::class, 'update'])->name('api.location.update');
});
