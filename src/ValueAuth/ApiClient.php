<?php


namespace ValueAuth;


use GuzzleHttp\Client;

class ApiClient
{
    /**
     * @var Client
     */
    protected $client;

    function __construct(string $baseUri, ?string $accessToken = null, ?string $role = null)
    {
        $this->client = new Client([
            'base_uri' => $baseUri
        ]);
    }
}