// Globale Variablen
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
let currentStationId = null;
let dispatchesData = null;
let currentDispatch = null;
var buildMode = false;
var stationMarkers = [];
var dispatchMarkers = [];

//=======================================================================================================
// KARTEN-INITIALISIERUNG & MARKER-ICONS
//=======================================================================================================

var map = L.map("map").setView([userLat, userLon], 13);

L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    subdomains: "abcd",
    maxZoom: 19,
}).addTo(map);

map.on("contextmenu", function (e) {
    e.originalEvent.preventDefault();
});

var buildModeIcon = L.divIcon({
    className: "custom-div-icon",
    html: '<div class="bg-white w-8 h-8 rounded-md flex items-center justify-center shadow-lg"><i class="fas fa-hammer text-gray-800"></i></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
});

var playerStationIcon = L.divIcon({
    className: "custom-div-icon",
    html: '<div class="bg-red-600 w-8 h-8 rounded-md flex items-center justify-center shadow-lg"><i class="fas fa-fire text-white"></i></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
});

var dispatchIcon = L.divIcon({
    className: "custom-div-icon",
    html: '<div class="bg-yellow-500 w-8 h-8 rounded-full flex items-center justify-center shadow-lg animate-pulse"><i class="fas fa-exclamation-triangle text-gray-900"></i></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 16],
});

//=======================================================================================================
// PANEL-FUNKTIONEN
//=======================================================================================================

/**
 * Zeigt ein spezifisches Seitenfenster (Panel) an und verbirgt alle anderen.
 * @param {string|null} panelId - Die ID des anzuzeigenden Panels oder null zum Schließen.
 */
function showPanel(panelId) {
    const panels = {
        "buy-panel": {
            element: document.getElementById("buy-panel"),
            width: "700px",
        },
        "manage-panel": {
            element: document.getElementById("manage-panel"),
            width: "700px",
        },
        "buy-vehicle-panel": {
            element: document.getElementById("buy-vehicle-panel"),
            width: "600px",
        },
        "dispatch-panel": {
            element: document.getElementById("dispatch-panel"),
            width: "700px",
        },
        "alert-panel": {
            element: document.getElementById("alert-panel"),
            width: "700px",
        },
    };

    // Schließe alle Panels
    for (const id in panels) {
        panels[id].element.classList.remove("left-0");
        panels[id].element.classList.add(`-left-[${panels[id].width}]`);
    }

    // Öffne das gewünschte Panel
    if (panelId && panels[panelId]) {
        panels[panelId].element.classList.remove(
            `-left-[${panels[panelId].width}]`
        );
        panels[panelId].element.classList.add("left-0");
    }
}

// Tab-Funktionalität
const tabButtons = document.querySelectorAll(".tab-button");
const tabPanels = document.querySelectorAll(".tab-panel");

tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
        tabButtons.forEach((btn) => {
            btn.classList.remove(
                "active-tab",
                "text-gray-900",
                "border-blue-500"
            );
            btn.classList.add("text-gray-500", "border-transparent");
        });

        tabPanels.forEach((panel) => panel.classList.add("hidden"));

        button.classList.add("active-tab", "text-gray-900", "border-blue-500");
        button.classList.remove("text-gray-500", "border-transparent");

        const tab = button.dataset.tab;
        document.getElementById(`${tab}-content`).classList.remove("hidden");

        if (tab === "vehicles") {
            loadStationVehicles();
        }
    });
});

//=======================================================================================================
// WACHEN-FUNKTIONEN
//=======================================================================================================

/**
 * Zeigt das richtige Panel für eine Wache an (Kaufen oder Verwalten).
 * @param {object} station - Das Wachen-Objekt.
 */
function showStationPanel(station) {
    currentStationId = station.id;
    if (station.user_id === userId) {
        document.getElementById("manage-panel-name").innerText = station.name;
        document.getElementById("manage-panel-type").innerText = station.type;
        document.getElementById("manage-panel-address").innerText =
            station.address;
        showPanel("manage-panel");
        document.querySelector('.tab-button[data-tab="overview"]').click();
    } else {
        document.getElementById("buy-panel-name").innerText = station.name;
        document.getElementById("buy-panel-type").innerText = station.type;
        document.getElementById("buy-panel-address").innerText =
            station.address;
        document.getElementById("buy-station-button").dataset.stationId =
            station.id;
        showPanel("buy-panel");
    }
}

/**
 * Zeigt die Wachen auf der Karte an, basierend auf dem aktuellen Kartenausschnitt.
 * @param {object[]} stationsData - Array von Wachen-Objekten.
 */
