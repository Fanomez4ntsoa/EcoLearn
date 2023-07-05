<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Contracts\EcoLearn\QuizServiceInterface;
use App\Contracts\Security\GuardServiceInterface;
use App\Contracts\EcoLearn\AccountServiceInterface;

class QuizController extends Controller
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected GuardServiceInterface $guardService,
        protected QuizServiceInterface $quizService
    ) {
        $this->middleware('auth:api');
    }

    /**
     * Add new Quizz from category by Admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function quizCategory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile'       => 'string',
            'category'      => 'required|integer',
        ]);

        try {
            if($validator->fails()) {
                return $this->error(
                    message:__('error.validations'),
                    data: $validator->errors(),
                    httpCode: 422
                );
            }
    
            $user = Auth::user();
            $profile = $this->accountService->getProfile($request->profile);
            
            if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $quizzes = $this->quizService->create($user, $request->category);
            if($quizzes != SUCCESS_QUIZZ_CREATED) {
                if($quizzes == ERROR_CATEGORY_NOT_FOUND) {
                    return $this->error(
                        message:__('error.quizz.category.not_found'),
                        httpCode: 404
                    );    
                } else if ($quizzes == ERROR_QUIZ_EXISTS_FOR_CATEGORY) {
                    return $this->error(
                        message:__('error.quizz.category.exists'),
                        httpCode: 403
                    );    
                } else {
                    return $this->error(
                        message:__('error.quizz.create'),
                        httpCode: 404
                    );
                }
            }
    
            return $this->success(
                message:__('success.quizz.create'),
                data: $quizzes,
                httpCode: 201
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
    }

    /**
     * Add new quiz question
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function quizQuestion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile'       => 'integer',
            'quiz_id'       => 'required|integer',
            'question'      => 'required|max:64'
        ]);

        if($validator->fails()) {
            return $this->error(
                message:__('error.validations'),
                data: $validator->errors(),
                httpCode: 422
            );
        }

        $user = Auth::user();
        $profile = $this->accountService->getProfile($request->profile);

        if($profile != ADMINISTRATION_ADMIN || !$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
            return $this->error(
                message:__('error.access.denied'),
                httpCode: 401
            );
        }

        $question = $this->quizService->questionQuiz($request->quiz_id, $request->question);
        if(!$question) {
            return $this->error(
                message:__('error.quizz.question'),
                httpCode: 403
            );
        }

        return $this->success(
            message:__('success.quizz.question'),
            data: $question,
            httpCode: 201
        );
    }
}