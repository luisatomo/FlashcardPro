@if ($showForm)
    <form wire:submit.prevent="saveFlashcard" class="space-y-4 p-4 bg-gray-50 border border-gray-200 rounded-md shadow-sm">

        <div class="py-3">
            <label for="flashcard-question" class="block text-sm font-medium text-gray-700">
                Question <span class="text-red-500">*</span>
            </label>
            <input
                id="flashcard-question"
                wire:model.defer="question"
                type="text"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g. Question?"
            >
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3">
            <label for="flashcard-answer" class="block text-sm font-medium text-gray-700">
                Answer <span class="text-red-500">*</span>
            </label>
            <input
                id="flashcard-answer"
                wire:model.defer="answer"
                type="text"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g. Answer"
            >
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center py-3">
            <input
                type="checkbox"
                id="flashcard-public"
                wire:model.defer="public"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
            >
            <label for="flashcard-public" class="ml-3 block text-sm text-gray-700">
                Make this flashcard public
            </label>
        </div>

        <div class="flex justify-end py-3">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-white hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                {{ $editingId ? 'Update Flashcard' : '+ Create Flashcard' }}
            </button>
        </div>

    </form>
@endif