function showStations(stationsData) {
    stationMarkers.forEach((marker) => map.removeLayer(marker));
    stationMarkers = [];
    if (stationsData.length === 0) return;
    stationsData.forEach((station) => {
        let marker;
        if (parseInt(station.user_id) === userId) {
            marker = L.marker([station.lat, station.lon], {
                icon: playerStationIcon,
            }).addTo(map);
        } else if (buildMode && station.user_id === null) {
            marker = L.marker([station.lat, station.lon], {
                icon: buildModeIcon,
            }).addTo(map);
        }
        if (marker) {
            marker.on("click", () => showStationPanel(station));
            stationMarkers.push(marker);
        }
    });
}

/**
 * Ruft die Wachen im aktuellen Kartenausschnitt vom Server ab.
 */
async function loadStationsInView() {
    const bounds = map.getBounds();
    const minLat = bounds.getSouthWest().lat;
    const minLon = bounds.getSouthWest().lng;
    const maxLat = bounds.getNorthEast().lat;
    const maxLon = bounds.getNorthEast().lng;
    const url = `/api/stations?minLat=${minLat}&minLon=${minLon}&maxLat=${maxLat}&maxLon=${maxLon}`;
    try {
        const response = await fetch(url);
        if (!response.ok)
            throw new Error("Fehler beim Abrufen der Wachen-Daten");
        const stations = await response.json();
        showStations(stations);
    } catch (error) {
        console.error("Fehler:", error);
    }
}

//=======================================================================================================
// FAHRZEUG-FUNKTIONEN
//=======================================================================================================

/**
 * Lädt die kaufbaren Fahrzeuge und füllt das Kauf-Panel.
 */
async function loadVehicles() {
    try {
        const response = await fetch("/data/vehicles_fire.json");
        const vehicles = await response.json();
        const vehicleList = document.getElementById("vehicle-list");
        vehicleList.innerHTML = "";

        vehicles.forEach((vehicle) => {
            const statsHtml = Object.entries(vehicle.stats)
                .map(([key, value]) => `${key.replace(/_/g, " ")}: ${value}`)
                .join(" | ");

            const vehicleElement = `
                <div class="flex items-center p-4 mb-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer buy-vehicle-button" data-vehicle-id="${
                    vehicle.id
                }">
                    <img src="${vehicle.image}" alt="${
                vehicle.name
            }" class="w-24 h-auto mr-4 rounded-md">
                    <div class="flex-grow">
                        <h4 class="text-lg font-bold">${vehicle.name}</h4>
                        <p class="text-sm text-gray-600">Preis: ${vehicle.price.toLocaleString(
                            "de-DE"
                        )} €</p>
                        <p class="text-xs text-gray-500">${statsHtml}</p>
                    </div>
                    <i class="fas fa-arrow-right text-gray-500"></i>
                </div>
            `;
            vehicleList.innerHTML += vehicleElement;
        });

        document.querySelectorAll(".buy-vehicle-button").forEach((button) => {
            button.addEventListener("click", async function () {
                const vehicleType = this.dataset.vehicleId;
                const url = `/api/stations/${currentStationId}/buy-vehicle`;

                try {
                    const response = await fetch(url, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ vehicle_type: vehicleType }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.error || "Fehler beim Kauf des Fahrzeugs."
                        );
                    }

                    // Credits aktualisieren
                    try {
                        if (data && typeof data.credits !== "undefined") {
                            const creditsEl = document.getElementById("credits-display");
                            if (creditsEl) {
                                creditsEl.innerText = Number(data.credits).toLocaleString("de-DE") + " €";
                            }
                        }
                    } catch (_) {}

                    showPanel("manage-panel");
                    document
                        .querySelector('.tab-button[data-tab="vehicles"]')
                        .click();
                } catch (error) {
                    console.error("Kauf-Fehler:", error);
                    alert(error.message);
                }
            });
        });
    } catch (error) {
        console.error("Fehler beim Laden der Fahrzeuge:", error);
        document.getElementById("vehicle-list").innerHTML =
            '<p class="text-red-500">Fahrzeuge konnten nicht geladen werden.</p>';
    }
}

/**
 * Lädt und zeigt die Fahrzeuge für das Management-Panel an.
 */
