<?php

namespace App\Services\EcoLearn;

use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        protected AccountServiceInterface $accountService
    ) {
    }

    /**
     * Find All category 
     * 
     * @return Category|null
     */
    public function findAll(): ?Category
    {
        $category = DB::table('categories')
                        ->first();
        if ($category) {
            $creationDate   = to_datetime($category->created_at);
            $newCategory = new Category();
            $newCategory->id                = $category->category_id;
            $newCategory->name              = $category->name;
            $newCategory->description       = $category->decription;
            $newCategory->created_at        = $creationDate;

            return $newCategory;
        }
        return null;
    }

    /**
     * Find category by id
     * 
     * @param integer $id
     * @return Categoy|null
     */
    public function findByID(int $id): ?Category
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if ($category) {
            $creationDate   = to_datetime($category->created_at);

            $newCategory = new Category();
            $newCategory->id                  = $category->category_id;
            $newCategory->name                = $category->name;
            $newCategory->description         = $category->description;
            $newCategory->created_at          = $creationDate;

            return $newCategory;
        }
        return null;
    }

    /**
     * Create new Category
     * 
     * @param string $name
     * @param string $description
     * @return integer
     */
    public function create(string $name, string $description): int
    {
        $category = new Category();
        $category->name         = $name;
        $category->description  = $description;

        $category->save();

        return 0;
    }
}