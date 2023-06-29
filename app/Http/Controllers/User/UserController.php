<?php

namespace App\Http\Controllers\User;

use App\Contracts\User\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance 
     */
    public function __construct(
        protected UserServiceInterface $userService
    ) {
        //
    }

    /**
     * Create new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|email|unique:users,email',
            'name'          => 'required|string|min:2|max:64',
            'username'      => 'required|string:max:64',
            'profile'       => 'string'
        ]);

        try {
            if($validator->fails()) {
                return $this->error(
                    message:__('error.validations'),
                    data: $validator->errors(),
                    httpCode: 422
                );
            }

            dd('here');
            
            $status = $this->userService->create($request->email, $request->name, $request->username, $request->profile);

            if($status === SUCCESS_USER_CREATED) {
                return $this->success(
                    message:__('success.user.created'),
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
     * Show User Information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        try {
            Auth::user();
            $user = $this->userService->find($userId);

            if($user) {
                return $this->success(
                    message:__('success.user.informations'),
                    data: new UserResource($user),
                    httpCode: 200
                );
                
            }

            return $this->error(
                message:__('error.user.not_found'),
                httpCode: 404
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
    }

    /**
     * Update an User
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, int $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|email|unique:users,email',
            'name'          => 'required|string|min:2|max:64',
            'username'      => 'required|string:max:64',
        ]);

        if($validator->fails()) {
            return $this->error(
                message:__('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        try {
            $currentUser = Auth::user();
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }

            
    }
}