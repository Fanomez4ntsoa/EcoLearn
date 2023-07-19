<?php

namespace App\Traits\Models;

use App\Models\Scopes\UnexpiredScope;

trait AccessModel
{
    use Model;

    /**
     * Get access key
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Get access name 
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    protected static function booted()
    {
        static::addGlobalScope(new UnexpiredScope());
    }
}