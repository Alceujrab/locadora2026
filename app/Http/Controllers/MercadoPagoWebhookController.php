<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request, MercadoPagoService $mpService)
    {
        Log::info('MercadoPago Webhook Recebido', $request->all());

        try {
            $mpService->handleWebhook($request->all());
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Erro MercadoPago Webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}
