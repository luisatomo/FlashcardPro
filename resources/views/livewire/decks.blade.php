<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('My Decks') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Flash message -->
                @if (session()->has('success'))
                    <div class="p-3 text-sm bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex items-center mb-4 space-x-2">
                    <button
                        wire:click="showCreateForm"
                        class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700"
                    >
                        + Add Deck
                    </button>

                    @if ($showForm)
                        <button
                            wire:click="$set('showForm', false)"
                            class="ml-3 px-4 py-2 text-gray-600 hover:underline text-sm"
                        >
                            Cancel
                        </button>
                    @endif
                </div>

                @if ($showForm)
                    <form wire:submit.prevent="saveDeck" class="space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">

                        <div class="py-3">
                            <label for="deck-name" class="block text-sm font-medium text-gray-700">
                                Deck Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="deck-name"
                                wire:model.defer="name"
                                type="text"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="e.g. Spanish"
                            >
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center py-3">
                            <input
                                type="checkbox"
                                id="deck-public"
                                wire:model.defer="public"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                            >
                            <label for="deck-public" class="ml-3 block text-sm text-gray-700">
                                Make this deck public
                            </label>
                        </div>

                        <div class="flex justify-end py-3">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-white hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                {{ $editingId ? 'Update Deck' : '+ Create Deck' }}
                            </button>
                        </div>

                    </form>
                @endif

                @if(count($decks) > 0)
                    <div class="my-3 space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">
                        @foreach ($decks as $deck)
                            <div class="py-3 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold">{{ $deck->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $deck->flashcards()->count() }} cards</div>
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
            </div>
        </div>
    </div>
</div>
