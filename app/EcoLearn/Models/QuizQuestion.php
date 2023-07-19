<?php

namespace App\EcoLearn\Models;

class QuizQuestion
{
    /**
     * Quiz id
     *
     * @var integer
     */
    public int $id;

    /**
     * Quiz title
     *
     * @var string
     */
    public $quiz_id;

    /**
     * Quiz title
     *
     * @var string
     */
    public $resource_id;

    /**
     * Quiz description
     *
     * @var string
     */
    public $question_text;

    /**
     * Quiz description
     *
     * @var string
     */
    public $answer_possibilities;

    /**
     * Quiz description
     *
     * @var string
     */
    public $correct_option;
}