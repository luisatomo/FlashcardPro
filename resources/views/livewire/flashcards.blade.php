<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $deck->name }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Flash message -->
                @if (session()->has('success'))
                    <div class="p-3 text-sm bg-green-100 text-green-700 mb-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex items-center mb-4 space-x-2">
                    @if(count($flashcards) > 0)
                        <a
                            href="{{ route('study-mode', $deck) }}"
                            wire:navigate
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-gray-700"
                        >
                            Study This Deck
                        </a>
                    @endif

                    <button
                        wire:click="showCreateForm"
                        class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700"
                    >
                        + Add Card
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

                @include('livewire.forms.flashcard-form')

                @include('livewire.lists.flashcard-list')
            </div>
        </div>
    </div>
</div>
