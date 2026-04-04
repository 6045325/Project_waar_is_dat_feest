/**
 * Map Manager - Toont activiteiten op een interactieve kaart
 * Gebruikt Leaflet.js voor kaartfunctionaliteit
 */
class MapManager {
    constructor() {
        this.map = null;
        this.markers = new Map();
        this.mapContainer = document.getElementById('activity-map');
    }

    /**
     * Initialiseer de kaart
     */
    init() {
        if (!this.mapContainer) {
            console.warn('Activity map container not found');
            return;
        }

        // Initialiseer kaart met Nederland als standaard locatie
        this.map = L.map('activity-map').setView([52.1326, 5.2913], 7);

        // Voeg kaart tiles toe (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>'
        }).addTo(this.map);

        // Laad activiteiten markers
        this.loadActivityMarkers();

        // Voeg gebruiker locatie toe als beschikbaar
        this.getUserLocation();
    }

    /**
     * Laad alle activiteiten als markers op de kaart
     */
    loadActivityMarkers() {
        const activities = document.querySelectorAll('[data-lat][data-lng]');
        
        if (activities.length === 0) {
            console.log('Geen activiteiten met coördinaten gevonden');
            return;
        }

        let bounds = L.latLngBounds();
        let hasValidMarkers = false;

        activities.forEach(activity => {
            const lat = parseFloat(activity.dataset.lat);
            const lng = parseFloat(activity.dataset.lng);
            const activityId = activity.dataset.activityId;

            if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                const titel = activity.querySelector('h2')?.textContent || 'Activiteit';
                const locatie = activity.querySelector('[strong="Locatie:"]')?.parentElement?.textContent || '';
                
                this.addMarker(lat, lng, activityId, titel, locatie);
                bounds.extend([lat, lng]);
                hasValidMarkers = true;
            }
        });

        // Zet kaart zoom zodat alle markers zichtbaar zijn
        if (hasValidMarkers && bounds.isValid()) {
            this.map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    /**
     * Voeg marker toe voor activiteit
     */
    addMarker(lat, lng, activityId, titel, locatie) {
        const markerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(this.map);
        
        const popupContent = `
            <div class="map-popup">
                <strong>${titel}</strong>
                <p>${locatie}</p>
                <button class="popup-btn" onclick="openDetailModal(${activityId})">Details</button>
            </div>
        `;
        
        marker.bindPopup(popupContent);
        this.markers.set(activityId, marker);
    }

    /**
     * Haal gebruiker huidige locatie op
     */
    getUserLocation() {
        if (!navigator.geolocation) {
            console.log('Geolocation niet ondersteund');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                this.addUserMarker(latitude, longitude);
            },
            (error) => {
                console.log('Kan gebruiker locatie niet ophalen:', error.message);
            },
            { timeout: 5000 }
        );
    }

    /**
     * Voeg gebruiker locatie als marker toe
     */
    addUserMarker(lat, lng) {
        const userIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(this.map);
        userMarker.bindPopup('📍 Je huidige locatie');
        
        // Voeg cirkel toe voor zichtradius
        L.circle([lat, lng], {
            radius: 10000, // 10km
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.1,
            weight: 2
        }).addTo(this.map);
    }

    /**
     * Highlight activiteit marker op kaart
     */
    highlightMarker(activityId) {
        // Reset alle markers
        this.markers.forEach(marker => {
            marker.setOpacity(0.7);
        });

        // Highlight geselecteerde marker
        if (this.markers.has(activityId)) {
            const marker = this.markers.get(activityId);
            marker.setOpacity(1);
            marker.openPopup();
            this.map.panTo(marker.getLatLng());
        }
    }

    /**
     * Reset alle markers
     */
    resetMarkers() {
        this.markers.forEach(marker => {
            marker.setOpacity(0.7);
            marker.closePopup();
        });
    }
}

// Initialiseer kaart wanneer DOM klaar is
document.addEventListener('DOMContentLoaded', () => {
    const mapManager = new MapManager();
    mapManager.init();

    // Maak mapManager globaal beschikbaar
    window.mapManager = mapManager;
});
