@extends('layouts.admin')

@section('content')
    <section class="space-y-6">
        <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-100">Campagnes Marketing</h1>
                <p class="text-sm text-neutral-400">Gérez vos campagnes promotionnelles.</p>
            </div>
            <a href="{{ route('admin.marketing_campaigns.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-600 text-sm font-semibold text-white hover:bg-sky-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nouvelle Campagne
            </a>
        </header>

        @if(session('status'))
            <div class="p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-neutral-800 bg-neutral-900 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-neutral-400">
                    <thead class="bg-neutral-950/50 text-neutral-200 uppercase tracking-wider text-xs font-semibold">
                    <tr>
                        <th class="p-4">Nom</th>
                        <th class="p-4">Début</th>
                        <th class="p-4">Fin</th>
                        <th class="p-4 text-center">Actif</th>
                        <th class="p-4 text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-neutral-800/50 transition">
                            <td class="p-4 font-medium text-neutral-200">{{ $campaign->name }}</td>
                            <td class="p-4">{{ $campaign->start_date?->format('d/m/Y') }}</td>
                            <td class="p-4">{{ $campaign->end_date?->format('d/m/Y') }}</td>
                            <td class="p-4 text-center">
                                @if($campaign->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-400/10 text-emerald-400">Oui</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-neutral-400/10 text-neutral-400">Non</span>
                                @endif
                            </td>
                            <td class="p-4 text-end">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.marketing_campaigns.edit', $campaign->id) }}" class="p-2 rounded-lg text-neutral-400 hover:text-white hover:bg-neutral-800">
                                        Modifier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-neutral-500">
                                Aucune campagne trouvée.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($campaigns->hasPages())
                <div class="p-4 border-t border-neutral-800">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
