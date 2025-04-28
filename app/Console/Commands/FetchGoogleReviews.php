<?php // Archivo: app/Console/Commands/FetchGoogleReviews.php

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
            return 1;
        }

        $url = "https://maps.googleapis.com/maps/api/place/details/json";

        try {
            $response = Http::timeout(130)->get($url, [
                'place_id' => $placeId,
                'key' => $apiKey,
                'fields' => 'reviews,rating,user_ratings_total',
                'reviews_sort' => 'newest',
                'language' => 'es'
            ]);

            if (!$response->successful()) {
                $this->error('Failed to fetch data from Google Places API. Status: ' . $response->status());
                Log::error('Google Places API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                return 1;
            }

            $data = $response->json();

            if ($data['status'] === 'OK' && (!isset($data['result']['reviews']) || !is_array($data['result']['reviews']))) {
                 $this->info('No reviews found for the specified Place ID or the business has no reviews yet.');
                 Log::info('No reviews found for Place ID or business has no reviews.', ['place_id' => $placeId]);
                 return 0;
            }

            if ($data['status'] !== 'OK') {
                $this->error('Google Places API returned an error status: ' . $data['status'] . (isset($data['error_message']) ? ' - ' . $data['error_message'] : ''));
                Log::error('Google Places API error.', ['status' => $data['status'] ?? 'Unknown', 'error_message' => $data['error_message'] ?? 'N/A', 'response_body' => $data]);
                return 1;
            }

            $reviewsData = $data['result']['reviews'];
            $savedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            $this->info('Fetched ' . count($reviewsData) . ' reviews. Processing...');

            foreach ($reviewsData as $review) {
                if (empty($review['author_name']) || !isset($review['rating']) || empty($review['time'])) {
                     Log::warning('Skipping review due to missing essential data.', ['review_preview' => substr($review['text'] ?? '', 0, 50)]);
                     $skippedCount++;
                     continue;
                }

                try {
                    $reviewTime = \Carbon\Carbon::createFromTimestamp($review['time']);

                    $googleReview = GoogleReview::updateOrCreate(
                        [ // Atributos para buscar
                            'author_name' => $review['author_name'],
                            'review_time' => $reviewTime,
                        ],
                        [ // Valores para insertar/actualizar
                            'author_name' => $review['author_name'], // <-- Incluido aquí
                            'language' => $review['language'] ?? 'en',
                            'profile_photo_url' => $review['profile_photo_url'] ?? null,
                            'rating' => $review['rating'],
                            'relative_time_description' => $review['relative_time_description'] ?? null,
                            'text' => $review['text'] ?? null,
                            'translated' => $review['translated'] ?? false,
                            'author_url' => $review['author_url'] ?? null,
                            // is_visible usa el default del modelo/migración
                        ]
                    );
                    $savedCount++; // Incrementar si updateOrCreate tiene éxito

                } catch (\Illuminate\Database\QueryException $e) {
                     Log::error('Database error saving Google Review.', [
                        'error_code' => $e->getCode(), 'error' => $e->getMessage(),
                        'author' => $review['author_name'] ?? 'N/A', 'time' => $review['time'] ?? 'N/A',
                        'sql' => $e->getSql() ?? 'N/A', 'bindings' => $e->getBindings() ?? [] ]);
                    $this->error('DB Error saving review from ' . ($review['author_name'] ?? 'N/A') . ': ' . $e->getMessage());
                    $errorCount++;
                } catch (\Exception $e) {
                    Log::error('General error processing Google Review.', [
                        'error' => $e->getMessage(), 'author' => $review['author_name'] ?? 'N/A', 'time' => $review['time'] ?? 'N/A' ]);
                    $this->error('Error processing review from ' . ($review['author_name'] ?? 'N/A') . ': ' . $e->getMessage());
                    $errorCount++;
                }
            }

            if (isset($data['result']['rating']) && isset($data['result']['user_ratings_total'])) {
                 Log::info('Overall Google Rating:', [
                     'rating' => $data['result']['rating'], 'total_ratings' => $data['result']['user_ratings_total'] ]);
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
        return 0;
    }
    // En app/Console/Kernel.php


}
