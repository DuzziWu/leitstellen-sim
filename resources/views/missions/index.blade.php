<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Leitstellen-Simulator</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    
    <link rel="stylesheet" href="/.vite/assets/app.css">
    <script src="/.vite/assets/app.js" defer></script>

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        #map {
            height: 100vh;
            width: 100vw;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .map-controls-top-right {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }

        .map-controls-top-left {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .control-button {
            background-color: rgba(44, 62, 80, 0.8);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            transition: background-color 0.3s ease;
        }

        .control-button:hover {
            background-color: rgba(52, 73, 94, 0.9);
        }

        .custom-div-icon {
            background-color: transparent;
            border: none;
        }
        .building-marker {
            background-color: #3498db;
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .mission-marker {
            background-color: #e74c3c;
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .leaflet-popup-content-wrapper {
            background-color: rgba(44, 62, 80, 0.9);
            color: white;
            border-radius: 8px;
            font-size: 14px;
        }
        .leaflet-popup-tip {
            background-color: rgba(44, 62, 80, 0.9);
        }

        .poi-marker {
            background-color: #8e44ad;
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        
    </style>
</head>
<body>
    <div id="map"></div>

    <div class="map-controls-top-right">
        <form method="GET" action="{{ route('dashboard') }}">
            @csrf
            <button type="submit" class="control-button" title="Dashboard">
                <i class="fa-solid fa-house"></i>
            </button>
        </form>
    </div>

    <div class="map-controls-top-left">
        <form method="POST" action="{{ route('buildings.store.from_map') }}">
            @csrf
            <button type="submit" class="control-button" title="Neue Wache bauen">
                <i class="fa-solid fa-helmet-safety"></i>
            </button>
        </form>
        <form method="POST" action="{{ route('missions.generate') }}">
            @csrf
            <button type="submit" class="control-button" title="Einsatz generieren">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        var missions = @json($missions);
        var buildings = @json($buildings);
        var userLat = @json($userLat);
        var userLon = @json($userLon);

        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('map', {
                zoomControl: false,
                scrollWheelZoom: true,
                dragging: true,
            }).setView([userLat, userLon], 15);

            map.attributionControl.setPrefix(false);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
            }).addTo(map);

            var buildingHtmlIcon = L.divIcon({
                className: 'custom-div-icon',
                html: '<div class="building-marker"><i class="fa-solid fa-building"></i></div>',
                iconSize: [35, 35],
                iconAnchor: [17, 35],
                popupAnchor: [0, -30]
            });

            var missionHtmlIcon = L.divIcon({
                className: 'custom-div-icon',
                html: '<div class="mission-marker"><i class="fa-solid fa-fire-flame-curved"></i></div>',
                iconSize: [35, 35],
                iconAnchor: [17, 35],
                popupAnchor: [0, -30]
            });

            buildings.forEach(building => {
                var popupContent = '<b>Wache: ' + building.name + '</b>';
                L.marker([building.latitude, building.longitude], {icon: buildingHtmlIcon})
                    .addTo(map)
                    .bindPopup(popupContent);
            });

            missions.forEach(mission => {
                var popupContent = '<b>' + mission.mission_type.name + '</b>';
                L.marker([mission.latitude, mission.longitude], {icon: missionHtmlIcon})
                    .addTo(map)
                    .bindPopup(popupContent);
            });

            var poiLayer = L.layerGroup().addTo(map);
            var fetchTimeout;

            function loadPois() {
                var currentZoom = map.getZoom();

                // POIs erst ab Zoom-Level 15 anzeigen
                if (currentZoom < 15) {
                    poiLayer.clearLayers();
                    return;
                }

                clearTimeout(fetchTimeout);
                fetchTimeout = setTimeout(function() {
                    var bounds = map.getBounds();
                    var minLat = bounds.getSouthWest().lat;
                    var minLon = bounds.getSouthWest().lng;
                    var maxLat = bounds.getNorthEast().lat;
                    var maxLon = bounds.getNorthEast().lng;
                    
                    // POIs vom Backend-Endpunkt laden
                    fetch(`/api/pois?minLat=${minLat}&minLon=${minLon}&maxLat=${maxLat}&maxLon=${maxLon}`)
                        .then(response => response.json())
                        .then(data => {
                            poiLayer.clearLayers();
                            data.forEach(poi => {
                                var poiHtmlIcon = L.divIcon({
                                    className: 'custom-div-icon',
                                    html: '<div class="poi-marker"><i class="fa-solid fa-store"></i></div>', // Beispiel-Icon
                                    iconSize: [35, 35],
                                    iconAnchor: [17, 35],
                                    popupAnchor: [0, -30]
                                });

                                L.marker([poi.lat, poi.lon], {icon: poiHtmlIcon})
                                    .addTo(poiLayer)
                                    .bindPopup('<b>' + poi.name + '</b>');
                            });
                        })
                        .catch(error => console.error('Fehler beim Laden der POIs:', error));
                }, 500); // 500ms Verzögerung, um Anfragen zu bündeln
            }

            // POIs beim ersten Laden, bei Zoom und Bewegung aktualisieren
            map.on('moveend', loadPois);
            loadPois();
        });
    </script>
</body>
</html>