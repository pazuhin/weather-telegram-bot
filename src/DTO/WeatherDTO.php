<?php

namespace App\DTO;

class WeatherDTO
{
    public string $feelsLike;
    public float $tempMin;
    public float $tempMax;
    public float $windSpeed;

    public function __construct(array $response)
    {
        $this->feelsLike = $this->convertToCelsius($response['main']['feels_like']);
        $this->tempMin = $this->convertToCelsius($response['main']['temp_min']);
        $this->tempMax = $this->convertToCelsius($response['main']['temp_max']);
        $this->windSpeed = $response['wind']['speed'];
    }

    private function convertToCelsius(float $kelvin): float
    {
        return round($kelvin - 273.15);
    }
}