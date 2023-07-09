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
use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\Services\EcoLearn\ResourceService;

class QuizController extends Controller
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected GuardServiceInterface $guardService,
        protected QuizServiceInterface $quizService,
        protected ResourceService $resourceService,
        protected CategoryServiceInterface $categoryService,
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
            
            if(!$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $status = $this->quizService->create($user, $request->category);

            if($status != SUCCESS_QUIZZ_CREATED) {
                if($status == ERROR_CATEGORY_NOT_FOUND) {
                    return $this->error(
                        message:__('error.quizz.category.not_found'),
                        httpCode: 404
                    );    
                } else if ($status == ERROR_QUIZ_EXISTS_FOR_CATEGORY) {
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
            } else {
                return $this->success(
                    message:__('success.quizz.created'),
                    httpCode: 201
                );
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
        return $this->error();
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
            'quiz_id'       => 'required|integer',
            'resource_id'   => 'required|integer',
            'question'      => 'required|max:64'
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
    
            if(!$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }
            
            $quiz = $this->quizService->find($request->quiz_id);

            if($quiz) {
                $category = $this->categoryService->find($quiz->category);
                $resource = $this->resourceService->find($request->resource_id);
                if(!array_column($this->resourceService->findByCategory($category), 'id')) {
                    return $this->error(
                        message: __('error.resource.not_found'),
                        httpCode: 403
                    );
                }
                
            }

            $question = $this->quizService->questionQuiz($request->quiz_id, $request->resource_id, $request->question);
            if($question != null) {
                return $this->success(
                    message:__('success.quizz.question'),
                    data: [
                        "category"  => $category->name,
                        "ressource" => $resource->title,
                        "question"  => $question->question_text
                    ],
                    httpCode: 201
                );
            } else if($question == false ){
                return $this->error(
                    message:__('error.quizz.question_already'),
                    httpCode: 404
                );
            } else {
                return $this->error(
                    message:__('error.quizz.quesiton'),
                    httpCode: 403
                );
            }
    
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
        return $this->error();
    }
}