<?php

namespace App\Models;

use App\Models\Scopes\UnexpiredScope;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'quizzes';

    /**
     * Primary key column
     *
     * @var string
     */
    protected $primaryKey = 'quiz_id';

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
        'title',
        'description',
        'created_at',
    ];

    /**
     * The attributes that should be cast
     *
     * @var array
     */
    protected $casts = [
        'created_at'    => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}