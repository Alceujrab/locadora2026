<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Client\ClientAuthController;
use App\Http\Controllers\Client\ClientPanelController;

Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/frota', [PublicPageController::class, 'vehicles'])->name('public.vehicles');
Route::get('/frota/{id}', [PublicPageController::class, 'vehicleDetails'])->name('public.vehicles.show');

Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle']);

Route::get('/export/cashflow', [App\Http\Controllers\CashFlowExportController::class, 'exportCsv'])
    ->name('export.cashflow')
    ->middleware(['web']);

// ==========================================
// CLIENT PORTAL ROUTES
// ==========================================
Route::prefix('cliente')->name('cliente.')->group(function () {
    
    // Auth Routes
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login']);
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');

    // Protected Portal Routes
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/dashboard', [ClientPanelController::class, 'dashboard'])->name('dashboard');
        Route::get('/faturas', [ClientPanelController::class, 'invoices'])->name('invoices');
        Route::get('/contratos', [ClientPanelController::class, 'contracts'])->name('contracts');
        Route::get('/reservas', [ClientPanelController::class, 'reservations'])->name('reservations');
        Route::get('/suporte', [ClientPanelController::class, 'support'])->name('support');
    });
});

Route::middleware(\MoonShine\Laravel\Http\Middleware\Authenticate::class)->group(function () {
    Route::get('/admin/api/calendar-events', function() {
        $reservations = \App\Models\Reservation::with(['vehicle', 'customer'])->get();
        $events = [];

        foreach ($reservations as $res) {
            $events[] = [
                'id' => $res->id,
                'title' => ($res->vehicle?->plate ?? 'N/A') . ' - ' . ($res->customer?->name ?? 'N/A'),
                'start' => $res->pickup_date->toIso8601String(),
                'end' => $res->return_date->toIso8601String(),
                'color' => 'var(--' . $res->status->color() . ')',
                'url' => '/admin/resource/reservation-resource/' . $res->id,
            ];
        }

        return response()->json($events);
    })->name('moonshine.calendar.events');
});
