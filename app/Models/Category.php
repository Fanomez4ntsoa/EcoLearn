<?php

namespace App\Models;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The table associated with the model
     * 
     * @var string
     */
    protected $table = 'categories';

    /**
     * Primary Key
     * 
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * Indicates if the model should be timestampted
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'created_at'
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

    /**
     * Category can have many resource 
     *
     * @return void
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Category can have just one Quiz
     *
     * @return void
     */
    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }
}
