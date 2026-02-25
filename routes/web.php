<?php

use App\Http\Controllers\Client\ClientAuthController;
use App\Http\Controllers\Client\ClientPanelController;
use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/frota', [PublicPageController::class, 'vehicles'])->name('public.vehicles');
Route::get('/frota/{id}', [PublicPageController::class, 'vehicleDetails'])->name('public.vehicles.show');

// ==========================================
// RESERVA / CHECKOUT ROUTES
// ==========================================
Route::get('/reserva/opcionais', [App\Http\Controllers\CheckoutController::class, 'extras'])->name('checkout.extras');
Route::post('/reserva/processar-opcionais', [App\Http\Controllers\CheckoutController::class, 'processExtras'])->name('checkout.process_extras');
Route::get('/reserva/identificacao', [App\Http\Controllers\CheckoutController::class, 'identify'])->name('checkout.identify');
Route::post('/reserva/login', [App\Http\Controllers\CheckoutController::class, 'login'])->name('checkout.login');
Route::post('/reserva/cadastro', [App\Http\Controllers\CheckoutController::class, 'register'])->name('checkout.register');
Route::get('/reserva/concluir', [App\Http\Controllers\CheckoutController::class, 'confirm'])->name('checkout.confirm');
Route::post('/reserva/finalizar', [App\Http\Controllers\CheckoutController::class, 'finish'])->name('checkout.finish');

Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle']);

Route::get('/export/cashflow', [App\Http\Controllers\CashFlowExportController::class, 'exportCsv'])
    ->name('export.cashflow')
    ->middleware(['web']);

// ==========================================
// ADMIN: SALVAR CONFIGURAÇÕES DO SISTEMA
// ==========================================
Route::post('/admin/settings/save', [App\Http\Controllers\SystemSettingsController::class, 'save'])
    ->name('admin.settings.save')
    ->middleware(['web']);

// ==========================================
// ADMIN: AÇÕES DO CONTRATO
// ==========================================
Route::post('/admin/contract/{id}/checkout', [App\Http\Controllers\ContractActionController::class, 'checkout'])
    ->name('admin.contract.checkout')->middleware(['web']);
Route::post('/admin/contract/{id}/checkin', [App\Http\Controllers\ContractActionController::class, 'checkin'])
    ->name('admin.contract.checkin')->middleware(['web']);
Route::post('/admin/contract/{id}/generate-pdf', [App\Http\Controllers\ContractActionController::class, 'generatePdf'])
    ->name('admin.contract.generatePdf')->middleware(['web']);
Route::post('/admin/contract/{id}/generate-invoices', [App\Http\Controllers\ContractActionController::class, 'generateInvoices'])
    ->name('admin.contract.generateInvoices')->middleware(['web']);

// ==========================================
// ASSINATURA DIGITAL (PÚBLICA)
// ==========================================
Route::get('/contrato/{id}/assinar', [App\Http\Controllers\SignatureController::class, 'show'])->name('contract.signature.show');
Route::post('/contrato/{id}/assinar', [App\Http\Controllers\SignatureController::class, 'sign'])->name('contract.signature.sign');

// ==========================================
// ASSINATURA DIGITAL OS (PÚBLICA)
// ==========================================
Route::get('/os/{id}/assinatura', [App\Http\Controllers\ServiceOrderSignatureController::class, 'show'])->name('os.signature.show');
Route::post('/os/{id}/autorizar', [App\Http\Controllers\ServiceOrderSignatureController::class, 'signAuthorization'])->name('os.signature.authorize');
Route::post('/os/{id}/aprovar', [App\Http\Controllers\ServiceOrderSignatureController::class, 'signCompletion'])->name('os.signature.approve');
Route::get('/os/{id}/pdf', [App\Http\Controllers\ServiceOrderSignatureController::class, 'downloadPdf'])->name('os.signature.pdf');

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
        Route::get('/ordens-de-servico', [ClientPanelController::class, 'serviceOrders'])->name('service-orders');
    });
});

Route::middleware(['web', 'auth'])->group(function () {
    // Exportar ficha do cliente em PDF
    Route::get('/admin/customer/{id}/pdf', \App\Http\Controllers\CustomerPdfController::class)
        ->name('admin.customer.pdf');
});
