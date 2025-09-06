<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescue Operator</title>

    {{-- CSRF-Token für sichere AJAX-Anfragen --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Externe Bibliotheken: Leaflet für die Karte und Font Awesome für Icons --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    {{-- Vite-Assets für Tailwind CSS und unser eigenes JavaScript --}}
    @vite('resources/css/app.css')

    <script>
        // Diese globalen JavaScript-Variablen werden serverseitig mit Blade-Werten befüllt.
        // Sie sind für map.js verfügbar, um Kartenansicht und Benutzeraktionen zu steuern.
        const userLat = {{ auth()->user()->home_city_lat ?? 52.5200 }};
        const userLon = {{ auth()->user()->home_city_lon ?? 13.4050 }};
        const userId = {{ auth()->id() }};
    </script>

    @vite('resources/js/map.js')
</head>

<body class="bg-gray-900 text-white font-sans">
    {{-- Hauptkarte: Das zentrale Element der Benutzeroberfläche --}}
    <div id="map" class="h-screen w-screen absolute top-0 left-0"></div>

    {{-- Bedienelemente auf der Karte: Buttons für Baumodus und Einsatzgenerierung --}}
    <div class="fixed top-4 right-4 z-50" style="z-index: 1000;">
        <button id="build-mode-button" title="Wache bauen"
            class="flex items-center justify-center w-12 h-12 bg-gray-800 bg-opacity-90 text-white rounded-lg shadow-lg mb-2 transform transition duration-200 hover:scale-110">
            <i class="fas fa-hammer text-xl"></i>
        </button>
        <button id="generate-dispatch-btn" title="Einsatz generieren"
            class="flex items-center justify-center w-12 h-12 bg-gray-800 bg-opacity-90 text-white rounded-lg shadow-lg mb-2 transform transition duration-200 hover:scale-1.10">
            <i class="fas fa-fire-extinguisher text-xl"></i>
        </button>
    </div>

    {{-- Dynamische Seitenfenster (Panels), ausgelagert in Blade-Komponenten --}}
    <x-buy-panel />
    <x-manage-panel />
    <x-buy-vehicle-panel />
    <x-dispatch-panel />

</body>

</html>