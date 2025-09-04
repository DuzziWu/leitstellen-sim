import * as L from 'leaflet';

export function initializeMap(userLat, userLon) {
    const map = L.map('map', {
        zoomControl: false,
        scrollWheelZoom: true,
        dragging: true,
    }).setView([userLat, userLon], 15);

    map.attributionControl.setPrefix(false);

    // Dunkles Karten-Design von CARTO verwenden
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
    }).addTo(map);

    // Rechtsklick auf der Karte deaktivieren
    document.getElementById('map').addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Neue, typ-basierte Icons für die Wachen
    const stationIcons = {
        'Feuerwache': L.divIcon({
            className: 'custom-div-icon station-marker station-feuerwehr',
            html: '<i class="fa-solid fa-f"></i>',
            iconSize: [35, 35],
            iconAnchor: [17, 35],
            popupAnchor: [0, -30]
        }),
        'Polizeiwache': L.divIcon({
            className: 'custom-div-icon station-marker station-polizei',
            html: '<i class="fa-solid fa-p"></i>',
            iconSize: [35, 35],
            iconAnchor: [17, 35],
            popupAnchor: [0, -30]
        }),
        'Rettungswache': L.divIcon({
            className: 'custom-div-icon station-marker station-rettung',
            html: '<i class="fa-solid fa-r"></i>',
            iconSize: [35, 35],
            iconAnchor: [17, 35],
            popupAnchor: [0, -30]
        })
    };
    
    // Einsatz-Icon (rotes abgerundetes Viereck)
    var missionHtmlIcon = L.divIcon({
        className: 'custom-div-icon mission-marker mission-red',
        html: '<i class="fa-solid fa-fire-flame-curved"></i>',
        iconSize: [35, 35],
        iconAnchor: [17, 35],
        popupAnchor: [0, -30]
    });

    // Daten von der API abrufen und Marker hinzufügen
    fetch('/api/stations')
        .then(response => response.json())
        .then(stations => {
            stations.forEach(station => {
                const lat = parseFloat(station.latitude);
                const lon = parseFloat(station.longitude);

                if (!isNaN(lat) && !isNaN(lon)) {
                    const icon = stationIcons[station.type] || L.Icon.Default();
                    const marker = L.marker([lat, lon], { icon: icon }).addTo(map);
                    marker.bindPopup(`<b>${station.name}</b><br>Typ: ${station.type || 'Unbekannt'}`);
                }
            });
        })
        .catch(error => {
            console.error('Fehler beim Abrufen der Wachen-Daten:', error);
        });

    return map;
}