<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;


trait ApiResponses
{
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $isJsonRequest = false) {
        $client = new Client([
            'base_uri' => $this->baseUri
        ]);

        if(method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $formParams,
            'headers' => $headers,
            'query' => $queryParams
        ]);

        $response = $response->getBody()->getContents();

        return $response;
    }
}
