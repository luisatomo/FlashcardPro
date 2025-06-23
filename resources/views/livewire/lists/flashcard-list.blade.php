@if(count($flashcards) > 0)
    <div class="my-3 space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">
        @foreach ($flashcards as $flashcard)
            <div class="py-3 flex justify-between items-center">
                <div>
                    <div class="font-semibold">
                        {{ $flashcard->answer }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $flashcard->question }}
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    {{ $flashcard->public ? 'Public' : 'Private' }}
                </div>
                <div>
                    <button
                        wire:click="showEditForm({{ $flashcard->id }})"
                        class="text-gray-500 hover:underline text-sm"
                    >
                        Edit
                    </button>
                    <button
                        wire:click="deleteFlashcard({{ $flashcard->id }})"
                        onclick="return confirm('Are you sure you want to delete this flashcard?')"
                        class="ml-3 text-red-600 hover:underline text-sm"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="my-3 space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">No flashcards yet.</div>
@endif
