import { initializeMap } from './map.js';

document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const userLat = body.dataset.userLat;
    const userLon = body.dataset.userLon;

    if (userLat && userLon) {
        initializeMap(parseFloat(userLat), parseFloat(userLon));
    } else {
        console.error('Fehler: Benutzerkoordinaten nicht verfügbar.');
    }
});