<?php

namespace App\Services\EcoLearn;

use App\EcoLearn\Models\Quiz;
use App\EcoLearn\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Contracts\EcoLearn\QuizServiceInterface;
use App\Models\QuizQuestion as ModelsQuizQuestion;

class QuizService implements QuizServiceInterface
{
    /**
     * Find quiz by id
     *
     * @param integer $id
     * @return Quiz|null
     */
    public function find(int $id): ?Quiz
    {
        $quiz = DB::table('quizzes')
                    ->where('quiz_id', $id)
                    ->first();

        if($quiz) {
            $creationDate   = to_datetime($quiz->created_at);

            $quizzes = new Quiz();
            $quizzes->id            = $quiz->quiz_id;
            $quizzes->category      = $quiz->category_id;
            $quizzes->creationDate  = $creationDate;

            return $quizzes;
        }
        return null;
    }

    /**
     * Add new quizz by Admin
     *
     * @param string $title
     * @param string $description
     * @return string
     */
    public function create(User $user, int $category): ?int
    {
        $now = Carbon::now();
        
        DB::beginTransaction();
        try {
            $categoryExists = DB::table('categories')
                                ->where('category_id', $category)
                                ->exists();
            
            if (!$categoryExists) {
                DB::rollBack();
                return ERROR_CATEGORY_NOT_FOUND;
            }

            $quizExists = DB::table('quizzes')
                            ->where('category_id', $category)
                            ->exists();
            
            if ($quizExists) {
                DB::rollBack();
                return ERROR_QUIZ_EXISTS_FOR_CATEGORY;
            }

            $quizId = DB::table('quizzes')
                        ->insertGetId([
                            'category_id'   => $category,
                            'created_at'    => $now,
                        ]);

            $quizz = $this->find($quizId);
            if($quizz) {
                DB::commit();
                return SUCCESS_QUIZZ_CREATED;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return ERROR_QUIZZ_CREATED;
        }
        return null;
    }

    /**
     * Question quizz
     *
     * @param integer $quiz
     * @param string $text
     * @param integer $resource
     * @return ModelsQuizQuestion|integer
     */
    public function questionQuiz(int $quizId, int $resourceId, string $text): ModelsQuizQuestion|int|null
    {
        $quizExists = DB::table('quizzes')
                        ->where('quiz_id', $quizId)
                        ->first();

        if($quizExists) {
            $questionExists = DB::table('quizQuestions')
                                ->where('question_text', $text)
                                ->exists();
            
            if($questionExists) {
                return false;
            }
            
            $quizQuestion = ModelsQuizQuestion::create([
                'quiz_id'           => $quizId,
                'ressource_id'      => $resourceId,
                'question_text'     => $text
            ]);
            $quizQuestion->save();
            return $quizQuestion;
        }
        return null;
    }

}