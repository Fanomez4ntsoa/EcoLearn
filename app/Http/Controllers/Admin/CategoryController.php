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
    /**
     * Create a new Controller instance
     */
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
            'profile'                => 'string',
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
            $profile = $this->accountService->getProfile($request->profile);
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
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
            throw new Exception(__('error.category.creation'), 403);

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
            return $this->error();
        }
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
            'profile'       => 'string',
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
            $profile = $this->accountService->getProfile($request->profile);
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows($user, ACCESS_ADMIN_CATEGORIES)) {
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
            throw new Exception(__('error.category.update'), 403);

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            return $this->error();
        }
    }

    /**
     * Delete a category
     *
     * @param Request $request
     * @param integer $categoryId
     * @return JsonResponse
     */
    public function delete(Request $request, int $categoryId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile'       => 'string'
        ]);

        if($validator->fails()) {
            return $this->error(
                message:__('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        try {
            $profile = $this->accountService->getProfile($request->profile);
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows(Auth::user(), ACCESS_ADMIN_CATEGORIES)) {
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
            throw new Exception(__('error.category.delete'), 403);
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            
            return $this->error(
                message: __('error.default'),
                httpCode: 403
            );
        }
        return $this->error(
            message:__('error.default'), 
        );
    }

}