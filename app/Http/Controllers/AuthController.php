<?php

namespace App\Http\Controllers;

use App\Contracts\Security\GuardServiceInterface;
use App\Contracts\User\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected GuardServiceInterface $guardService,
    ) {
        $this->middleware('auth:api', ['except' => 'login']);
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|string',
        ]);

        if($validator->fails()) {
            return $this->error(
                message:__('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        try {
            $user = $this->userService->findByEmail($request->email);
            if($user) {
                if(Hash::check($request->password, $user->getHashedPassword())) {
                    $customClaims = ["accesses" => $this->guardService->index($user), "user" => $user];
                    dd(Auth::check());

                    $token = Auth::claims($customClaims)->login($user);
                    return $this->success(data: compact('token'));
                }
                return $this->error(
                    message:__('error.auth.password'),
                    data: ['password' => [__('error.auth.password')]],
                    httpCode: 400,
                );
            }
            return $this->error(
                message:__('error.auth.password'),
                data:['password' => [__('error.auth.email')]],
                httpCode: 400,
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }

        return $this->success(message:__('success.default'));
    }

    /**
     * Get current user details
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();
        return $this->success(data: [
            'id'            => $user->id,
            'username'      => $user->username,
            'email'         => $user->email,
            'creationDate'  => $user->created_at,
        ]);
    }

    /**
     * Loggout current User
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->success(
            message:__('success.auth.logged_out'),
            httpCode: 200,
        );
    }

    /**
     * Refresh Token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $token = Auth::refresh();
        return $this->success(
            data: compact('token')
        );
    }
}