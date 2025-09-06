<div id="manage-panel"
    class="fixed top-0 -left-[700px] h-screen w-[700px] bg-white text-gray-800 p-6 shadow-xl transition-all duration-300 transform rounded-r-xl flex flex-col"
    style="z-index: 1000;">
    <button id="close-manage-panel-button"
        class="absolute top-4 right-4 text-3xl font-light text-gray-400 hover:text-red-500 transition-colors">&times;</button>
    <div class="mb-6">
        <h2 id="manage-panel-name" class="text-2xl font-bold pb-1 text-gray-900"></h2>
        <p class="text-sm text-gray-500">Typ: <span id="manage-panel-type"></span> | Adresse: <span
                id="manage-panel-address"></span></p>
    </div>

    <div class="flex border-b border-gray-200 mb-6">
        <button
            class="tab-button py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-blue-500 transition-colors duration-200 active-tab"
            data-tab="overview">
            Übersicht
        </button>
        <button
            class="tab-button py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-blue-500 transition-colors duration-200"
            data-tab="vehicles">
            Fahrzeuge
        </button>
        <button
            class="tab-button py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-blue-500 transition-colors duration-200"
            data-tab="personnel">
            Personal
        </button>
        <button
            class="tab-button py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-blue-500 transition-colors duration-200"
            data-tab="upgrades">
            Erweiterungen
        </button>
    </div>

    <div id="tab-content" class="flex-grow">
        <div id="overview-content" class="tab-panel active">
            <h3 class="text-lg font-bold mb-4">Wachen-Übersicht</h3>
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="text-gray-700">Hier kommen alle wichtigen Infos zur Wache hin.</p>
            </div>
        </div>

        <div id="vehicles-content" class="tab-panel hidden">
            <h3 class="text-lg font-bold mb-4">Fahrzeugstellplätze (<span id="vehicle-count">0</span>/4 belegt)</h3>
            <div class="grid grid-cols-2 gap-4">
                <div id="slot-1"
                    class="vehicle-slot bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 h-48 flex flex-col justify-center items-center cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                    data-slot-id="1">
                    <i class="fas fa-plus text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-semibold">Fahrzeug kaufen</p>
                </div>
                <div id="slot-2"
                    class="vehicle-slot bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 h-48 flex flex-col justify-center items-center cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                    data-slot-id="2">
                    <i class="fas fa-plus text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-semibold">Fahrzeug kaufen</p>
                </div>
                <div id="slot-3"
                    class="vehicle-slot bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 h-48 flex flex-col justify-center items-center cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                    data-slot-id="3">
                    <i class="fas fa-plus text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-semibold">Fahrzeug kaufen</p>
                </div>
                <div id="slot-4"
                    class="vehicle-slot bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 h-48 flex flex-col justify-center items-center cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                    data-slot-id="4">
                    <i class="fas fa-plus text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-semibold">Fahrzeug kaufen</p>
                </div>
            </div>
        </div>

        <div id="personnel-content" class="tab-panel hidden">
            <h3 class="text-lg font-bold mb-4">Personalverwaltung</h3>
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="text-gray-700">Hier können Personalstellen besetzt und verwaltet werden.</p>
            </div>
        </div>

        <div id="upgrades-content" class="tab-panel hidden">
            <h3 class="text-lg font-bold mb-4">Wachen-Erweiterungen</h3>
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="text-gray-700">Hier können neue Stellplätze und andere Erweiterungen gekauft werden.</p>
            </div>
        </div>
    </div>
</div>