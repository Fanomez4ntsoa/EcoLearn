<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->group(['prefix' => 'auth'], function($router) {
    $router->post('', 'AuthController@login');
    $router->get('', 'AuthController@me');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
});

$router->group(['prefix' => 'admin', 'namespace' => 'Admin'], function($router) {
    $router->group(['prefix' => 'user'], function($router) {
        $router->get('', 'AdminController@index');
        $router->get('{userId}', 'AdminController@show');
        $router->delete('delete/{userId}', 'AdminController@delete');
    });
    $router->group(['prefix' => 'category'], function($router) {
        $router->post('create', 'CategoryController@create');
        $router->patch('update/{categoryId}', 'CategoryController@update');
        $router->delete('delete/{categoryId}', 'CategoryController@delete');
    });
    $router->group(['prefix' => 'resource'], function($router) {
        $router->get('', 'ResourceController@index');
        $router->post('create', 'ResourceController@create');
        $router->get('{resourceId}', 'ResourceController@show');
        $router->patch('update/{resourceId}', 'ResourceController@update');
        $router->delete('delete/{resourceId}', 'ResourceController@delete');
    });
    $router->group(['prefix' => 'quizz'], function($router) {
        $router->post('create', 'QuizController@quizCategory');
        $router->post('question', 'QuizController@quizQuestion');
        $router->post('answer', 'QuizController@setAnswerQuestionQuiz');
        $router->delete('delete/{quizQuestionId}', 'QuizController@quizQuestionDelete');
    });
});

$router->group(['prefix' => 'security', 'namespace' => 'Security'], function ($router) {
    $router->post('password/decode-token', 'PasswordController@decodeToken');
    $router->post('password/reset', 'PasswordController@resetPassword');
    $router->post('password/set', 'PasswordController@setPassword');
});


$router->group(['prefix' => 'user', 'namespace' => 'User'], function($router) {
    $router->post('create', 'UserController@create');
    $router->patch('update/{userId}', 'UserController@update');
});

