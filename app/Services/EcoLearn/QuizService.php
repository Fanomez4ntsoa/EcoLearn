<?php

namespace App\Services\EcoLearn;

use App\EcoLearn\Models\Quiz;
use App\EcoLearn\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Contracts\EcoLearn\QuizServiceInterface;
use App\EcoLearn\Models\QuizAnswer;
use App\EcoLearn\Models\QuizQuestion;
use App\Models\QuizQuestion as ModelsQuizQuestion;
use App\Models\QuizAnswer as ModelsQuizAnswer;
use Illuminate\Support\Facades\Log;

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
     * Find question by id
     *
     * @param integer $id
     * @return QuizQuestion|null
     */
    public function findQuestion(int $id): ?QuizQuestion
    {
        $question = DB::table('quizQuestions')
                        ->where('question_id', $id)
                        ->first();

        if($question) {
            $questions = new QuizQuestion();
            $questions->id            = $question->question_id;
            $questions->quiz_id       = $question->quiz_id;
            $questions->resource_id   = $question->ressource_id;
            $questions->question_text = $question->question_text;

            return $questions;
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
     * Question Quiz
     *
     * @param integer $quizId
     * @param integer $resourceId
     * @param string $text
     * @param array $answerPossibilities
     * @param string $correctOption
     * @return ModelsQuizQuestion|integer|null
     */
    public function questionQuiz(int $quizId, int $resourceId, string $text, array $answerPossibilities, string $correctOption): ModelsQuizQuestion|int|null
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
                'quiz_id'                   => $quizId,
                'ressource_id'              => $resourceId,
                'question_text'             => $text,
                'answer_possibilities'      => $answerPossibilities,
                'correct_option'            => $correctOption
            ]);
            $quizQuestion->save();
            return $quizQuestion;
        }
        return null;
    }

    /**
     * Quiz answer
     *
     * @param User $user
     * @param Quiz $quiz
     * @param QuizQuestion $question
     * @param string $selectedOption
     * @return ModelsQuizAnswer|QuizAnswer|integer|null
     */
    public function answerQuiz(User $user, Quiz $quiz, QuizQuestion $question, string $selectedOption) : ModelsQuizAnswer|QuizAnswer|int|null
    {
        DB::beginTransaction();
        try {
            // Vérification si l'utilisateur a déjà répondu à cette question du quizz
            $existingAnswer = DB::table('quizAnswers')
                                ->where('user_id', $user->id)
                                ->where('quiz_id', $quiz->id)
                                ->where('question_id', $question->id)
                                ->first();

            

            // Récupérer la question du quiz
            $questionId = DB::table('quizQuestions')
                            ->where('question_id', $question->id)
                            ->first();

            if(!$questionId) {
                return ERROR_USER_ANSWER;
            }

            // Vérifier si l'option sélectionner fait partie des réponses disponibles
            if(!isset(json_decode($questionId->answer_possibilities, true)[$selectedOption])) {
                // L'option sélectionnée n'est pas valide pour cette question
                return ERROR_QUIZ_ANSWER_OPTION;
            }
            // dd($questionId->correct_option);
            if($existingAnswer) {
                // Mettre à jour la réponse existante
                DB::table('quizAnswers')
                    ->where('answer_id', $existingAnswer->answer_id)
                    ->update([
                        'chosen_option' => $selectedOption,
                        'is_correct'    => ($selectedOption == $questionId->correct_option) 
                    ]);
                DB::commit();
                return SUCCESS_USER_UPDATE_ANSWER;

            } else {
                // Créer une nouvelle réponse pour l'utilisateur
                $answer = ModelsQuizAnswer::create([
                    'user_id'       => $user->id,
                    'quiz_id'       => $quiz->id,
                    'question_id'   => $question->id,
                    'chosen_option' => $selectedOption,
                    'is_correct'    => $selectedOption == $question->correct_option
                ]);
                $answer->save();
            }

            DB::commit();
            return SUCCESS_USER_ANSWER;

        } catch (\Throwable $th) {
            dd($th->getMessage());
            Log::error($th->getMessage(), [$th]);
        }
        return null;
    }

    /**
     * Delete question Quiz
     *
     * @param QuizQuestion $quizQuestion
     * @return boolean
     */
    public function deleteQuestion(QuizQuestion $quizQuestion): ?bool
    {
        DB::beginTransaction();
        try {
            $answer = DB::table('quizAnswers')
                        ->where('question_id', $quizQuestion->id)
                        ->exists();
            
            if($answer) {
                DB::table('quizAnswers')
                    ->where('question_id', $quizQuestion->id)
                    ->delete();
            }

            $question = DB::table('quizQuestions')
                            ->where('question_id', $quizQuestion->id)
                            ->delete();
            if($question) {
                DB::commit();
                return true;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [$th]);
            return false;
        }

        return null;
    }
}