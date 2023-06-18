<?php

namespace App\Models;

use App\Models\Scopes\UnexpiredScope;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model should be timestampted
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username', 
        'email',
        'isAdmin',
        'Valid_From',
        'Valid_Till',
        'token',
        'token_valid_from',
        'token_valid_till',
        'created_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'token',
        'token_valid_from',
        'token_valid_till',
    ];

    /**
     * The attributes that should be cast
     *
     * @var array
     */
    protected $casts = [
        'isAdmin'           => 'boolean',
        'Valid_From'        => 'datetime',
        'Valid_Till'        => 'datetime',
        'created_at'        => 'datetime',
        'token_valid_from'  => 'datetime',
        'token_valid_till'  => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    protected static function booted()
    {
        static::addGlobalScope(new UnexpiredScope());
    }
}
