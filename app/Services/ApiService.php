<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

abstract class ApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('app.api_base_url', 'https://api.example.com');
    }

    /**
     * Make a GET request to the API.
     *
     * @param string $endpoint
     * @param array $params
     * @return array|null
     */
    protected function get(string $endpoint, array $params = [])
    {
        $url = $this->baseUrl . $endpoint;
        
        $response = Http::get($url, $params);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
