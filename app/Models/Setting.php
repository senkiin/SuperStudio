<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class Setting extends Model
{
    // No necesitamos HasFactory aquí
    public $timestamps = false; // Opcional

    protected $fillable = ['key', 'value'];

    // Helper para guardar tokens encriptados
    public static function setGoogleTokens(array $tokenData): void
    {
        // Mantener el refresh token anterior si no viene uno nuevo
        $currentTokenData = self::getGoogleTokens();
        $refreshToken = $tokenData['refresh_token'] ?? $currentTokenData['refresh_token'] ?? null;

        $jsonData = json_encode([
            'access_token' => $tokenData['access_token'] ?? null,
            'refresh_token' => $refreshToken,
            'expires_at' => $tokenData['expires_at'] ?? null, // Guardar como timestamp
        ]);

        Setting::updateOrCreate(
            ['key' => 'google_api_tokens'],
            // Encripta siempre antes de guardar
            ['value' => Crypt::encryptString($jsonData)]
        );
    }

    // Helper para obtener tokens desencriptados
    public static function getGoogleTokens(): ?array
    {
        $setting = Setting::where('key', 'google_api_tokens')->first();
        if (!$setting || !$setting->value) {
            return null;
        }
        try {
            // Desencripta al leer
            $decryptedJson = Crypt::decryptString($setting->value);
            return json_decode($decryptedJson, true);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Failed to decrypt Google API tokens: ' . $e->getMessage());
            return null;
        }
    }

    // Helper para guardar otros settings (accountId, locationId)
    public static function setSetting(string $key, ?string $value): void
    {
        if ($value === null) {
             Setting::where('key', $key)->delete();
        } else {
             Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    // Helper para obtener otros settings
    public static function getSetting(string $key): ?string
    {
         return Setting::where('key', $key)->value('value');
    }
}
