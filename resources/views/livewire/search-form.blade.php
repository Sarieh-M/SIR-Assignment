<?php

use App\Models\Question;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Illuminate\View\View;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = ''; // State variable for search query
    public $algorithm = 'boolean'; // State variable for selected algorithm

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * The computed property for questions based on the selected algorithm.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQuestionsProperty()
    {
        $page = request()->get('page', 1);
        $perPage = 10;

        Log::info('Search query:', ['search' => $this->search]);
        Log::info('Selected algorithm:', ['algorithm' => $this->algorithm]);

        if (empty($this->search)) {
            Log::info('No search query provided, fetching all questions.');
            return Question::whereRaw('0 = 1')->paginate($perPage);
        }
        
        $highlightResults = function ($questions, $search) {
            $searchTerms = array_filter(explode(' ', trim($search)));
            $escapedTerms = array_map(function ($term) {
                return preg_quote($term, '/');
            }, $searchTerms);
            $highlightPattern = '/(' . implode('|', $escapedTerms) . ')/i'; 
            foreach ($questions as $question) {
                $question->question = preg_replace($highlightPattern, '<span class="bg-yellow-200 font-bold">$1</span>', $question->question);
                $question->answer = preg_replace($highlightPattern, '<span class="bg-yellow-200 font-bold">$1</span>', $question->answer);
            }
            return $questions;
        };

        // Define search functions
        $booleanSearch = function (string $search) use ($perPage, $highlightResults) {
            Log::info('Executing Boolean Search', ['search' => trim($search)]);
            $questions = Question::where(function ($query) use ($search) {
                $query->where('question', 'like', '%' . $search . '%')->orWhere('answer', 'like', '%' . $search . '%');
            })->paginate($perPage);

            return $highlightResults($questions, $search);
        };

        $extendedBooleanSearch = function (string $search) use ($perPage, $highlightResults) {
            $terms = array_filter(explode(' ', trim($search)));
            Log::info('Executing Extended Boolean Search', ['terms' => $terms]);
            $questions = Question::where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhere('question', 'like', '%' . $term . '%')->orWhere('answer', 'like', '%' . $term . '%');
                }
            })->paginate($perPage);

            return $highlightResults($questions, $search);
        };

        $vectorModelSearch = function (string $search) use ($perPage, $highlightResults) {
            $lowerSearch = strtolower(trim($search));
            Log::info('Executing Vector Search', ['search' => $lowerSearch]);

            $questions = Question::selectRaw(
                "*, ((LENGTH(LOWER(question)) - LENGTH(REPLACE(LOWER(question), ?, ''))) + 
                (LENGTH(LOWER(answer)) - LENGTH(REPLACE(LOWER(answer), ?, '')))) AS relevance",
                [$lowerSearch, $lowerSearch],
            )
                ->having('relevance', '>', 0) // Filter rows with no relevance
                ->orderByDesc('relevance')
                ->paginate($perPage);

            return $highlightResults($questions, $search);
        };

        // Call the appropriate search function
        switch ($this->algorithm) {
            case 'extended':
                Log::info('Using Extended Boolean Model.');
                return $extendedBooleanSearch($this->search);
            case 'vector':
                Log::info('Using Vector Model.');
                return $vectorModelSearch($this->search);
            case 'boolean':
            default:
                Log::info('Using Boolean Model.');
                return $booleanSearch($this->search);
        }
    }
}; ?>

<div class="min-h-screen rounded-lg bg-cyan-50 p-6 pt-10 shadow-2xl">
    <!-- Algorithm Selection -->
    <div class="mb-6 rounded-lg bg-white p-4 shadow-lg">
        <h2 class="mb-3 text-lg font-semibold">Select Search Algorithm</h2>
        <div class="flex gap-6">
            <label class="flex cursor-pointer items-center gap-2">
                <input
                    class="focus:ring-stale-800"
                    type="radio"
                    value="boolean"
                    wire:model.live="algorithm"
                >
                <span class="transition">Boolean Model</span>
            </label>
            <label class="flex cursor-pointer items-center gap-2">
                <input
                    class="focus:ring-stale-800"
                    type="radio"
                    value="extended"
                    wire:model.live="algorithm"
                >
                <span class="transition">Extended Boolean Model</span>
            </label>
            <label class="flex cursor-pointer items-center gap-2">
                <input
                    class="focus:ring-stale-800"
                    type="radio"
                    value="vector"
                    wire:model.live="algorithm"
                >
                <span class="transition">Vector Model</span>
            </label>
        </div>
    </div>

    <!-- Search Input -->
    <div class="mb-6">
        <input
            class="focus:ring-stale-800 w-full rounded-lg border border-cyan-500 p-3 shadow-lg transition focus:outline-none focus:ring-1"
            type="text"
            wire:model.live="search"
            placeholder="Search questions (Arabic or English)..."
        >
    </div>

    <!-- Results -->
    <div>
        <div class="rounded-lg bg-white p-4 shadow-lg">
            <h1 class="mb-4 text-xl font-bold">Search Results:</h1>
            <ul class="space-y-3">
                @if ($this->questions && $this->questions->isNotEmpty())
                    @foreach ($this->questions as $question)
                        <li
                            class="h-auto rounded-md border border-cyan-500 bg-cyan-50 p-3 shadow-md transition hover:scale-[100.5%] hover:bg-cyan-50 hover:shadow-lg"
                            wire:key="{{ $question->id }}"
                        >
                            <p class="font-semibold">Question:</p>
                            <p class="">{!! $question->question !!}</p>
                            <p class="font-semibold">Answer:</p>
                            <p class="h-auto">
                                {!! $question->answer !!}
                            </p>

                        </li>
                    @endforeach
                @else
                    <li class="">No results found.</li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-white text-black">
        @if ($this->questions && $this->questions->isNotEmpty() && $this->questions->hasPages())
            <div class="rounded-lg p-3 shadow-lg">
                {{ $this->questions->links() }}
            </div>
        @endif
    </div>
</div>
