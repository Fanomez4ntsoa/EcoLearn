<?php

namespace App\Traits\Models;

trait Model 
{
    /**
     * Get the Id of the current model
     *
     * @return mixed
     */
    public function getId(): mixed
    {
        $attribute = $this->primaryKey;
        return $this->$attribute;
    }
}