<?php 

namespace App\Http\Controllers\Security;

use App\Contracts\Security\PasswordServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    public function __construct(
        protected PasswordServiceInterface $passwordService
    ) {
        
    }

    /**
     * Set password of User
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token'     => 'required|string',
            'password'  => 'required|string',
        ]);

        try {
            if($validator->fails()) {
                return $this->error(
                    message:__('error.validations'),
                    data: $validator->errors(),
                    httpCode: 422,
                );
            }

            $data = json_decode(decrypt($request->token));
            if($data->email && $data->token) {
                if($this->passwordService->verifyToken($data->email, $data->token)) {
                    if($this->passwordService->setPassword($data->email, $request->password)) {
                        return $this->success(__('success.security.password.set'));
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
            return $this->error(
                message:__('error.security.password.token_invalid'),
                httpCode: 400
            );
        }
        return $this->error();
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        return $this->error();
    }

    /**
     * Decode password token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function decodeToken(Request $request): JsonResponse
    {
        return $this->error();
    }
}