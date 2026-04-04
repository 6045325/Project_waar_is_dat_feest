<?php
declare(strict_types=1);

/**
 * WeatherManager - Haalt weer data op via Open-Meteo API
 * Open-Meteo biedt gratis weer data zonder API key
 */
class WeatherManager {
    private const API_URL = "https://api.open-meteo.com/v1/forecast";
    private const CACHE_DIR = __DIR__ . "/../cache/weather/";
    private const CACHE_DURATION = 3600; // 1 uur
    
    public function __construct() {
        // Zorg ervoor dat cache directory bestaat
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }
    }

    /**
     * Haal weer data op voor een gegeven locatie (lat, lng)
     */
    public function getWeather(float $latitude, float $longitude): ?array {
        $cacheFile = $this->getCacheFile($latitude, $longitude);
        
        // Check of cache nog geldig is
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < self::CACHE_DURATION)) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        // Haal nieuwe data op
        $weatherData = $this->fetchWeatherFromAPI($latitude, $longitude);
        
        if ($weatherData) {
            // Zet om in leesbaar format
            $formattedData = $this->formatWeatherData($weatherData);
            // Sla op in cache
            file_put_contents($cacheFile, json_encode($formattedData));
            return $formattedData;
        }

        return null;
    }

    /**
     * Haal weer data op voor meerdere locaties
     */
    public function getWeatherForMultipleLocations(array $locations): array {
        $result = [];
        
        foreach ($locations as $id => $location) {
            if (isset($location['lat']) && isset($location['lng'])) {
                $weather = $this->getWeather($location['lat'], $location['lng']);
                if ($weather) {
                    $result[$id] = $this->formatWeatherData($weather);
                }
            }
        }

        return $result;
    }

    /**
     * Haal weer data op van API
     */
    private function fetchWeatherFromAPI(float $latitude, float $longitude): ?array {
        try {
            $params = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                'hourly' => 'temperature_2m,precipitation,weather_code',
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weather_code',
                'timezone' => 'Europe/Amsterdam'
            ];

            $url = self::API_URL . '?' . http_build_query($params);

            $opts = [
                "http" => [
                    "method" => "GET",
                    "timeout" => 5,
                    "header" => "User-Agent: eventify/1.0\r\n"
                ]
            ];

            $context = stream_context_create($opts);
            $response = file_get_contents($url, false, $context);

            if ($response === false) {
                return null;
            }

            return json_decode($response, true);
        } catch (Exception $e) {
            error_log("Weather API error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Zet raw API data om in leesbaar format
     */
    private function formatWeatherData(array $data): array {
        if (!isset($data['current'])) {
            return [];
        }

        $current = $data['current'];
        $daily = $data['daily'][0] ?? [];

        return [
            'current' => [
                'temperature' => $current['temperature_2m'] ?? 0,
                'humidity' => $current['relative_humidity_2m'] ?? 0,
                'weather_code' => $current['weather_code'] ?? 0,
                'wind_speed' => $current['wind_speed_10m'] ?? 0,
                'description' => $this->getWeatherDescription($current['weather_code'] ?? 0),
                'icon' => $this->getWeatherIcon($current['weather_code'] ?? 0)
            ],
            'daily' => [
                'temp_max' => $daily['temperature_2m_max'] ?? 0,
                'temp_min' => $daily['temperature_2m_min'] ?? 0,
                'precipitation' => $daily['precipitation_sum'] ?? 0,
                'weather_code' => $daily['weather_code'] ?? 0
            ]
        ];
    }

    /**
     * Zet WMO weather code om in beschrijving
     */
    private function getWeatherDescription(int $code): string {
        $descriptions = [
            0 => 'Helder',
            1 => 'Overwegend helder',
            2 => 'Gedeeltelijk bewolkt',
            3 => 'Bewolkt',
            45 => 'Mistig',
            48 => 'Dichte mist',
            51 => 'Licht motregen',
            53 => 'Matig motregen',
            55 => 'Dicht motregen',
            61 => 'Lichte regen',
            63 => 'Matige regen',
            65 => 'Zware regen',
            71 => 'Lichte sneeuw',
            73 => 'Matige sneeuw',
            75 => 'Zware sneeuw',
            77 => 'Sneeuwkorrels',
            80 => 'Lichte regenbuien',
            81 => 'Matige regenbuien',
            82 => 'Zware regenbuien',
            85 => 'Lichte sneeuwbuien',
            86 => 'Zware sneeuwbuien',
            95 => 'Onweer',
            96 => 'Onweer met hagel',
            99 => 'Onweer met zware hagel'
        ];

        return $descriptions[$code] ?? 'Onbekend';
    }

    /**
     * Geef emoji/icon terug voor weather code
     */
    private function getWeatherIcon(int $code): string {
        if ($code == 0) return '☀️';
        if ($code == 1 || $code == 2) return '⛅';
        if ($code == 3) return '☁️';
        if ($code == 45 || $code == 48) return '🌫️';
        if (in_array($code, [51, 53, 55, 61, 63, 65, 80, 81, 82])) return '🌧️';
        if (in_array($code, [71, 73, 75, 77, 85, 86])) return '❄️';
        if (in_array($code, [95, 96, 99])) return '⛈️';
        return '🌥️';
    }

    /**
     * Bepaal cache bestandsnaam
     */
    private function getCacheFile(float $latitude, float $longitude): string {
        $hash = md5(round($latitude, 2) . '-' . round($longitude, 2));
        return self::CACHE_DIR . $hash . '.json';
    }

    /**
     * Wis cache voor locatie
     */
    public function clearCache(float $latitude, float $longitude): bool {
        $cacheFile = $this->getCacheFile($latitude, $longitude);
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }
        return false;
    }

    /**
     * Wis alle cache
     */
    public function clearAllCache(): bool {
        $files = glob(self::CACHE_DIR . '*.json');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
}
?>