async function loadStationVehicles() {
    const slotsContainer = document
        .getElementById("vehicles-content")
        .querySelector(".grid");
    slotsContainer.innerHTML = "";
    document.getElementById("vehicle-count").innerText = "0";

    try {
        const dbResponse = await fetch(
            `/api/stations/${currentStationId}/vehicles`
        );
        if (!dbResponse.ok)
            throw new Error("Fehler beim Abrufen der Fahrzeugdaten.");

        const vehiclesFromDb = await dbResponse.json();
        document.getElementById("vehicle-count").innerText =
            vehiclesFromDb.length;

        const jsonResponse = await fetch("/data/vehicles_fire.json");
        const vehiclesJsonData = await jsonResponse.json();

        const vehicles = vehiclesFromDb.map((dbVehicle) => {
            const vehicleDetails = vehiclesJsonData.find(
                (v) => v.id === dbVehicle.vehicle_type
            );
            if (vehicleDetails) {
                return { ...dbVehicle, ...vehicleDetails };
            }
            return dbVehicle;
        });

        vehicles.forEach((vehicle) => {
            const vehicleElement = `
                <div class="vehicle-slot bg-gray-100 rounded-lg p-4 h-48 flex flex-col justify-center items-center relative">
                    <button class="delete-vehicle-button absolute top-2 right-2 text-red-500 hover:text-red-700" data-vehicle-id="${vehicle.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                    <img src="${vehicle.image}" alt="${vehicle.name}" class="w-24 h-auto mb-2 rounded-md">
                    <p class="text-sm font-semibold text-gray-800">${vehicle.name}</p>
                </div>
            `;
            slotsContainer.innerHTML += vehicleElement;
        });

        for (let i = vehicles.length; i < 4; i++) {
            const addSlotElement = `
                <div class="vehicle-slot bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 h-48 flex flex-col justify-center items-center cursor-pointer hover:bg-gray-200 transition-colors duration-200" data-slot-id="${
                    i + 1
                }">
                    <i class="fas fa-plus text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-semibold">Fahrzeug kaufen</p>
                </div>
            `;
            slotsContainer.innerHTML += addSlotElement;
        }

        document
            .querySelectorAll(".vehicle-slot[data-slot-id]")
            .forEach((slot) => {
                slot.addEventListener("click", () => {
                    showPanel("buy-vehicle-panel");
                    loadVehicles();
                });
            });

        document
            .querySelectorAll(".delete-vehicle-button")
            .forEach((button) => {
                button.addEventListener("click", async (event) => {
                    event.stopPropagation();
                    const vehicleId = event.currentTarget.dataset.vehicleId;

                    if (
                        confirm(
                            "Bist du sicher, dass du dieses Fahrzeug löschen möchtest?"
                        )
                    ) {
                        try {
                            const response = await fetch(
                                `/api/stations/${currentStationId}/vehicles/${vehicleId}`,
                                {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": csrfToken,
                                    },
                                }
                            );

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(
                                    errorData.error ||
                                        "Fehler beim Löschen des Fahrzeugs."
                                );
                            }

                            alert("Fahrzeug erfolgreich gelöscht!");
                            loadStationVehicles();
                        } catch (error) {
                            console.error("Lösch-Fehler:", error);
                            alert(error.message);
                        }
                    }
                });
            });
    } catch (error) {
        console.error("Fehler beim Laden der Wachen-Fahrzeuge:", error);
        slotsContainer.innerHTML =
            '<p class="text-red-500">Fahrzeuge konnten nicht geladen werden.</p>';
    }
}

/**
 * Lädt die Wachen und Fahrzeuge des Spielers und zeigt sie im Alarmierungs-Panel an.
 */
