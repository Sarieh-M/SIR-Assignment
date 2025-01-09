<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the questions table
        DB::table('questions')->truncate();

        // First JSON File
        $file1 = storage_path('app/data/topics_with_faqs_english.json');
        if (file_exists($file1)) {
            $data = json_decode(file_get_contents($file1), true);

            foreach ($data as $topic => $faqs) {
                foreach ($faqs as $faq) {
                    $cleanQuestion = strip_tags($faq['question']);
                    $cleanAnswer = strip_tags($faq['answer']);

                    Question::create([
                        'question' => $cleanQuestion,
                        'answer' => $cleanAnswer,
                    ]);
                }
            }
        }

        // Second JSON File
        $file2 = storage_path('app/data/questions-arabic.json');
        if (file_exists($file2)) {
            $data = json_decode(file_get_contents($file2), true);

            foreach ($data as $item) {
                $cleanQuestion = strip_tags($item['question']);
                $cleanAnswer = strip_tags($item['answer']);

                Question::create([
                    'question' => $cleanQuestion,
                    'answer' => $cleanAnswer,
                ]);
            }
        }
    }
}

