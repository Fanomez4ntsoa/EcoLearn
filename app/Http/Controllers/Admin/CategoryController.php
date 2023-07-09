<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\Contracts\Security\GuardServiceInterface;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService,
        protected AccountServiceInterface $accountService,
        protected GuardServiceInterface $guardService
    ) {
        $this->middleware('auth:api');
    }

    /**
     * Create new category.
     *
     * @return Response
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'                   => 'required|string|min:2|max:64',
            'description'            => 'required|string'
        ]);

        try {
            if ($validator->fails()) {
                return $this->error(
                    message: __('error.validations'),
                    data: $validator->errors(),
                    httpCode: 422
                );
            }

            $user = Auth::user();
            
            if(!$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $status = $this->categoryService->create($request->name, $request->description);

            if ($status === SUCCESS_CATEGORY_CREATED) {
                return $this->success(
                    message: __('success.category.created'),
                    httpCode: 200,
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());

            return $this->error(
                message:__('error.category.creation'),
                httpCode: 403
            );
        }
        return $this->error();
    }

    /**
     * Update category
     *
     * @param Request $request
     * @param integer $categoryId
     * @return JsonResponse
     */
    public function update(Request $request, int $categoryId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'nullable|string|min:2|max:64',
            'description'   => 'nullable|string'
        ]);

        if($validator->fails()) {
            return $this->error(
                message:__('error.validations'),
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
            $category = $this->categoryService->find($categoryId);

            if($category) {
                $updated = $this->categoryService->update(
                    category: $category,
                    name: $request->name,
                    description: $request->description
                );
            }

            if($updated) {
                return $this->success(
                    message:__('success.category.updated'),
                    httpCode: 200
                );
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);

            return $this->error(
                message:__('error.category.update'), 
                httpCode: 403
            );
        }
        return $this->error();
    }

    /**
     * Delete a category
     *
     * @param Request $request
     * @param integer $categoryId
     * @return JsonResponse
     */
    public function delete(int $categoryId): JsonResponse
    {
        $user = Auth::user();

        try {
            if(!$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $category = $this->categoryService->find($categoryId);

            $delete = $this->categoryService->delete($category);
            if($delete) {
                return $this->success(
                    message:__('success.category.deleted'),
                    httpCode: 202
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            
            return $this->error(
                message: __('error.category.delete'),
                httpCode: 403
            );
        }
        return $this->error();
    }

}