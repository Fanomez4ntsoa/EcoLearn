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
        'ressource_id',
        'question_text',
        'answer_possibilities',
        'correct_option'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    
    public function answers()
    {
        return $this->hasOne(QuizAnswer::class);
    }
}