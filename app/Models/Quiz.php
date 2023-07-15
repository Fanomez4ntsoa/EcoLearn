<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "quizzes";

    /**
     * Primary key column
     *
     * @var string
     */
    protected $primaryKey = "quiz_id";

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
        'category_id',
    ];

    /**
     * Every quiz have just one category 
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * One quiz can have many question
     *
     * @return void
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}