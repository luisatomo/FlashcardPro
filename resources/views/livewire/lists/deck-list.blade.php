@if(count($decks) > 0)
    <div class="my-3 space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">
        @foreach ($decks as $deck)
            <div class="py-3 flex justify-between items-center">
                <div>
                    <div class="font-semibold">
                        <a
                            href="{{ route('flashcards', $deck) }}" :active="request()->routeIs('decks')" wire:navigate
                        >
                            {{ $deck->name }}
                        </a>
                    </div>
                    <div class="text-sm text-gray-500">
                        <a
                            href="{{ route('flashcards', $deck) }}" :active="request()->routeIs('decks')" wire:navigate
                        >
                            {{ $deck->flashcards()->count() }} cards
                        </a>
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    {{ $deck->public ? 'Public' : 'Private' }}
                </div>
                <div>
                    <button
                        wire:click="showEditForm({{ $deck->id }})"
                        class="text-gray-500 hover:underline text-sm"
                    >
                        Edit
                    </button>
                    <button
                        wire:click="deleteDeck({{ $deck->id }})"
                        onclick="return confirm('Are you sure you want to delete this deck?')"
                        class="ml-3 text-red-600 hover:underline text-sm"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="my-3 space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">No decks yet.</div>
@endif
