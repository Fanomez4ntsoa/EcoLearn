<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\Contracts\Security\GuardServiceInterface;
use App\Http\Controllers\Controller;
use App\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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

            if ($status === 0) {
                return $this->success(
                    message: __('Category créé avec success'),
                    httpCode: 200,
                );
            }
            return $this->error();
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
            return $this->error();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function findByID($category_id): JsonResponse
    {
        try {
            Auth::user();
            $category = $this->categoryService->findByID($category_id);
            $to_json = new CategoryResource($category);

            if ($category) {
                return $this->success(
                    message: __('success.user.informations'),
                    data: $to_json->toArray($category),
                    httpCode: 200
                );
            }

            return $this->error(
                message: __('error.user.not_found'),
                httpCode: 404
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    // public function edit(Category $category)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Category $category)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Category $category)
    // {
    //     //
    // }

}