async function loadStationsForAlertPanel() {
    const stationList = document.getElementById("station-list");
    stationList.innerHTML = '<p class="text-gray-500">Lade Wachen...</p>';

    try {
        const dbResponse = await fetch("/api/player-stations-with-vehicles");
        if (!dbResponse.ok) {
            throw new Error(
                "Fehler beim Abrufen der Wachen- und Fahrzeugdaten."
            );
        }
        const stations = await dbResponse.json();

        const jsonResponse = await fetch("/data/vehicles_fire.json");
        const vehiclesJsonData = await jsonResponse.json();

        if (stations.length === 0) {
            stationList.innerHTML =
                '<p class="text-gray-500">Du besitzt noch keine Wachen, die alarmiert werden können.</p>';
            return;
        }

        stationList.innerHTML = "";

        for (const station of stations) {
            const stationElement = document.createElement("div");
            stationElement.className = "bg-gray-100 p-4 rounded-lg shadow-sm";

            let vehiclesWithDetails = station.vehicles.map((dbVehicle) => {
                const vehicleDetails = vehiclesJsonData.find(
                    (v) => v.id === dbVehicle.vehicle_type
                );
                return vehicleDetails
                    ? { ...dbVehicle, ...vehicleDetails }
                    : dbVehicle;
            });

            let vehiclesHtml = "";
            if (vehiclesWithDetails.length > 0) {
                const distancePromises = vehiclesWithDetails.map(
                    async (vehicle) => {
                        const startCoords = `${station.lon},${station.lat}`;
                        const endCoords = `${currentDispatch.lon},${currentDispatch.lat}`;
                        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${startCoords};${endCoords}?overview=false`;

                        try {
                            const res = await fetch(osrmUrl);
                            const data = await res.json();

                            if (data.routes && data.routes.length > 0) {
                                const distanceInMeters =
                                    data.routes[0].distance;
                                const distanceInKm = (
                                    distanceInMeters / 1000
                                ).toFixed(2);
                                return { vehicle, distance: distanceInKm };
                            } else {
                                return { vehicle, distance: "n/a" };
                            }
                        } catch (error) {
                            console.error(
                                "Fehler bei der OSRM-API-Anfrage:",
                                error
                            );
                            return { vehicle, distance: "Fehler" };
                        }
                    }
                );

                const results = await Promise.all(distancePromises);

                vehiclesHtml = results
                    .map(
                        (result) => `
                    <label class="flex items-center p-3 my-2 bg-white rounded-lg shadow-sm cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" class="form-checkbox text-blue-600 h-5 w-5 mr-3" value="${result.vehicle.id}">
                        <div>
                            <span class="font-semibold text-gray-800">${result.vehicle.name}</span>
                            <p class="text-xs text-gray-500">Entfernung: ${result.distance} km</p>
                        </div>
                    </label>
                `
                    )
                    .join("");
            } else {
                vehiclesHtml =
                    '<p class="text-gray-500 text-sm">Keine Fahrzeuge an dieser Wache.</p>';
            }

            stationElement.innerHTML = `
                <h3 class="font-bold text-lg mb-2 text-gray-900">${station.name}</h3>
                <div class="space-y-2">
                    ${vehiclesHtml}
                </div>
            `;
            stationList.appendChild(stationElement);
        }
    } catch (error) {
        console.error("Fehler:", error);
        stationList.innerHTML = `<p class="text-red-500">Fehler beim Laden der Daten: ${error.message}</p>`;
    }
}

//=======================================================================================================
// EINSATZ-FUNKTIONEN
//=======================================================================================================

/**
 * Funktion zum Laden der Einsatz-JSON-Daten.
 */
async function loadDispatchData() {
    try {
        const response = await fetch("/data/dispatches.json");
        if (!response.ok)
            throw new Error("Fehler beim Laden der Einsatzdaten.");
        dispatchesData = await response.json();
    } catch (error) {
        console.error("Fehler:", error);
    }
}

/**
 * Funktion zur Anzeige des Einsatz-Panels.
 * @param {object} dispatch - Das Einsatz-Objekt.
 */
function showDispatchPanel(dispatch) {
    currentDispatch = dispatch;

    const dispatchDetails = dispatchesData.find(
        (d) => d.id === dispatch.dispatch_type
    );

    if (dispatchDetails) {
        document.getElementById("dispatch-panel-name").innerText =
            dispatchDetails.name;
        document.getElementById("dispatch-details-description").innerText =
            dispatchDetails.description;
        document.getElementById("dispatch-details-reward").innerText =
            dispatch.reward;
    } else {
        document.getElementById("dispatch-panel-name").innerText =
            "Einsatzdetails";
        document.getElementById("dispatch-details-description").innerText =
            "Details zu diesem Einsatz konnten nicht geladen werden.";
    }

    document.getElementById("dispatch-panel-id").innerText = dispatch.id;

    showPanel("dispatch-panel");
}

/**
 * Ruft die Einsätze vom Server ab und zeigt sie auf der Karte an.
 */
async function loadDispatches() {
    try {
        const response = await fetch("/api/dispatches");
        if (!response.ok) {
            throw new Error("Fehler beim Abrufen der Einsätze.");
        }
        const dispatches = await response.json();

        // Vorherige Dispatch-Marker entfernen
        dispatchMarkers.forEach((marker) => map.removeLayer(marker));
        dispatchMarkers = [];

        // Neue Dispatch-Marker hinzufügen
        dispatches.forEach((dispatch) => {
            const marker = L.marker([dispatch.lat, dispatch.lon], {
                icon: dispatchIcon,
            }).addTo(map);

            marker.on("click", () => {
                showDispatchPanel(dispatch);
            });

            dispatchMarkers.push(marker);
        });
    } catch (error) {
        console.error("Fehler:", error);
    }
}

//=======================================================================================================
// EVENT-LISTENER
//=======================================================================================================

// Event-Listener für den "Einsatz generieren"-Button
document
    .getElementById("generate-dispatch-btn")
    .addEventListener("click", function () {
        fetch("/api/dispatches/generate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((err) => {
                        throw new Error(err.error);
                    });
                }
                return response.json();
            })
            .then((data) => {
                alert(data.message);
                loadDispatches();
            })
            .catch((error) => {
                alert("Fehler: " + error.message);
            });
    });

// Event-Listener für den "Fahrzeuge alarmieren"-Button im dispatch-panel
document.getElementById("alert-button").addEventListener("click", function () {
    if (currentDispatch) {
        document.getElementById("alert-dispatch-id").innerText =
            currentDispatch.id;
        showPanel("alert-panel");
        loadStationsForAlertPanel();
    } else {
        alert("Kein Einsatz ausgewählt.");
    }
});

// Event-Listener für den Schließen-Button des neuen Panels
document
    .getElementById("close-alert-panel-button")
    .addEventListener("click", () => showPanel(null));

// Event-Listener für Schließen-Buttons
document
    .getElementById("close-buy-panel-button")
    .addEventListener("click", () => showPanel(null));
document
    .getElementById("close-manage-panel-button")
    .addEventListener("click", () => showPanel(null));
document
    .getElementById("close-buy-vehicle-panel-button")
    .addEventListener("click", () => showPanel(null));
document
    .getElementById("close-dispatch-panel-button")
    .addEventListener("click", () => showPanel(null));

// Event-Listener für den neuen Zurück-Button
document
    .getElementById("back-to-manage-button")
    .addEventListener("click", function () {
        showPanel("manage-panel");
        document.querySelector('.tab-button[data-tab="vehicles"]').click();
    });

// Event-Listener für den Baumodus-Button
document
    .getElementById("build-mode-button")
    .addEventListener("click", function () {
        buildMode = !buildMode;
        if (buildMode) {
            this.innerHTML = '<i class="fas fa-times"></i>';
            loadStationsInView();
        } else {
            this.innerHTML = '<i class="fas fa-hammer"></i>';
            loadStationsInView();
        }
    });

// Event-Listener für den Kauf-Button
document
    .getElementById("buy-station-button")
    .addEventListener("click", async function () {
        const stationId = this.dataset.stationId;
        const url = `/stations/${stationId}/buy`;
        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                },
            });
            if (!response.ok) throw new Error("Fehler beim Kauf der Wache.");
            alert("Wache erfolgreich gekauft!");
            showPanel(null);
            loadStationsInView();
        } catch (error) {
            console.error("Kauf-Fehler:", error);
            alert("Es ist ein Fehler aufgetreten. Bitte versuche es erneut.");
        }
    });

// Event-Listener für den "Ausgewählte Fahrzeuge zum Einsatz schicken"-Button
document
    .getElementById("send-alert-button")
    .addEventListener("click", async function () {
        // Sammle alle ausgewählten Fahrzeug-IDs
        const selectedVehicleIds = Array.from(
            document.querySelectorAll(
                '#alert-panel input[type="checkbox"]:checked'
            )
        )
            .map((checkbox) => checkbox.value)
            .filter((id) => id); // Stellt sicher, dass nur gültige IDs gesammelt werden

        // Überprüfe, ob Fahrzeuge ausgewählt wurden
        if (selectedVehicleIds.length === 0) {
            alert("Bitte wähle mindestens ein Fahrzeug aus.");
            return;
        }

        // Stelle sicher, dass ein Einsatz ausgewählt ist
        if (!currentDispatch) {
            alert(
                "Kein Einsatz ausgewählt. Bitte wähle einen Einsatz aus, um Fahrzeuge zu alarmieren."
            );
            return;
        }

        // Sende die Daten an den Server
        try {
            const response = await fetch(
                `/api/dispatches/${currentDispatch.id}/alert-vehicles`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        vehicle_ids: selectedVehicleIds,
                    }),
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.error || "Fehler beim Alarmieren der Fahrzeuge."
                );
            }

            // Erfolg: Schließe das Panel und lade die Einsätze neu
            alert(data.message);
            showPanel(null);
            loadDispatches();
        } catch (error) {
            console.error("Alarmierungs-Fehler:", error);
            alert("Fehler: " + error.message);
        }
    });

//=======================================================================================================
// STARTFUNKTIONEN
//=======================================================================================================

// Starte Funktionen beim Laden der Seite
loadStationsInView();
loadDispatchData();
loadDispatches();
setInterval(loadDispatches, 10000);

// Credits initialisieren
try {
    const creditsEl = document.getElementById("credits-display");
    if (creditsEl && typeof userCredits !== "undefined") {
        creditsEl.innerText = Number(userCredits).toLocaleString("de-DE") + " €";
    }
} catch (e) {}
