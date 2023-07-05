<?php

namespace App\Contracts\Admin;

use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\EcoLearn\ResourceServiceInterface;
use App\Http\Controllers\Controller;
use App\Resources\ResourceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    /**
     * Create a new Controller instance
     */
    public function __construct(
        protected ResourceServiceInterface $ressourceService,
        protected AccountServiceInterface $accountService
    ) {
        $this->middleware('auth:api');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile'                 => 'string',
            'category_id'             => 'int',
            'title'                   => 'required|string|min:2|max:64',
            'description'            => 'required|string',
            'url'                   => 'nullable|string'
        ]);

        $user = Auth::user();
        $profile = $this->accountService->getProfile($request->profile);

        if ($profile != ADMINISTRATION_ADMIN) {
            return $this->error(
                message: __('error.category.acces'),
                httpCode: 500,
            );
        }
        try {
            if ($validator->fails()) {
                return $this->error(
                    message: __('error.validations'),
                    data: $validator->errors(),
                    httpCode: 422
                );
            }

            $status = $this->ressourceService->create($request->category_id, $request->title, $request->description, $request->url);

            if ($status === 1) {
                return $this->success(
                    message: __('ressource créé avec success'),
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
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function findByID($ressource_id): JsonResponse
    {
        try {
            $ressource = $this->ressourceService->findByID($ressource_id);
            $to_json = new ResourceResource($ressource);

            if ($ressource) {
                return $this->success(
                    message: __('success.user.informations'),
                    data: $to_json->toArray($ressource),
                    httpCode: 200
                );
            }

            return $this->error(
                message: __('error.user.not_found'),
                httpCode: 404
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ressources  $ressource
     * @return \Illuminate\Http\Response
     */
    // public function edit(Ressources $ressource)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ressources  $ressources
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Ressources $ressources)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ressources  $ressource
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Ressources $ressource)
    // {
    //     //
    // }
}