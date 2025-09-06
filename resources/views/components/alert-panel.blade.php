<div id="alert-panel"
    class="fixed top-0 -left-[700px] h-screen w-[700px] bg-white text-gray-800 p-6 shadow-xl transition-all duration-300 transform rounded-r-xl flex flex-col"
    style="z-index: 1000;">
    <button id="close-alert-panel-button"
        class="absolute top-4 right-4 text-3xl font-light text-gray-400 hover:text-red-500 transition-colors">&times;</button>
    <div class="mb-6">
        <h2 class="text-2xl font-bold pb-1 text-gray-900">Fahrzeuge alarmieren</h2>
        <p class="text-sm text-gray-500">Einsatz ID: <span id="alert-dispatch-id"></span></p>
    </div>

    <div class="flex-grow overflow-y-auto pr-2">
        {{-- Hier werden die Wachen und ihre Fahrzeuge dynamisch eingefügt --}}
        <div id="station-list" class="space-y-4">
            <p class="text-gray-500">Lade Wachen...</p>
        </div>
    </div>

    <div class="mt-auto pt-4 border-t border-gray-200">
        <button id="send-alert-button"
            class="w-full py-3 px-4 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-colors">
            Ausgewählte Fahrzeuge zum Einsatz schicken
        </button>
    </div>
</div>