<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Neue Wache bauen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('buildings.store') }}">
                        @csrf
                        <div>
                            <label for="name">Name der Wache:</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="mt-4">
                            <label for="location">Standort der Wache auf der Karte auswählen:</label>
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            <input type="hidden" name="latitude" id="latitude" required>
                            <input type="hidden" name="longitude" id="longitude" required>
                        </div>

                        <button type="submit">Wache erstellen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        var map = L.map('map').setView([52.5200, 13.4050], 12);

        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker;

        map.on('click', function(e) {
            // Entferne alten Marker, falls vorhanden
            if (marker) {
                map.removeLayer(marker);
            }

            // Füge neuen Marker hinzu und aktualisiere die Formularfelder
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });
    </script>
</x-app-layout>