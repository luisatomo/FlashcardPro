<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Decks') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="divide-y">
                    @forelse ($this->decks as $deck)
                        <div class="py-3 flex justify-between items-center">
                            <div>
                                <div class="font-semibold">{{ $deck->name }}</div>
                                <div class="text-sm text-gray-500">{{ $deck->cards()->count() }} cards</div>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $deck->public ? 'Public' : 'Private' }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No decks yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
