<?php

namespace App\Http\Controllers;

use App\Models\Setting; // Importar Setting
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt; // Para desencriptar/encriptar

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        // La ruta ya está protegida por el middleware admin
        try {
            $client = new Client();
            // ... (configuración del cliente igual que antes: ID, Secret, Redirect, Scopes, AccessType, State) ...
             $client->setClientId(config('services.google_business.client_id'));
             $client->setClientSecret(config('services.google_business.client_secret'));
             $client->setRedirectUri(config('services.google_business.redirect'));
             $client->setScopes(['https://www.googleapis.com/auth/business.manage']);
             $client->setAccessType('offline');
             $client->setApprovalPrompt('force');

             $state = sha1(time() . uniqid());
             session(['google_oauth_state' => $state]);
             $client->setState($state);

             $authUrl = $client->createAuthUrl();
             return Redirect::away($authUrl);

        } catch (\Exception $e) {
            Log::error('Google Auth Redirect Error: ' . $e->getMessage());
            return redirect('/admin/dashboard')->with('error', 'Error al redirigir a Google.'); // Ajusta ruta
        }
    }

    public function handleGoogleCallback(Request $request)
    {
         // La ruta ya está protegida por el middleware admin

         // ... (verificación del state igual que antes) ...
         $sessionState = session()->pull('google_oauth_state');
         if (!$request->has('state') || !$sessionState || $request->input('state') !== $sessionState) {
              return redirect('/admin/dashboard')->with('error', 'Estado de OAuth inválido.');
         }


        if (!$request->has('code')) {
            return redirect('/admin/dashboard')->with('error', 'Autorización de Google cancelada o fallida.');
        }

        $client = new Client();
        $client->setClientId(config('services.google_business.client_id'));
        $client->setClientSecret(config('services.google_business.client_secret'));
        $client->setRedirectUri(config('services.google_business.redirect'));

        try {
            $tokenData = $client->fetchAccessTokenWithAuthCode($request->input('code'));

            if (isset($tokenData['error'])) {
                 Log::error('Google Auth Callback Error:', $tokenData);
                 return redirect('/admin/dashboard')->with('error', 'Error: ' . $tokenData['error_description'] ?? 'Desconocido');
            }

            // *** GUARDAR TOKENS GLOBALMENTE USANDO EL MODELO Setting ***
            $currentTokens = Setting::getGoogleTokens(); // Obtener tokens actuales (para mantener el refresh si no viene uno nuevo)

            Setting::setGoogleTokens([
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $currentTokens['refresh_token'] ?? null, // Mantener el viejo si no hay nuevo
                'expires_at' => now()->addSeconds($tokenData['expires_in'] - 60)->timestamp,
            ]);

            // *** PENDIENTE: Guardar accountId y locationId ***
            // Después de guardar los tokens, deberías hacer una llamada API aquí
            // para obtener la lista de cuentas/ubicaciones y guardarlas también
            // en la tabla 'settings' o en config/.env.
            // Setting::updateOrCreate(['key' => 'google_account_id'], ['value' => 'accounts/ACCOUNT_ID_OBTENIDO']);
            // Setting::updateOrCreate(['key' => 'google_location_id'], ['value' => 'locations/LOCATION_ID_OBTENIDO']);
            Log::info('Google API Tokens stored globally.');


            return redirect('/admin/dashboard')->with('success', 'Conexión con Google Business establecida globalmente.');

        } catch (\Exception $e) {
            Log::error('Google Auth Callback Exception: ' . $e->getMessage());
            return redirect('/admin/dashboard')->with('error', 'Excepción al procesar la respuesta de Google.');
        }
    }
}
