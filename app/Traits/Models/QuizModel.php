<?php

namespace App\Traits\Models;

use App\Traits\Models\Model;

trait QuizModel
{
    use Model;

    /**
     * Get category Id
     *
     * @return string|null
     */
    public function setCategoryId(int $categoryId)
    {
        return $this->category_id = $categoryId;
    }
}