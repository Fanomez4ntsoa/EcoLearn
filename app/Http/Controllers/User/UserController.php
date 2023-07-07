<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Contracts\User\UserServiceInterface;
use App\Contracts\Security\GuardServiceInterface;

class UserController extends Controller
{
    /**
     * Create a new controller instance 
     */
    public function __construct(
        protected UserServiceInterface $userService,
        protected GuardServiceInterface $guardService,
    ) {
        $this->middleware('auth:api', ['only' => 'update']);
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
            $user = Auth::user();
            $allowed = $this->guardService->allows($user, ACCESS_CLIENT_USER); 
            if(!$allowed) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            } else {
                $updated = false;
                $user = $this->userService->find($userId);

                if($user) {
                    $userCheck = $this->userService->findByEmail($request->email);
                    if($userCheck && ($userCheck->id !== $user->id)) {
                        return $this->error(
                            message:__('error.user.not_found'),
                            httpCode: 404
                        );
                    }

                    $updated = $this->userService->update(
                        user: $user,
                        email: $request->email,
                        name: $request->name,
                        username: $request->username
                    );
                }

                if($updated) {
                    return $this->success(
                        message:__('success.user.updated'),
                        httpCode: 202
                    );
                }
                return $this->error();
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
    }
}