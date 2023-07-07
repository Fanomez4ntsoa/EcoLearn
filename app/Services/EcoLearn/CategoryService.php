<?php

namespace App\Services\EcoLearn;

use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\EcoLearn\Models\Category;
use App\EcoLearn\Models\User;
use App\Models\Category as ModelsCategory;
use App\Models\Scopes\UnexpiredScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                        ->where(function($query) {
                            (new UnexpiredScope())->applyToBuilder($query, 'quizzes');
                        })
                        ->first();

        if ($category) {
            $creationDate   = to_datetime($category->created_at);

            $newCategory = new Category();
            $newCategory->id                = $category->category_id;
            $newCategory->name              = $category->name;
            $newCategory->description       = $category->decription;
            $newCategory->creationDate      = $creationDate;

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
                    'created_at'    => $now
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
            // Vérifier si la catégorie est liée à des ressources
            $resources = DB::table('ressources')
                            ->where('category_id', $category->id)
                            ->exist();

            if($resources) {
                DB::table('ressources')
                    ->where('category_id', $category->id)
                    ->delete();
            }

            // Récuperer les quiz liés à la catégorie
            $quizzes = DB::table('quizzes')
                        ->where('category_id', $category->id)
                        ->get();

            if($quizzes) {
                foreach($quizzes as $quiz) {
                    // Supprimer les quizQuestions liées au quiz
                    DB::table('quizQuestions')
                        ->where('quiz_id', $quiz->id)
                        ->delete();

                    // Supprimer les quizAnswers liées au quiz
                    DB::table('quizAnswer')
                        ->where('quiz_id', $quiz)
                        ->delete();
                }
            } else {
                // Supprimer les quizs liés à la catégorie
                DB::table('quizzes')
                    ->where('category_id', $category->id)
                    ->delete();
            }

            // Supprimer la catégorie
            $category = DB::table('categories')
                            ->where('category_id', $category)
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