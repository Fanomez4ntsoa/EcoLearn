<?php

namespace App\Models;

use App\Traits\Models\AccessModel;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use AccessModel;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'accessRight';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $primaryKey = 'access_id';

    /**
     * Disable Eloquent Timestamp
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast
     *
     * @var array
     */
    protected $casts = [
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime'
    ];
}