<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'quizQuestions';

    /**
     * Primary key column
     *
     * @var string
     */
    protected $primaryKey = 'question_id';

    /**
     * Disable Eloquent Timestamp
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'quiz_id',
        'question_text',
    ];

    // protected static function booted()
    // {
    //     static::addGlobalScope(new UnexpiredScope());
    // }
}