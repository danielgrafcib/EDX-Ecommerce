<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord Entreprise') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Mes Entreprises</h3>

                    @if($enterprises->isEmpty())
                        <p class="text-gray-500">Vous n'êtes associé à aucune entreprise.</p>
                        <a href="#" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Créer une entreprise</a>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($enterprises as $enterprise)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-xl">{{ $enterprise->name }}</h4>
                                        <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ $enterprise->pivot->role }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($enterprise->description, 100) }}</p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-sm font-semibold">Solde: {{ $enterprise->wallet ? number_format($enterprise->wallet->balance, 0) : '0' }} XOF</span>
                                        <a href="{{ route('enterprise.manage', $enterprise->id) }}" class="text-blue-600 hover:underline text-sm">Gérer</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
