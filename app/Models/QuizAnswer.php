<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'quizAnswers';

    /**
     * Primary key column
     *
     * @var string
     */
    protected $primaryKey = 'answer_id';

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
        'user_id',
        'quiz_id',
        'question_text',
        'chosen_option'
    ];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class);
    }
}