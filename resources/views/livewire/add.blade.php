<?php

use Livewire\Volt\Component;
use App\Models\Question;

new class extends Component {
    public $question = '';
    public $answer = '';

    protected $rules = [
        'question' => 'required|string|max:255',
        'answer' => 'required|string|max:1000',
    ];

    public function saveQuestion()
    {
        $this->validate();

        Question::create([
            'question' => $this->question,
            'answer' => $this->answer,
        ]);

        session()->flash('success', 'Question added successfully!');
        $this->reset(['question', 'answer']);
    }

}; ?>
<div class="rounded-lg p-6 shadow-lg bg-white mx-10 my-10">
    <h1 class="mb-6 text-2xl font-bold">Add New Question</h1>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form
        class="space-y-4"
        wire:submit.prevent="saveQuestion"
    >
        <div>
            <label
                class="block font-semibold"
                for="question"
            >Question:</label>
            <input
                class="w-full rounded-lg border border-cyan-500 p-3 shadow-md transition focus:outline-none focus:ring-1 focus:ring-cyan-800"
                id="question"
                type="text"
                wire:model.defer="question"
            >
            @error('question')
                <span class="text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label
                class="block font-semibold"
                for="answer"
            >Answer:</label>
            <textarea
                class="w-full rounded-lg border border-cyan-500 p-3 shadow-md transition focus:outline-none focus:ring-1 focus:ring-cyan-800"
                id="answer"
                wire:model.defer="answer"
            ></textarea>
            @error('answer')
                <span class="text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <button
                class="rounded-lg bg-cyan-600 px-4 py-2 font-semibold text-white shadow transition hover:bg-slate-800"
                type="submit"
            >
                Save Question
            </button>
        </div>
    </form>
</div>
