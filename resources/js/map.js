const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

let currentStationId = null;
let dispatchesData = null; // Variable für die Einsatz-JSON-Daten
let currentDispatch = null; // Variable für den aktuell ausgewählten Einsatz

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

var buildMode = false;
var stationMarkers = [];

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

// Neue Funktion zur Verwaltung der Panels
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

// Funktion zur Anzeige des richtigen Panels
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

// Funktion zum Laden der Einsatz-JSON-Daten
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

// Funktion zur Anzeige des Einsatz-Panels
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

// Funktion zum Laden der kaufbaren Fahrzeuge
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
                                <h4 class="text-lg font-bold">${
                                    vehicle.name
                                }</h4>
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
                const url = `/stations/${currentStationId}/buy-vehicle`;

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

                    // Schließt das Kauf-Panel und navigiert zurück zum Verwaltungs-Panel
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

// Funktion zum Laden und Anzeigen der Fahrzeuge für das Management-Panel
async function loadStationVehicles() {
    const slotsContainer = document
        .getElementById("vehicles-content")
        .querySelector(".grid");
    slotsContainer.innerHTML = "";
    document.getElementById("vehicle-count").innerText = "0";

    try {
        const response = await fetch(
            `/api/stations/${currentStationId}/vehicles`
        );
        if (!response.ok)
            throw new Error("Fehler beim Abrufen der Fahrzeugdaten.");

        const vehicles = await response.json();
        document.getElementById("vehicle-count").innerText = vehicles.length;

        // Slots für jedes vorhandene Fahrzeug rendern
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

        // "Fahrzeug kaufen"-Slots für die restlichen Plätze rendern
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

        // Event-Listener für die neuen "Fahrzeug kaufen"-Slots
        document
            .querySelectorAll(".vehicle-slot[data-slot-id]")
            .forEach((slot) => {
                slot.addEventListener("click", () => {
                    showPanel("buy-vehicle-panel");
                    loadVehicles();
                });
            });

        // NEUER EVENT-LISTENER FÜR DEN LÖSCH-BUTTON
        document
            .querySelectorAll(".delete-vehicle-button")
            .forEach((button) => {
                button.addEventListener("click", async (event) => {
                    event.stopPropagation(); // Verhindert, dass der Klick auf den Slot übergeht
                    const vehicleId = event.currentTarget.dataset.vehicleId;

                    if (
                        confirm(
                            "Bist du sicher, dass du dieses Fahrzeug löschen möchtest?"
                        )
                    ) {
                        try {
                            const response = await fetch(
                                `/stations/${currentStationId}/vehicles/${vehicleId}`,
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
                            loadStationVehicles(); // Lade die Stellplätze neu
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

// Event-Listener für den neuen "Einsatz generieren"-Button
const generateDispatchBtn = document.getElementById("generate-dispatch-btn");
if (generateDispatchBtn) {
    generateDispatchBtn.addEventListener("click", function () {
        fetch("/dispatches/generate", {
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
                // Hier wird später die Logik zum Anzeigen des neuen Einsatzes hinzugefügt
            })
            .catch((error) => {
                alert("Fehler: " + error.message);
            });
    });
}

function showStations(stationsData) {
    stationMarkers.forEach((marker) => map.removeLayer(marker));
    stationMarkers = [];
    if (stationsData.length === 0) return;
    stationsData.forEach((station) => {
        let marker;
        if (station.user_id === userId) {
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

map.on("moveend", loadStationsInView);

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

loadStationsInView();

var dispatchMarkers = [];

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

// Starte Funktionen beim Laden der Seite
loadStationsInView();
loadDispatchData(); // Lädt die statischen Einsatz-Daten
loadDispatches();
setInterval(loadDispatches, 10000); // Lädt Einsätze alle 10 Sekunden neu
