<?php

namespace App\Contracts\EcoLearn;

use App\EcoLearn\Models\Quiz;
use App\EcoLearn\Models\QuizAnswer;
use App\EcoLearn\Models\QuizQuestion;
use App\EcoLearn\Models\Resource;
use App\EcoLearn\Models\User;
use App\Models\QuizAnswer as ModelsQuizAnswer;
use App\Models\QuizQuestion as ModelsQuizQuestion;
use Illuminate\Contracts\Pagination\Paginator;

interface QuizServiceInterface
{
    /**
     * Find quiz by id
     *
     * @param integer $id
     * @return Quiz|null
     */
    public function find(int $id): ?Quiz;

    /**
     * Find question by id
     *
     * @param integer $id
     * @return QuizQuestion|null
     */
    public function findQuestion(int $id): ?QuizQuestion;

    /**
     * Quiz Question index in Resource
     *
     * @param Resource $resource
     * @param string|null $field
     * @param string|null $search
     * @param integer|null $perPage
     * @return Paginator
     */
    public function index(Resource $resource, string $field = null, string $search = null, int $perPage = null): Paginator;

    /**
     * Add new quizz by Admin
     *
     * @param string $title
     * @param string $description
     * @return string
     */
    public function create(User $user, int $category): ?int;

    /**
     * Question quizz
     *
     * @param integer $quiz
     * @param string $text
     * @param integer $resource
     * @return ModelsQuizQuestion|integer
     */
    public function questionQuiz(int $quizId, int $resourceId, string $text, array $answerPossibilities, string $correctOption): ModelsQuizQuestion|int|null;

    /**
     * Quiz answer 
     *
     * @param User $user
     * @param Quiz $quiz
     * @param QuizQuestion $question
     * @param string $selectedOption
     * @return ModelsQuizAnswer|QuizAnswer|integer|null
     */
    public function answerQuiz(User $user, Quiz $quiz, QuizQuestion $question, string $selectedOption): ModelsQuizAnswer|QuizAnswer|int|null;

    /**
     * Delete question Quiz
     *
     * @param QuizQuestion $quizQuestion
     * @return boolean
     */
    public function deleteQuestion(QuizQuestion $quizQuestion): ?bool;
}