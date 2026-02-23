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

    Route::get('/admin/api/calendar-timeline-events', function() {
        // 1. Fetch Groups (Vehicles)
        $vehicles = \App\Models\Vehicle::with('category')->get();
        $groups = [];
        foreach ($vehicles as $vehicle) {
            $groups[] = [
                'id' => $vehicle->id,
                'content' => sprintf('<b>%s %s</b><br><span style="font-size:0.8em; color:gray">%s - %s</span>', 
                                     $vehicle->make, 
                                     $vehicle->model,
                                     $vehicle->plate,
                                     $vehicle->category?->name ?? 'Sem Categoria')
            ];
        }

        // 2. Fetch Items (Events: Reservations & Maintenance)
        $items = [];
        
        // --- Reservations ---
        $reservations = \App\Models\Reservation::with('customer')->get();
        foreach ($reservations as $res) {
            $bgColor = match($res->status->value) {
                'pendente' => '#3b82f6', // blue-500
                'confirmada', 'caucao_retida' => '#eab308', // yellow-500
                'andamento' => '#22c55e', // green-500
                'concluida' => '#6b7280', // gray-500
                'cancelada' => '#ef4444', // red-500
                default => '#94a3b8'
            };

            $items[] = [
                'id' => 'res_' . $res->id,
                'group' => $res->vehicle_id,
                'content' => $res->customer?->name ?? 'Cliente N/A',
                'start' => $res->pickup_date->toIso8601String(),
                'end' => $res->return_date->toIso8601String(),
                'style' => 'background-color: ' . $bgColor . '; color: white; border-color: ' . $bgColor . ';',
                'title' => "Reserva #{$res->id}\nStatus: {$res->status->name}\nCliente: {$res->customer?->name}"
            ];
        }

        // --- Maintenance / Service Orders ---
        $serviceOrders = \App\Models\ServiceOrder::whereNotIn('status', ['concluido', 'cancelado'])->get();
        foreach ($serviceOrders as $so) {
            $items[] = [
                'id' => 'so_' . $so->id,
                'group' => $so->vehicle_id,
                'content' => '🔧 Manutenção',
                'start' => $so->entry_date->toIso8601String(),
                'end' => clone $so->entry_date->addDays(3)->toIso8601String(), // Mock end date if missing
                'style' => 'background-color: #ef4444; color: white; border-color: #dc2626;', // red-500
                'title' => "OS #{$so->id} - {$so->service_type}"
            ];
        }

        return response()->json([
            'groups' => $groups,
            'items' => $items
        ]);
    })->name('moonshine.calendar.timeline');
});
