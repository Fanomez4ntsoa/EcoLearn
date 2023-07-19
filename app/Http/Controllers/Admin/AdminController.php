<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Contracts\User\UserServiceInterface;
use App\Contracts\Security\GuardServiceInterface;
use App\Contracts\EcoLearn\AccountServiceInterface;

class AdminController extends Controller
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected GuardServiceInterface $guardService,
        protected UserServiceInterface $userService
    ) {
        $this->middleware('auth:api');
    }

    /**
     * Users list with filter
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): UserResource|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'field'         => 'nullable|string:|in:*,id,name,username,email',
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
            $user = Auth::user();            
            if(!$this->guardService->allows($user, ACCESS_ADMIN_USER)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }
            
            $userCollection = $this->userService->index(
                field: $request->field,
                search: $request->search,
                perPage: $request->per_page
            );

            if($userCollection->isEmpty()) {
                return $this->error(
                    message: __('error.user.collection'),
                    httpCode: 404
                );
            }

            return $this->success(
                message: __('success.user.collection_informations'),
                data: [
                    'users' => UserResource::collection($userCollection), 
                    'pagination' => [
                        'from'          => $userCollection->firstItem(),
                        'to'            => $userCollection->lastItem(),
                        'next_page_url' => $userCollection->nextPageUrl(),
                        'path'          => $userCollection->path(),
                        'per_page'      => $userCollection->perPage(),
                        'prev_page_url' => $userCollection->previousPageUrl(),
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
     * Get User informations by admin
     *
     * @return JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        $currentUser = Auth::user();
        try {

            if(!$this->guardService->allows($currentUser, ACCESS_ADMIN_USER)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $user = $this->userService->find($userId);

            if($user) {
                return $this->success(
                    message:__('success.user.informations'),
                    data: $user,
                    httpCode: 200
                );
            }
            return $this->error(__('error.user.not_found'), httpCode: 403);

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
     * Delete User by admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(int $userId): JsonResponse
    {
        $currentUser = Auth::user();
        try {

            if(!$this->guardService->allows($currentUser, ACCESS_ADMIN_USER)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }
            $user = $this->userService->find($userId);

            $delete = $this->userService->delete($user);
            if($delete) {
                return $this->success(
                    message:__('success.user.deleted'),
                    httpCode: 202
                );
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            
            return $this->error(
                message: __('error.user.delete'),
                httpCode: 403
            );
        }
        return $this->error();
    }
}