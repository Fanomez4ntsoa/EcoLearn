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
     * Index Quiz Question
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'resource'  => 'required|integer',
            'field'     => 'nullable|string:|in:*,id,quiz,question,answer,correct',
            'search'    => 'nullable|string',
            'per_page'  => 'nullable|integer'
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
            if(!$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }
            $resource = $this->resourceService->find($request->resource);
            $category = $this->categoryService->find($resource->category_id);

            $quizQuestionCollection = $this->quizService->index(
                resource: $resource,
                field: $request->field,
                search: $request->search,
                perPage: $request->per_page
            );

            if($quizQuestionCollection->isEmpty()){
                return $this->error(
                    message:__('error.quizz.collection'),
                    httpCode: 404
                );
            }

            $quizQuestions = [];
            foreach ($quizQuestionCollection as $quizQuestion) {
                $quizQuestion->answer_possibilities = json_decode($quizQuestion->answer_possibilities, true);
                $quizQuestions[] = $quizQuestion;
            }

            return $this->success(
                message: __("success.quizz.question_informations"),
                data: [
                    'category'  => $category->name,
                    'ressource' => $resource->title,
                    'question'  => $quizQuestions
                ],
                httpCode: 200
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            return $this->error(__('error.default'), httpCode: 403);
        }
        return $this->error();    
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
            'quiz_id'                   => 'required|integer',
            'resource_id'               => 'required|integer',
            'question'                  => 'required|max:120',
            'answer_possibilities'      => 'required|array',
            'correct_option'            => 'required|string'
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
            if(!$quiz) {
                return $this->error(
                    message:__('error.quizz.not_found'),
                    httpCode: 403
                );
            }

            $category = $this->categoryService->find($quiz->category);
            $resource = $this->resourceService->find($request->resource_id);

            if(!$resource || $resource->category_id !== $category->id) {
                return $this->error(
                    message: __('error.quizz.resource.category'),
                    httpCode: 403
                );
            }

            $question = $this->quizService->questionQuiz($request->quiz_id, $request->resource_id, $request->question, $request->answer_possibilities, $request->correct_option);
            if($question != null) {
                return $this->success(
                    message:__('success.quizz.question'),
                    data: [
                        "category"  => $category->name,
                        "ressource" => $resource->title,
                        "question"  => $question->question_text,
                        "correct"   => $question->correct_option
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
            return $this->error(__('error.default'), 404);
        }
        return $this->error();
    }

    /**
     * Get informations question Quiz
     *
     * @param integer $quizQuestionId
     * @return JsonResponse
     */
    public function showQuizQuestion(int $quizQuestionId): JsonResponse
    {
        $user = Auth::user();
        try {
            if(!$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $question = $this->quizService->findQuestion($quizQuestionId);
            $question->answer_possibilities = json_decode($question->answer_possibilities, true);
            $quizQuestions[] = $question;
            
            $resource = $this->resourceService->find($question->resource_id);
            $category = $this->categoryService->find($resource->category_id);

            if($question) {
                return $this->success(
                    message:__('success.quizz.question_informations'),
                    data: [
                        'category'  => $category->name,
                        'ressource' => $resource->title,
                        'question'  => $quizQuestions
                    ],
                    httpCode: 200
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);

            return $this->error(__('error.default'), httpCode: 403);
        }
        return $this->error();
    }

    /**
     * Delete Quiz Question
     *
     * @return JsonResponse
     */
    public function quizQuestionDelete(int $quizQuestionId): JsonResponse
    {
        $user = Auth::user();
        try {
            if(!$this->guardService->allows($user, ACCESS_ADMIN_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $quizQuestion = $this->quizService->findQuestion($quizQuestionId);
            if(!$quizQuestion) {
                return $this->error(
                    message:__('error.quizz.already_deleted'),
                    httpCode: 403
                );
            }
            
            $delete = $this->quizService->deleteQuestion($quizQuestion);
            if($delete) {
                return $this->success(
                    message:__('success.quizz.deleted'),
                    httpCode: 202
                );
            }
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
        return $this->error();
    }

    /**
     * Set answer for question Quiz
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setAnswerQuestionQuiz(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quiz_id'       => 'required|integer',
            'question_id'   => 'required|integer',
            'chosen_option' => 'required|string'
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
            if(!$this->guardService->allows($user, ACCESS_CLIENT_QUIZ)) {
                return $this->error(
                    message:__('error.access.denied'),
                    httpCode: 401
                );
            }

            $quiz = $this->quizService->find($request->quiz_id);
            $question = $this->quizService->findQuestion($request->question_id);
            if(!$quiz && !$question) {
                return $this->error(
                    message:__('error.quizz.not_found'),
                    httpCode: 403
                );
            }

            $answer = $this->quizService->answerQuiz($user, $quiz, $question, $request->chosen_option);
            if($answer == ERROR_USER_ANSWER) {
                return $this->error(
                    message:__('error.quizz.answer.user'),
                    httpCode: 403
                );
            } else if($answer == ERROR_QUIZ_ANSWER_OPTION) {
                return $this->error(
                    message:__('error.quizz.answer.option'),
                    httpCode: 403
                );
            }

            return $this->success(
                message:__('success.quizz.answer'),
                httpCode: 200
            );


        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            return $this->error(__('error.default'), 404);
        }
        return $this->error();
    }
    
}