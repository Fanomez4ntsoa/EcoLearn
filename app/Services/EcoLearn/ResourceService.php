<?php

namespace App\Services\EcoLearn;

use Illuminate\Support\Str;
use App\EcoLearn\Models\User;
use Illuminate\Support\Carbon;
use App\EcoLearn\Models\Resource;
use App\EcoLearn\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\Paginator;
use App\Contracts\EcoLearn\ResourceServiceInterface;
use App\Contracts\EcoLearn\CategoryServiceInterface;

class ResourceService implements ResourceServiceInterface
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {
    }

    /**
     * Find resource by id
     *
     * @param integer $id
     * @return Resource|null
     */
    public function find(int $id): ?Resource
    {
        $resources = DB::table('ressources')
                        ->where('ressource_id', $id)
                        ->first();

        if ($resources) {
            $creationDate   = to_datetime($resources->created_at);

            $newResource = new Resource();
            $newResource->id                = $resources->ressource_id;
            $newResource->category_id       = $resources->category_id;
            $newResource->title             = $resources->title;
            $newResource->description       = $resources->description;
            $newResource->creationDate      = $creationDate;
            $newResource->updatedDate       = $creationDate;

            return $newResource;
        }
        return null;
    }

    /**
     * Find resource by category
     *
     * @param Category $category
     * @return array|null
     */
    public function findByCategory(Category $category): ?array
    {
        $resources = DB::table('ressources')
                        ->where('category_id', $category->id)
                        ->get();
        
        if ($resources->isNotEmpty()) {
            $newResources = [];
    
            foreach ($resources as $resource) {
                $creationDate = to_datetime($resource->created_at);
    
                $newResource = new Resource();
                $newResource->id = $resource->ressource_id;
                $newResource->title = $resource->title;
                $newResource->description = $resource->description;
                $newResource->creationDate = $creationDate;
                $newResource->updatedDate = $creationDate;
    
                $newResources[] = $newResource;
            }
    
            return $newResources;
        }
        return null;
    }

    /**
     * Resource index in Category
     *
     * @param Category $category
     * @param string|null $field
     * @param string|null $search
     * @param integer|null $perPage
     * @return Paginator
     */
    public function index(Category $category, ?string $field = null, ?string $search = null, ?int $perPage = null): Paginator
    {
        $query = DB::table('ressources')
                    ->where('category_id', $category->id)
                    ->where(function($query) use ($field, $search) {
                        $maps = [
                            'id'            => 'ressource_id',
                            'title'         => 'title',
                            'description'   => 'description',
                        ];

                        if($search) {
                            if(is_null($field) || $field === '') {
                                $compare = '%' . Str::replaceArray(' ', ['%', ''], $search) . '%';
                                $query
                                    ->where('title', 'like', $compare)
                                    ->orWhere('description', 'like', $compare);
                            } else if(isset($maps[$field])) {
                                $query->where($maps[$field], 'like', '%' . Str::replace(' ', '%', $search) . '%');
                            }
                        }

                    });

        return $query->paginate($perPage);
    }

    /**
     * Create new Resource
     *
     * @param integer $category_id
     * @param string $title
     * @param string $description
     * @param string $url
     * @return integer
     */
    public function create(User $user, Category $category, string $title, string $description, string $url): ?int
    {
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $categoryExists = DB::table('categories')
                                ->where('category_id', $category->id)
                                ->exists();
                                
            if (!$categoryExists) {
                DB::rollBack();
                return ERROR_CATEGORY_NOT_FOUND;
            }
            

            $resourceId = DB::table('ressources')
                        ->insertGetId([
                            'category_id'   => $category->id,
                            'title'         => $title,
                            'description'   => $description,
                            'url'           => $url,
                            'created_at'    => $now
                        ]);

            $resource = $this->find($resourceId);
            if($resource) {
                DB::commit();
                return SUCCESS_RESOURCE_CREATED;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return ERROR_RESOURCE_CREATED;
        }
        return null;
    }

    /**
     * Update Resource
     *
     * @param Resource $resource
     * @param string $title
     * @param string $description
     * @param string $url
     * @return boolean
     */
    public function update(Resource $resource, Category $category, string $title, string $description, string $url): bool
    {
        DB::beginTransaction();
        $now = Carbon::now();

        try {
            DB::table('ressources')
                ->where('ressource_id', $resource->id)
                ->update([
                    'category_id'   => $category->id,
                    'title'         => $title,
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
     * Delete resource
     *
     * @param Resource $resource
     * @return boolean
     */
    public function delete(Resource $resource): bool
    {
        DB::beginTransaction();
        try {
            $comments = DB::table('comments')
                            ->where('ressource_id', $resource->id)
                            ->exists();
            if($comments) {
                DB::table('comments')
                    ->where('ressource_id', $resource->id)
                    ->delete();
            }

            $progress = DB::table('progress')
                            ->where('ressource_id', $resource->id)
                            ->get();
            if($progress) {
                DB::table('progress')
                    ->where('ressource_id', $resource->id)
                    ->delete();
            }

            $resource = DB::table('ressources')
                            ->where('ressource_id', $resource->id)
                            ->delete();
            if($resource) {
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