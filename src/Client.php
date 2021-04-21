<?php

namespace Meema\Flysystem;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * Construct Meema client.
     *
     * @param string $accessKey
     */
    public function __construct($accessKey, $config = [])
    {
        $this->accessKey = $accessKey;

        $this->config = $config;

        $this->client = new GuzzleClient([
            'base_uri' => $url ?? 'http://meema-api.test/api/',
        ]);
    }

    /**
     * Handle the API request.
     *
     * @param string $method
     * @param string $path
     */
    public function request($method, $path, $data = null)
    {
        $content = $this->client->request($method, $path, [
            'query'   => $data,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => "Bearer {$this->accessKey}",
            ],
        ])
        ->getBody()
        ->getContents();

        $body = json_decode($content, true);

        if ($body && array_key_exists('data', $body)) {
            return $body['data'];
        }

        return $body;
    }
}
