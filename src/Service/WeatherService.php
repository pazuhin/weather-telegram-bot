<?php

namespace App\Service;

use App\DTO\WeatherDTO;

class WeatherService extends AbstractWeatherMap
{
    public function getCurrentWeatherByCity(string $city): array
    {
        $query = [
            'q' => $city,
            'appid' => $this->config->get('openweathermap')['api_key'],
        ];
        $url = $this->config->get('openweathermap')['url_current'];

        return $this->currentWeather($query, $url);
    }

    public function makeWeatherHtml(WeatherDTO $weatherDTO, string $city): string
    {
        return "
            __Погода на сегодня в городе *$city:*__
           `Температура от $weatherDTO->tempMin °C до $weatherDTO->tempMax °C`
           `По ощущенинию: $weatherDTO->feelsLike °C`
           `Скорость ветра: $weatherDTO->windSpeed м/с`
        ";
    }
}