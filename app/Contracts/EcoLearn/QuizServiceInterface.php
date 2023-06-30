<?php

namespace App\Contracts\EcoLearn;

use App\EcoLearn\Models\Quiz;
use App\EcoLearn\Models\User;

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
     * Add new quizz by Admin
     *
     * @param string $title
     * @param string $description
     * @return string
     */
    public function create(User $user, int $category): ?int;
}