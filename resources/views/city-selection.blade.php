<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wähle deine Heimatstadt</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

    @vite('resources/css/app.css')

</head>
<body class="bg-gray-900 text-gray-200">
    <div class="flex h-screen">
        <div class="w-2/5 flex flex-col justify-center items-center p-5 bg-gray-800 shadow-lg z-10">
            <div class="w-full max-w-sm">
                <h1 class="text-3xl font-bold mb-4">Wähle deine Heimatstadt</h1>
                <p class="mb-6 text-gray-400">Gib den Namen deiner Stadt ein, um dein Spiel zu beginnen.</p>
                <form action="{{ route('city.store') }}" method="POST">
                    @csrf
                    <input type="text" name="home_city" id="home_city" placeholder="z.B. Berlin oder Hamburg"
                           class="w-full px-4 py-2 mb-4 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-300">
                        Spiel starten
                    </button>
                </form>
            </div>
        </div>
        <div class="w-3/5 relative">
            <div id="map" class="w-full h-full filter blur-sm"></div>
        </div>
    </div>
    
    <script>
        // Icons für die Dienste erstellen
        var fireIcon = L.divIcon({
            className: 'flex justify-center items-center w-20 h-20 rounded-full shadow-lg bg-red-600',
            html: '<i class="fas fa-fire text-white text-3xl"></i>',
            iconAnchor: [40, 40]
        });

        var medicalIcon = L.divIcon({
            className: 'flex justify-center items-center w-20 h-20 rounded-full shadow-lg bg-yellow-500',
            html: '<i class="fas fa-briefcase-medical text-white text-3xl"></i>',
            iconAnchor: [40, 40]
        });

        var policeIcon = L.divIcon({
            className: 'flex justify-center items-center w-20 h-20 rounded-full shadow-lg bg-blue-600',
            html: '<i class="fas fa-handcuffs text-white text-3xl"></i>',
            iconAnchor: [40, 40]
        });

        var map = L.map('map', {
            zoomControl: false,
            attributionControl: false,
            dragging: false,
            touchZoom: false,
            scrollWheelZoom: false,
            doubleClickZoom: false
        }).setView([52.5200, 13.4050], 13);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

        L.marker([52.525, 13.415], {icon: fireIcon, interactive: false}).addTo(map);
        L.marker([52.51, 13.40], {icon: medicalIcon, interactive: false}).addTo(map);
        L.marker([52.53, 13.39], {icon: policeIcon, interactive: false}).addTo(map);
        
        map.on('contextmenu', function (e) {
            e.originalEvent.preventDefault();
        });
    </script>
</body>
</html>