<?php

namespace App\Services\EcoLearn;

use App\EcoLearn\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Contracts\EcoLearn\CategoryServiceInterface;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Find category by id 
     * 
     * @param integer $id
     * @return Category|null
     */
    public function find(int $id): ?Category
    {
        $category = DB::table('categories')
                        ->where('category_id', $id)
                        ->first();

        if ($category) {
            $creationDate   = to_datetime($category->created_at);

            $newCategory = new Category();
            $newCategory->id                = $category->category_id;
            $newCategory->name              = $category->name;
            $newCategory->description       = $category->description;
            $newCategory->creationDate      = $creationDate;
            $newCategory->updatedDate       = $creationDate;

            return $newCategory;
        }
        return null;
    }

    /**
     * Create new Category
     * 
     * @param string $name
     * @param string $description
     * @return integer|null
     */
    public function create(string $name, string $description): ?int
    {
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $category = DB::table('categories')
                        ->insertGetId([
                            'name'          => $name,
                            'description'   => $description,
                            'created_at'    => $now
                        ]);
        
            if($category) {
                DB::commit();
                return SUCCESS_CATEGORY_CREATED;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return ERROR_CATEGORY_CREATED;
        }
        return null;
    }

    /**
     * Update category
     *
     * @param Category $category
     * @param string $name
     * @param string $description
     * @return boolean
     */
    public function update(Category $category, string $name, string $description): bool
    {
        DB::beginTransaction();
        $now = Carbon::now();
       
        try {
            DB::table('categories')
                ->where('category_id', $category->id)
                ->update([
                    'name'          => $name,
                    'description'   => $description,
                    'updated_at'    => $now
                ]);

            DB::commit();
            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [$th]);

            return false;
        }
    }

    /**
     * Delete category
     *
     * @param Category $category
     * @return boolean
     */
    public function delete(Category $category): bool
    {
        DB::beginTransaction();
        try {
            $resources = DB::table('ressources')
                            ->where('category_id', $category->id)
                            ->exists();

            if($resources) {
                DB::table('ressources')
                    ->where('category_id', $category->id)
                    ->delete();
            }

            $quizzes = DB::table('quizzes')
                        ->where('category_id', $category->id)
                        ->get();
            
            if($quizzes) {
                foreach($quizzes as $quiz) {
                    DB::table('quizQuestions')
                        ->where('quiz_id', $quiz->id)
                        ->delete();

                    DB::table('quizAnswer')
                        ->where('quiz_id', $quiz)
                        ->delete();
                }
            } else {
                DB::table('quizzes')
                    ->where('category_id', $category->id)
                    ->delete();

                DB::table('userProgress')
                    ->where('category_id')
                    ->delete();
            }
            
            $category = DB::table('categories')
                            ->where('category_id', $category->id)
                            ->delete();
            
            if($category) {
                DB::commit();
                return true;
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [$th]);
            return false;
        }
    }
}