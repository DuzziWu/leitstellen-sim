<div id="dispatch-panel"
    class="fixed top-0 -left-[700px] h-screen w-[700px] bg-white text-gray-800 p-6 shadow-xl transition-all duration-300 transform rounded-r-xl flex flex-col"
    style="z-index: 1000;">
    <button id="close-dispatch-panel-button"
        class="absolute top-4 right-4 text-3xl font-light text-gray-400 hover:text-red-500 transition-colors">&times;</button>
    <div class="mb-6">
        <h2 id="dispatch-panel-name" class="text-2xl font-bold pb-1 text-gray-900">Einsatzdetails</h2>
        <p class="text-sm text-gray-500">ID: <span id="dispatch-panel-id"></span></p>
    </div>

    <div class="flex-grow overflow-y-auto pr-2 space-y-6">
        <div class="bg-gray-100 p-4 rounded-lg shadow-inner">
            <h3 class="text-lg font-bold mb-2 text-gray-800">Anrufslog</h3>
            <p class="text-sm text-gray-700 font-semibold mb-1">Anrufer meldete:</p>
            <p id="dispatch-details-description" class="text-base text-gray-900"></p>
        </div>

        <div id="dispatch-details-reward-block"
            class="bg-yellow-100 p-3 rounded-lg shadow-inner flex items-center justify-between">
            <p class="text-sm text-yellow-800 font-semibold">Vergütung: <span id="dispatch-details-reward"
                    class="font-bold"></span> €</p>
        </div>

        <div>
            <h3 class="text-lg font-bold mb-2 text-gray-800">Einsatzkräfte vor Ort</h3>
            <div id="alarmed-vehicles-list" class="space-y-2">
                <p id="no-vehicles-message" class="text-gray-500">Keine Einsatzkräfte am Einsatzort.</p>
            </div>
            <button id="alert-button"
                class="mt-4 w-full py-3 px-4 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                Fahrzeuge alarmieren
            </button>
        </div>
    </div>
</div>