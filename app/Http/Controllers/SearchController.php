<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // Validate the input
        $request->validate([
            'query' => 'required|string',
            'algorithm' => 'required|string|in:boolean,extended_boolean,vector',
        ]);

        $query = $request->input('query');
        $algorithm = $request->input('algorithm');

        // Fetch all questions
        $questions = Question::all();

        // Call the appropriate algorithm
        $results = match ($algorithm) {
            'boolean' => $this->booleanSearch($query, $questions),
            'extended_boolean' => $this->extendedBooleanSearch($query, $questions),
            'vector' => $this->vectorSearch($query, $questions),
        };

        // Return the results
        return view('search.results', compact('results', 'query', 'algorithm'));
    }

    private function booleanSearch($query, $questions)
    {
        $queryWords = explode(' ', strtolower($query));
        return $questions->filter(function ($question) use ($queryWords) {
            $questionWords = explode(' ', strtolower($question->question));
            return !empty(array_intersect($queryWords, $questionWords));
        });
    }

    private function extendedBooleanSearch($query, $questions)
    {
        $queryWords = explode(' ', strtolower($query));
        return $questions->map(function ($question) use ($queryWords) {
            $questionWords = explode(' ', strtolower($question->question));
            $score = 0;
            foreach ($queryWords as $word) {
                if (in_array($word, $questionWords)) {
                    $score++;
                }
            }
            $question->score = $score; // Adding a dynamic score
            return $question;
        })->sortByDesc('score');
    }

    private function vectorSearch($query, $questions)
    {
        $queryVector = $this->getVector($query);
        return $questions->map(function ($question) use ($queryVector) {
            $questionVector = $this->getVector($question->question);
            $similarity = $this->cosineSimilarity($queryVector, $questionVector);
            $question->similarity = $similarity;
            return $question;
        })->sortByDesc('similarity');
    }

    private function getVector($text)
    {
        // Simple vectorization based on word frequencies
        $words = array_count_values(explode(' ', strtolower($text)));
        return $words;
    }

    private function cosineSimilarity($vectorA, $vectorB)
    {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;
        $allWords = array_unique(array_merge(array_keys($vectorA), array_keys($vectorB)));
        foreach ($allWords as $word) {
            $valueA = $vectorA[$word] ?? 0;
            $valueB = $vectorB[$word] ?? 0;
            $dotProduct += $valueA * $valueB;
            $magnitudeA += $valueA ** 2;
            $magnitudeB += $valueB ** 2;
        }

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0; // Avoid division by zero
        }

        return $dotProduct / (sqrt($magnitudeA) * sqrt($magnitudeB));
    }
}
