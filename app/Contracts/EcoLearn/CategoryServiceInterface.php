<?php

namespace App\Contracts\EcoLearn;

use App\Models\Category;

interface CategoryServiceInterface
{
    /**
     * Find All category 
     * 
     * @return Category|null
     */
    public function findAll(): ?Category;

    /**
     * Find category by id
     * 
     * @param integer $id
     * @return Category|null
     */
    public function findByID(int $id): ?Category;

    /**
     * Create new Category
     * 
     * @param string $name
     * @param string $description
     * @return integer
     */
    public function create(string $name, string $description): int;
}