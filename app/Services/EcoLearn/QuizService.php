<?php

namespace App\Services\EcoLearn;

use App\Contracts\EcoLearn\QuizServiceInterface;
use App\EcoLearn\Models\Quiz;
use App\EcoLearn\Models\User;
use App\Models\Quiz as ModelsQuiz;
use App\Models\QuizQuestion;
use App\Models\Scopes\UnexpiredScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
                    ->where(function($query) {
                        (new UnexpiredScope())->applyToBuilder($query, 'quizzes');
                    })
                    ->first();

        if($quiz) {
            $creationDate   = to_datetime($quiz->created_at);

            $quizzes = new Quiz();
            $quizzes->id            = $quiz->quiz_id;
            $quizzes->title         = $quiz->title;
            $quizzes->description   = $quiz->description;
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
            // Vérifier si la catégorie existe dans la table "categories"
            $categoryExists = DB::table('categories')
                                ->where('category_id', $category)
                                ->exists();

            if (!$categoryExists) {
                // La catégorie n'existe pas, renvoyer une erreur ou gérer le cas approprié
                DB::rollBack();
                return ERROR_CATEGORY_NOT_FOUND;
            }

            // Vérifier si un quiz existe déjà pour la catégorie donnée
            $quizExists = DB::table('quizzes')
                            ->where('category_id', $category)
                            ->exists();

            if ($quizExists) {
                // Un quiz existe déjà pour la catégorie donnée, renvoyer une erreur ou gérer le cas approprié
                DB::rollBack();
                return ERROR_QUIZ_EXISTS_FOR_CATEGORY;
            }

            // Insérer un nouveau quiz
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
     * Add new Question into Quizz
     *
     * @param User $user
     * @param integer $quiz
     * @param string $text
     * @return string|null
     */
    public function questionQuiz(int $quizId, string $text): ?string
    {
        // Vérifier si un Quiz existe
        $quizExists = ModelsQuiz::where('quiz_id', $quizId)
                            ->first();
        if($quizExists) {
            // Ajouter une question
            $quizQuestion = QuizQuestion::create([
                'quiz_id'           => $quizExists,
                'question_text'     => $text
            ]);
            
            $quizQuestion->save();
            return $quizQuestion;
        }
        return null;
    }

}