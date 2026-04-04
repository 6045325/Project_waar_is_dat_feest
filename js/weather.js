/**
 * Weather Widget - Laadt weer data voor activiteiten
 */
class WeatherWidget {
    constructor() {
        // Bepaal de juiste API URL gebaseerd op de huidge locatie
        const path = window.location.pathname;
        const projectPath = path.substring(0, path.lastIndexOf('/') + 1); // Geeft /php/Project_waar_is_dat_feest/
        this.apiUrl = projectPath + 'api/weather.php';
        this.weatherCache = new Map();
        console.log('Weather API URL:', this.apiUrl);
    }

    /**
     * Laad weer data voor alle activiteiten op de pagina
     */
    async loadWeatherForActivities() {
        const activities = document.querySelectorAll('[data-lat][data-lng]');
        
        activities.forEach(activity => {
            const lat = parseFloat(activity.dataset.lat);
            const lng = parseFloat(activity.dataset.lng);
            const activityId = activity.dataset.activityId;

            if (lat && lng) {
                this.loadWeatherForLocation(lat, lng, activityId);
            }
        });
    }

    /**
     * Laad weer data voor één locatie
     */
    async loadWeatherForLocation(lat, lng, activityId) {
        const cacheKey = `${lat.toFixed(2)}-${lng.toFixed(2)}`;
        
        // Check cache
        if (this.weatherCache.has(cacheKey)) {
            this.displayWeather(this.weatherCache.get(cacheKey), activityId);
            return;
        }

        try {
            const response = await fetch(`${this.apiUrl}?lat=${lat}&lng=${lng}`);
            
            if (!response.ok) {
                console.error(`Weather API error: ${response.status}`);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            
            console.log('Weather API response:', result);
            
            if (result.success && result.data) {
                this.weatherCache.set(cacheKey, result.data);
                this.displayWeather(result.data, activityId);
            } else {
                console.error('Invalid response format:', result);
                this.displayWeatherError(activityId);
            }
        } catch (error) {
            console.error('Fout bij laden weer data:', error);
            this.displayWeatherError(activityId);
        }
    }

    /**
     * Toon weer gegevens
     */
    displayWeather(weatherData, activityId) {
        const weatherContainer = document.querySelector(
            `[data-activity-id="${activityId}"] .weather-widget`
        );

        if (!weatherContainer) return;

        const current = weatherData.current || {};
        
        weatherContainer.innerHTML = `
            <div class="weather-info">
                <div class="weather-icon">${current.icon || '🌥️'}</div>
                <div class="weather-details">
                    <div class="weather-temp">${Math.round(current.temperature)}°C</div>
                    <div class="weather-desc">${current.description || 'Onbekend'}</div>
                    <div class="weather-extra">
                        <span class="weather-humidity">💧 ${current.humidity || 0}%</span>
                        <span class="weather-wind">💨 ${Math.round(current.wind_speed || 0)} km/h</span>
                    </div>
                </div>
            </div>
        `;
        
        weatherContainer.classList.add('weather-loaded');
    }

    /**
     * Toon weer fout
     */
    displayWeatherError(activityId) {
        const weatherContainer = document.querySelector(
            `[data-activity-id="${activityId}"] .weather-widget`
        );

        if (!weatherContainer) return;

        weatherContainer.innerHTML = `
            <div class="weather-error">
                <div class="weather-icon">❌</div>
                <div class="weather-details">
                    <div class="weather-desc">Weer onbeschikbaar</div>
                </div>
            </div>
        `;
    }
}

// Initialiseer weather widget wanneer DOM klaar is
document.addEventListener('DOMContentLoaded', () => {
    const weatherWidget = new WeatherWidget();
    weatherWidget.loadWeatherForActivities();
});
