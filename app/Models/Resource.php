<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    /**
     * The table associated with the model
     * 
     * @var string
     */
    protected $table = 'ressources';

    /**
     * Primary Key
     * 
     * @var string
     */
    protected $primaryKey = 'ressource_id';

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
        'category_id',
        'title',
        'category_id',
        'description',
        'url',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast
     * 
     * @var array
     */
    protected $casts = [
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
            $model->updated_at = $model->freshTimestamp();
        });
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
