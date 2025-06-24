<x-slot name="header">
    <a
        href="{{ route('flashcards', $deck) }}"
        wire:navigate
        class="float-right bg-transparent text-gray-800 px-4 py-2 rounded text-xl"
    >
        X
    </a>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">

        {{ $deck->name }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <div x-data="{ current: {{ $current }}, total: {{ count($flashcards) }} }" class="w-full max-w-xl mx-auto">
                    <!-- Progress Bar -->
                    <div class="w-full mb-4 text-center text-lg font-bold">
                        @if ($current < count($flashcards))
                            {{ $current + 1 }}/{{ count($flashcards) }} cards
                        @endif
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded mb-4">
                        <div
                            class="h-full bg-blue-500 rounded transition-all duration-300"
                            :style="{ width: ((current + 1) / total * 100) + '%' }"
                        ></div>
                    </div>

                    <!-- Flashcards -->
                    <div class="relative overflow-hidden h-48 border rounded bg-white shadow mb-4">
                        @foreach ($flashcards as $flashcard)
                            <template x-if="{{ $current }} === {{ $loop->index }}">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div>
                                        <h3 class="text-xl mb-4">{{ $flashcard->question }}</h3>
                                        <div class="flex items-center mb-4 space-x-2">
                                            @if(!$displayedAnswer)
                                            <button
                                                wire:click="showAnswer"
                                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-gray-700 font-bold"
                                            >
                                                Reveal Answer
                                            </button>
                                            @else
                                                <p>
                                                    {{ $flashcard->answer }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </template>
                        @endforeach
                        @if ($current === count($flashcards))
                            <template x-if="{{ $current }} === {{ count($flashcards) }}">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <h3 class="text-xl mb-4">You got {{ $score }} out of {{ count($flashcards) }} correct.</h3>
                                        <a
                                            href="{{ route('flashcards', $deck) }}"
                                            wire:navigate
                                            class=" bg-gray-800 text-white px-4 py-2 rounded text-xl"
                                        >
                                            X Close
                                        </a>
                                    </div>
                                </div>
                            </template>
                        @endif
                    </div>

                    <!-- Buttons -->
                    @if ($displayedAnswer)
                    <div class="text-center w-full">
                        <button
                            wire:click="incrementScore(true)"
                            class="text-nowrap px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400 w-64 mb-3 text-center"
                        >
                            <svg class="w-4 h-4 inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z"></path>
                            </svg> I got it right!
                        </button><br>
                        <button
                            wire:click="incrementScore(false)"
                            class="text-nowrap px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-800 hover:text-white disabled:bg-gray-400 w-64 text-center"
                        >
                            <svg class="w-4 h-4 inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 15h2.25m8.024-9.75c.011.05.028.1.052.148.591 1.2.924 2.55.924 3.977a8.96 8.96 0 01-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398C20.613 14.547 19.833 15 19 15h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 00.303-.54m.023-8.25H16.48a4.5 4.5 0 01-1.423-.23l-3.114-1.04a4.5 4.5 0 00-1.423-.23H6.504c-.618 0-1.217.247-1.605.729A11.95 11.95 0 002.25 12c0 .434.023.863.068 1.285C2.427 14.306 3.346 15 4.372 15h3.126c.618 0 .991.724.725 1.282A7.471 7.471 0 007.5 19.5a2.25 2.25 0 002.25 2.25.75.75 0 00.75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 002.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384"></path>
                            </svg> Maybe next time...
                        </button>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
