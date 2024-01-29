<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AbstractWeatherMap
{
    protected ParameterBagInterface $config;
    protected HttpClientInterface $httpClient;

    public function __construct(ParameterBagInterface $config, HttpClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    protected function currentWeather(array $query, string $url): array
    {
        $response = $this->httpClient->request('GET', $url, [
            'query' => $query
        ]);

        return $response->toArray();
    }
}