<?php

namespace App\Contracts\EcoLearn;

use App\EcoLearn\Models\Category;
use App\EcoLearn\Models\User;
use App\Models\Category as ModelsCategory;

interface CategoryServiceInterface
{
    /**
     * Find category by id
     * 
     * @param integer $id
     * @return Category|null
     */
    public function find(int $id): ?Category;

    /**
     * Create new Category
     * 
     * @param string $name
     * @param string $description
     * @return integer|null
     */
    public function create(string $name, string $description): ?int;

    /**
     * Update category
     *
     * @param Category $category
     * @param string $name
     * @param string $description
     * @return boolean
     */
    public function update(Category $category, string $name, string $description): bool;

    /**
     * Delete category
     *
     * @param Category $category
     * @return boolean
     */
    public function delete(Category $category): bool;
}