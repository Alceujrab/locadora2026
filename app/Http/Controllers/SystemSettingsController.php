<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Mapa de chaves aceitas e seus respectivos grupos.
     */
    private array $settingsMap = [
        // Tema
        'PANEL_COLOR_PRIMARY' => 'theme',
        'PANEL_COLOR_SECONDARY' => 'theme',
        'PANEL_COLOR_ACCENT' => 'theme',
        'PANEL_DARK_MODE' => 'theme',
        'PANEL_LOGO_TEXT' => 'theme',

        // Social Login
        'GOOGLE_CLIENT_ID' => 'socialite',
        'GOOGLE_CLIENT_SECRET' => 'socialite',
        'GOOGLE_REDIRECT_URI' => 'socialite',
        'FACEBOOK_CLIENT_ID' => 'socialite',
        'FACEBOOK_CLIENT_SECRET' => 'socialite',

        // WhatsApp
        'EVOLUTION_API_URL' => 'whatsapp',
        'EVOLUTION_API_KEY' => 'whatsapp',
        'EVOLUTION_INSTANCE_NAME' => 'whatsapp',
        'WHATSAPP_NUMBER' => 'whatsapp',

        // Pagamento
        'PAYMENT_GATEWAY' => 'payment',
        'PAYMENT_API_KEY' => 'payment',
        'PAYMENT_SECRET_KEY' => 'payment',
        'PAYMENT_WEBHOOK_SECRET' => 'payment',

        // Empresa
        'COMPANY_NAME' => 'general',
        'COMPANY_DOCUMENT' => 'general',
        'COMPANY_PHONE' => 'general',
        'COMPANY_EMAIL' => 'general',
        'TIMEZONE' => 'general',
    ];

    public function save(Request $request)
    {
        try {
            foreach ($this->settingsMap as $key => $group) {
                $value = $request->input($key);

                // Só salva campos que foram enviados (ignora null)
                if ($value !== null) {
                    Setting::set($key, $value, $group);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Configurações salvas com sucesso!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
