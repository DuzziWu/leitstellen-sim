<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $building->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold">Fahrzeuge in dieser Wache:</h3>
                        @if ($building->vehicles->isEmpty())
                            <p>In dieser Wache gibt es noch keine Fahrzeuge.</p>
                        @else
                            <ul>
                                @foreach ($building->vehicles as $vehicle)
                                    <li>{{ $vehicle->vehicleType->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold mt-8">Fahrzeug kaufen:</h3>
                    <form action="{{ route('buildings.purchase-vehicle', $building) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label for="vehicle_type_id" class="block text-sm font-medium text-gray-700">Fahrzeugtyp</label>
                            <select name="vehicle_type_id" id="vehicle_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                @foreach ($vehicleTypes as $vehicleType)
                                    <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }} ({{ number_format($vehicleType->cost) }} €)</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                            Kaufen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>