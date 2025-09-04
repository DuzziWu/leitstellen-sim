<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stadt auswählen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Bitte gib den Namen deiner Startstadt ein.</p>
                    <form method="POST" action="{{ route('user.save_city_name') }}">
                        @csrf
                        <div class="mt-4">
                            <x-input-label for="city_name" :value="__('Stadtname')" />
                            <x-text-input id="city_name" class="block mt-1 w-full" type="text" name="city_name" required autofocus />
                            <x-input-error :messages="$errors->get('city_name')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Stadt speichern') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>