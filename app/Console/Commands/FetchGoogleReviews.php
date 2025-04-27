<?php

namespace App\Console\Commands;

use App\Models\GoogleReview;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchGoogleReviews extends Command
{
    protected $signature = 'app:fetch-google-reviews';
    protected $description = 'Fetch Google Reviews using Places API and store them in the database.';

    public function handle()
    {
        $apiKey = config('services.google.places_api_key');
        $placeId = config('services.google.places_id');

        if (!$apiKey || !$placeId) {
            $this->error('Google Places API Key or Place ID is not configured in config/services.php or .env file.');
            Log::error('Google Places API Key or Place ID is not configured.');
            return 1; // Error code
        }

        // Documentación de la API de Places (Place Details): https://developers.google.com/maps/documentation/places/web-service/details
        $url = "https://maps.googleapis.com/maps/api/place/details/json";

        try {
            $response = Http::timeout(30)->get($url, [
                'place_id' => $placeId,
                'key' => $apiKey,
                'fields' => 'reviews,rating,user_ratings_total', // Campos que queremos obtener
                'reviews_sort' => 'newest', // Ordenar por más recientes
                'language' => 'es' // Obtener reseñas en español si es posible
            ]);

            if (!$response->successful()) {
                $this->error('Failed to fetch data from Google Places API. Status: ' . $response->status());
                Log::error('Google Places API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                return 1;
            }

            $data = $response->json();

            // Verifica si la respuesta fue exitosa pero no contiene reseñas
            if ($data['status'] === 'OK' && (!isset($data['result']['reviews']) || !is_array($data['result']['reviews']))) {
                 $this->info('No reviews found for the specified Place ID or the business has no reviews yet.');
                 Log::info('No reviews found for Place ID or business has no reviews.', ['place_id' => $placeId]);
                 return 0; // No es un error, simplemente no hay reseñas
            }

            // Maneja otros estados de error de la API
            if ($data['status'] !== 'OK') {
                $this->error('Google Places API returned an error status: ' . $data['status'] . (isset($data['error_message']) ? ' - ' . $data['error_message'] : ''));
                Log::error('Google Places API error.', ['status' => $data['status'] ?? 'Unknown', 'error_message' => $data['error_message'] ?? 'N/A', 'response_body' => $data]);
                return 1; // Error de la API
            }


            $reviewsData = $data['result']['reviews'];
            $savedCount = 0;
            $skippedCount = 0;
            $errorCount = 0; // Contador para errores al guardar

            $this->info('Fetched ' . count($reviewsData) . ' reviews. Processing...');

            foreach ($reviewsData as $review) {
                // Validación básica de datos esenciales
                if (empty($review['author_name']) || !isset($review['rating']) || empty($review['time'])) {
                     Log::warning('Skipping review due to missing essential data.', ['review_preview' => substr($review['text'] ?? '', 0, 50)]);
                     $skippedCount++;
                     continue;
                }

                // Google a menudo no devuelve un ID único y estable para la reseña en sí.
                // Usaremos una combinación de autor y tiempo como clave única aproximada si es necesario,
                // pero es mejor confiar en updateOrCreate con atributos que probablemente identifiquen la reseña.
                // El 'review_id' no está garantizado por la API de Places Details.
                // Usaremos author_name y time como identificadores principales para updateOrCreate.

                // ... (dentro del bucle foreach) ...

try {
    // Intenta convertir el timestamp a objeto Carbon
    $reviewTime = \Carbon\Carbon::createFromTimestamp($review['time']);

    // --- INICIO: Lógica Manual Reemplazando updateOrCreate ---

    // 1. Buscar si ya existe
    $existingReview = GoogleReview::where('author_name', $review['author_name'])
                                  ->where('review_time', $reviewTime)
                                  ->first();

    if ($existingReview) {
        // La reseña ya existe. Opcional: podríamos actualizarla aquí si quisiéramos.
        // Por ahora, no hacemos nada o simplemente la contamos como 'procesada' (no error, no nueva).
        // Consideraremos que no se guarda nada nuevo. Podríamos incrementar skippedCount si preferimos.
        // Log::info('Review already exists, skipping update.', ['author' => $review['author_name'], 'time' => $reviewTime]);

    } else {
        // 2. La reseña NO existe, la creamos.
        GoogleReview::create([
            // Asegúrate de incluir TODOS los campos necesarios y fillable
            'author_name' => $review['author_name'], // <-- Incluido explícitamente
            'review_time' => $reviewTime,           // <-- Incluido explícitamente
            'language' => $review['language'] ?? 'en',
            'profile_photo_url' => $review['profile_photo_url'] ?? null,
            'rating' => $review['rating'],
            'relative_time_description' => $review['relative_time_description'] ?? null,
            'text' => $review['text'] ?? null,
            'translated' => $review['translated'] ?? false,
            'author_url' => $review['author_url'] ?? null,
            'is_visible' => true, // Asumiendo que por defecto es visible
        ]);
        $savedCount++; // Incrementa el contador SOLO si se crea una nueva
    }

    // --- FIN: Lógica Manual ---

// Corrección: Mueve $savedCount++ aquí si la lógica de arriba lo requiere
// o elimínalo si ya lo manejas dentro del if/else.
// En este caso, ya está dentro del 'else'.

} catch (\Illuminate\Database\QueryException $e) { // Captura específicamente errores de DB
     Log::error('Database error saving Google Review.', [
        'error_code' => $e->getCode(),
        'error' => $e->getMessage(),
        'author' => $review['author_name'] ?? 'N/A',
        'time' => $review['time'] ?? 'N/A',
        'sql' => $e->getSql() ?? 'N/A',
        'bindings' => $e->getBindings() ?? []
    ]);
    $this->error('DB Error saving review from ' . ($review['author_name'] ?? 'N/A') . ': ' . $e->getMessage());
    $errorCount++; // Incrementa contador de errores de DB

} catch (\Exception $e) { // Captura otras excepciones generales
    Log::error('General error processing Google Review.', [
        'error' => $e->getMessage(),
        'author' => $review['author_name'] ?? 'N/A',
        'time' => $review['time'] ?? 'N/A'
    ]);
    $this->error('Error processing review from ' . ($review['author_name'] ?? 'N/A') . ': ' . $e->getMessage());
    $errorCount++; // Incrementa contador de errores generales
}


// ... (resto del bucle foreach y comando) ...

            }

            // Opcional: Actualizar rating general y total en tabla 'settings' o similar si es necesario
            if (isset($data['result']['rating']) && isset($data['result']['user_ratings_total'])) {
                 // Guardar $data['result']['rating'] y $data['result']['user_ratings_total']
                 // en algún lugar si quieres mostrar el promedio general.
                 Log::info('Overall Google Rating:', [
                     'rating' => $data['result']['rating'],
                     'total_ratings' => $data['result']['user_ratings_total']
                 ]);
                 // Ejemplo: Setting::updateOrCreate(['key' => 'google_overall_rating'], ['value' => $data['result']['rating']]);
                 // Setting::updateOrCreate(['key' => 'google_total_ratings'], ['value' => $data['result']['user_ratings_total']]);
            }


            $this->info("Successfully processed reviews. Saved/Updated: {$savedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}.");
            Log::info("FetchGoogleReviews completed. Saved/Updated: {$savedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}.");

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error('Connection Error: Could not connect to Google Places API. ' . $e->getMessage());
            Log::error('Google Places API connection error.', ['error' => $e->getMessage()]);
            return 1;
        } catch (\Exception $e) {
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            Log::error('Unexpected error in FetchGoogleReviews command.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }

        // Devuelve 0 si todo fue bien, incluso si hubo errores al guardar reseñas individuales
        // Devuelve 1 solo si falla la conexión o la configuración inicial
        return 0; // Success overall execution
    }
}
