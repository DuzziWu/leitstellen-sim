<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meine Wachen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg">Wachen-Übersicht</h3>
                        <a href="{{ route('buildings.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                            Neue Wache bauen
                        </a>
                    </div>

                    @if ($buildings->isEmpty())
                        <p class="text-gray-500">Du hast noch keine Wachen.</p>
                    @else
                        <ul>
                            @foreach ($buildings as $building)
                                <li class="p-2 border-b">
                                    <a href="{{ route('buildings.show', $building) }}" class="block p-2 border-b hover:bg-gray-100 transition-colors duration-200">
                                        {{ $building->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>