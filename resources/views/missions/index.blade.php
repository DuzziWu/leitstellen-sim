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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-user-lat="{{ $userLat }}" data-user-lon="{{ $userLon }}">
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
        <button id="showRadiusBtn" class="control-button" title="Radius anzeigen/ausblenden">
            <i class="fa-solid fa-circle-notch"></i>
        </button>
    </div>

    <div id="building-sidebar" class="building-sidebar">
        <button id="close-sidebar" class="close-sidebar-btn">&times;</button>
        <h3>Neue Wache bauen</h3>
        <p>Wähle einen Standort auf der Karte aus.</p>
        
        <form id="building-form" method="POST" action="{{ route('buildings.store.from_map') }}">
            @csrf
            <div class="form-group">
                <label for="building-name">Name der Wache:</label>
                <input type="text" id="building-name" name="name" required>
            </div>

            <div class="form-group">
                <label for="building-address">Adresse:</label>
                <input type="text" id="building-address" name="address">
            </div>
            
            <input type="hidden" id="building-lat" name="latitude">
            <input type="hidden" id="building-lon" name="longitude">
            
            <button type="submit" class="submit-btn" id="submit-building-btn" disabled>Wache bauen</button>
        </form>
    </div>
</body>
</html>