<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfCoService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.pdf.co/v1';

    public function __construct()
    {
        $this->apiKey = env('PDF_CO_API_KEY'); // set in .env
    }

    /**
     * Call /pdf/edit/add with the provided payload (mirrors the cURL sample).
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function editAdd(array $payload): array
    {
        $url = $this->baseUrl . '/pdf/edit/add';

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('PDF.co /pdf/edit/add failed: ' . $response->body());
            throw new \Exception('PDF.co API request failed: ' . $response->body());
        }

        return $response->json();
    }

}
