<?php

namespace App\Contracts\EcoLearn;

use App\EcoLearn\Models\Resource;
use App\EcoLearn\Models\Category;
use App\EcoLearn\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

interface ResourceServiceInterface
{
    /**
     * Find resource by id
     *
     * @param integer $id
     * @return Resource|null
     */
    public function find(int $id): ?Resource;

    /**
     * Resource index in Category
     *
     * @param Category $category
     * @param string|null $field
     * @param string|null $search
     * @param integer|null $perPage
     * @return Paginator
     */
    public function index(Category $category, string $field = null, string $search = null, int $perPage = null ): Paginator;

    /**
     * Create new Ressource
     * 
     * @param Category $category
     * @param string $title
     * @param string $description
     * @param string $url
     * @return integer
     */
    public function create(User $user, Category $category, string $title, string $description, string $url): ?int;   

    /**
     * Update category
     *
     * @param Resource $resource
     * @param string $title
     * @param string $description
     * @param string $url
     * @return boolean
     */
    public function update(Resource $resource, Category $category, string $title, string $description, string $url): bool;

    /**
     * Delete resource
     *
     * @param Resource $resource
     * @return boolean
     */
    public function delete(Resource $resource): bool;
}