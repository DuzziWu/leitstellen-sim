<div id="buy-panel"
    class="fixed top-0 -left-[700px] h-screen w-[700px] bg-white text-gray-800 p-6 shadow-xl transition-all duration-300 transform rounded-r-xl flex flex-col gap-6"
    style="z-index: 1000;">
    <button id="close-buy-panel-button"
        class="absolute top-4 right-4 text-3xl font-light text-gray-400 hover:text-red-500 transition-colors">&times;</button>
    <h2 id="buy-panel-name" class="text-2xl font-bold pb-2 border-b border-gray-200"></h2>
    <p class="text-base">Typ: <span id="buy-panel-type" class="font-semibold text-gray-700"></span></p>
    <p class="text-base">Adresse: <span id="buy-panel-address" class="font-semibold text-gray-700"></span></p>

    <button id="buy-station-button"
        class="mt-auto py-3 px-4 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-colors">
        Kaufen (kostenlos)
    </button>
</div>