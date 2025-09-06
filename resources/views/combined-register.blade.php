<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen beim Leitstellen-Simulator</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body class="relative h-screen overflow-hidden">

    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/e/e8/Verwaltungsgliederung_Deutschland.png'); filter: blur(8px); transform: scale(1.05);"></div>

    <div class="absolute inset-0 flex items-center justify-center z-0">
        <i class="fa-solid fa-fire-truck text-red-500 text-5xl opacity-50 absolute" style="top: 20%; left: 30%;"></i>
        <i class="fa-solid fa-car-on text-blue-500 text-5xl opacity-50 absolute" style="top: 50%; right: 20%;"></i>
        <i class="fa-solid fa-house-chimney-medical text-white text-5xl opacity-50 absolute" style="bottom: 10%; left: 60%;"></i>
    </div>

    <div class="relative z-10 flex items-center justify-center w-full h-full p-4 md:p-8">
        <div class="bg-gray-900 bg-opacity-60 backdrop-blur-sm p-8 rounded-lg shadow-lg w-full max-w-lg text-gray-200">
            <form id="registration-flow-form" action="{{ route('register.flow') }}" method="POST">
                @csrf
                <div id="step-1">
                    <h2 class="text-3xl font-bold mb-4 text-center">Registrierung</h2>
                    <div class="mb-4">
                        <label for="name" class="block text-gray-400">Benutzername</label>
                        <input type="text" name="name" id="name" required
                               class="w-full px-4 py-2 mt-2 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-400">E-Mail</label>
                        <input type="email" name="email" id="email" required
                               class="w-full px-4 py-2 mt-2 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-400">Passwort</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-2 mt-2 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-400">Passwort bestätigen</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-2 mt-2 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="button" onclick="nextStep()"
                            class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-300">
                        Weiter
                    </button>
                </div>

                <div id="step-2" class="hidden">
                    <h2 class="text-3xl font-bold mb-4 text-center">Heimatstadt auswählen</h2>
                    <div class="mb-6">
                        <label for="home_city" class="block text-gray-400">Gib den Namen deiner Stadt ein</label>
                        <input type="text" name="home_city" id="home_city" required
                               class="w-full px-4 py-2 mt-2 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-300">
                        Spiel starten
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <h3 class="text-sm text-center text-gray-400 mb-2">Fortschritt</h3>
                <div class="w-full bg-gray-700 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-in-out" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const progressBar = document.getElementById('progress-bar');
        const form = document.getElementById('registration-flow-form');

        function nextStep() {
            // Einfache Validierung vor dem Übergang
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;

            if (name && email && password && password === password_confirmation) {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                progressBar.style.width = '50%';
            } else {
                alert('Bitte fülle alle Registrierungsfelder korrekt aus.');
            }
        }
    </script>
</body>
</html>