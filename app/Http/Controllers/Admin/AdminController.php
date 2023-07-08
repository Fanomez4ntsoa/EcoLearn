<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\Security\GuardServiceInterface;
use App\Contracts\User\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Resources\UserResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'profile'       => 'string',
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
            $profile = $this->accountService->getProfile($request->profile);
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows(Auth::user(), ACCESS_ADMIN_USER)) {
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
    }

    /**
     * Get User informations by admin
     *
     * @return JsonResponse
     */
    public function show(Request $request, int $userId): JsonResponse
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
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows(Auth::user(), ACCESS_ADMIN_USER)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $user = $this->userService->find($userId);

            if($user) {
                return $this->success(
                    message:__('error.user_informations'),
                    data: $user,
                    httpCode: 200
                );
            }
            return $this->error(
                message:__('error.user.not_found'),
                httpCode: 404
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
    }

    /**
     * Delete User by admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request, int $userId): JsonResponse
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
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows(Auth::user(), ACCESS_ADMIN_USER)) {
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
            throw new Exception(__('error.user.delete'), 403);

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