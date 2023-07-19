<?php

namespace App\EcoLearn\Models;

class QuizAnswer
{
    /**
     * Answer id
     *
     * @var integer
     */
    public int $id;

    /**
     * Answer Id user
     *
     * @var string
     */
    public $user_id;

    /**
     * Answer Id Quiz
     *
     * @var string
     */
    public $quiz_id;

    /**
     * Answer Id question
     *
     * @var string
     */
    public $question_id;

    /**
     * Answer selected option
     *
     * @var string
     */
    public $chosen_option;

    /**
     * Answer is Correct
     *
     * @var boolean
     */
    public $is_correct;
}