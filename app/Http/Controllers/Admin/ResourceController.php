<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Resources\ResourceResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\Contracts\EcoLearn\ResourceServiceInterface;
use App\Contracts\Security\GuardServiceInterface;

class ResourceController extends Controller
{
    public function __construct(
        protected ResourceServiceInterface $resourceService,
        protected AccountServiceInterface $accountService,
        protected GuardServiceInterface $guardService,
        protected CategoryServiceInterface $categoryService,
    ) {
        $this->middleware('auth:api');
    }

    /**
     * Resources list with filter
     *
     * @param Request $request
     * @return ResourceResource|JsonResponse
     */
    public function index(Request $request): ResourceResource|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category'      => 'required|integer',
            'field'         => 'nullable|string:|in:*,id,title,description',
            'search'        => 'nullable|string',
            'per_page'      => 'nullable|integer'
        ]);

        if($validator->fails()) {
            return $this->error(
                message:__('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        try {
            if(!$this->guardService->allows(Auth::user(), ACCESS_ADMIN_CATEGORIES)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }
            $category = $this->categoryService->find($request->category);
            
            $resourceCollection = $this->resourceService->index(
                category: $category,
                field: $request->field,
                search: $request->search,
                perPage: $request->per_page
            );

            
            if($resourceCollection->isEmpty()) {
                return $this->error(
                    message: __('error.resource.collection'),
                    httpCode: 404
                );
            }
            
            return $this->success(
                message: __('success.user.collection_informations'),
                data: [
                    'users' => ResourceResource::collection($resourceCollection), 
                    'pagination' => [
                        'from'          => $resourceCollection->firstItem(),
                        'to'            => $resourceCollection->lastItem(),
                        'next_page_url' => $resourceCollection->nextPageUrl(),
                        'path'          => $resourceCollection->path(),
                        'per_page'      => $resourceCollection->perPage(),
                        'prev_page_url' => $resourceCollection->previousPageUrl(),
                    ],
                ],
                httpCode: 200
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
        return $this->error();
    }

    /**
     * Add new resource from category by Admin
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category'              => 'required|integer',
            'title'                 => 'required|string|min:2|max:100',
            'description'           => 'required|string',
            'url'                   => 'nullable|string'
        ]);

        if($validator->fails()) {
            return $this->error(
                message: __('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        try {
            $user = Auth::user();

            if(!$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $category = $this->categoryService->find($request->category);
            $status = $this->resourceService->create($user, $category, $request->title, $request->description, $request->url);

            if($status != SUCCESS_RESOURCE_CREATED) {
                if($status == ERROR_CATEGORY_NOT_FOUND) {
                    return $this->error(
                        message:__('error.resource.category.not_found'),
                        httpCode: 404
                    );    
                }
            }
    
            return $this->success(
                message:__('success.resource.created'),
                httpCode: 201
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());

            return $this->error(
                message:__('error.resource.create'),
                httpCode: 404
            );
        }
        return $this->error();
    }

    /**
     * Get informations of resources
     *
     * @param integer $resourceId
     * @return JsonResponse
     */
    public function show(int $resourceId): JsonResponse
    {
        try {
            if(!$this->guardService->allows(Auth::user(), ACCESS_ADMIN_USER)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $resource = $this->resourceService->find($resourceId);
            $category = $this->categoryService->find($resource->category_id);

            if($resource) {
                return $this->success(
                    message:__('error.user_informations'),
                    data: [
                        'resources' => [
                            'id'            => $resource->id,
                            'category'      => $category->name,
                            'title'         => $resource->title,
                            'description'   => $resource->description,
                            'creation Date' => $resource->creationDate->format(__('date.format.date.short'))
                        ],
                    ],
                    httpCode: 200
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);

            return $this->error(
                message:__('error.user.not_found'),
                httpCode: 404
            );
        }
        return $this->error();
    }

    /**
     * Update Resource
     *
     * @param Request $request
     * @param integer $resourceId
     * @return JsonResponse
     */
    public function update(Request $request, int $resourceId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category'              => 'required|integer',
            'title'                 => 'nullable|string|min:2|max:100',
            'description'           => 'nullable|string',
            'url'                   => 'nullable|string'
        ]);

        if($validator->fails()) {
            return $this->error(
                message: __('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        try {
            $user = Auth::user();

            if(!$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $updated = false;
            $category = $this->categoryService->find($request->category);
            $resource = $this->resourceService->find($resourceId);

            if(!$category) {
                return $this->error(
                    message:__('error.resource.category.not_found'),
                    httpCode: 404
                );
            }

            if(!$resource) {
                return $this->error(
                    message:__('error.resource.not_found'),
                    httpCode: 404
                );
            } else {
                $updated = $this->resourceService->update(
                    resource: $resource,
                    category: $category,
                    title: $request->title,
                    description: $request->description,
                    url: $request->url 
                );
            }

            if($updated) {
                return $this->success(
                    message:__('success.resource.updated'),
                    httpCode: 200
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
        return $this->error();
    }

   /**
    * Delete a resource
    *
    * @param Request $request
    * @param integer $resourceId
    * @return JsonResponse
    */
    public function delete(int $resourceId): JsonResponse
    {
        $user = Auth::user();
        try {
            if(!$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $resource = $this->resourceService->find($resourceId);
            if(!$resource) {
                return $this->error(
                    message:__('error.resource.already_deleted'),
                    httpCode: 404
                );
            }
            $delete = $this->resourceService->delete($resource);

            if($delete) {
                return $this->success(
                    message:__('success.resource.deleted'),
                    httpCode: 202
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            
            return $this->error(
                message: __('error.resource.delete'),
                httpCode: 403
            );
        }
        return $this->error();
    }
}