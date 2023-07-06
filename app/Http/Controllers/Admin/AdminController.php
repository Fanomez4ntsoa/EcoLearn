<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\Security\GuardServiceInterface;
use App\Contracts\User\UserServiceInterface;
use App\EcoLearn\Models\User;
use App\Http\Controllers\Controller;
use App\Resources\UserResource;
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
     * All Users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
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
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows(Auth::user(), ACCESS_ADMIN_PROFILES_ACCESS)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $userCollection = $this->userService->getAllclientUser();

            if($userCollection->isEmpty()) {
                return $this->error(
                    message: __('error.user.collection'),
                    httpCode: 404
                );
            }

            return $this->success(
                message: __('success.user.collection_informations'),
                data: $userCollection,
                httpCode: 200
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
    }

    /**
     * Get User informations 
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
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows(Auth::user(), ACCESS_ADMIN_PROFILES_ACCESS)) {
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
}