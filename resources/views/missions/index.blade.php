<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Einsatzkarte
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="map" style="height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('map').setView([52.5200, 13.4050], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var missions = @json($missions);

            missions.forEach(mission => {
                var popupContent = '<b>Einsatz-ID:</b> ' + mission.id + '<br>';
                // Später mehr Details hinzufügen
                L.marker([mission.latitude, mission.longitude])
                    .addTo(map)
                    .bindPopup(popupContent);
            });
        });
    </script>
</x-app-layout>