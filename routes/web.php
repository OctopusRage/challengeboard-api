<?php

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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix'=>'users'], function () use ($app) {
    $app->post('create', 'UserController@create');
});

$app->group(['prefix' => 'admin'], function () use ($app) {
    $app->group(['prefix' => 'challenges'], function () use ($app) {
       $app->post('/', 'ChallengesController@create');
       $app->post('/confirm_request/{id}', 'ChallengesController@create');
    }); 
});

$app->group(['prefix' => 'challenges'], function () use ($app) {
    $app->get('/', 'ChallengesController@index');
    $app->post('/join/{id}', 'ChallengesController@join');
});

$app->post('/login', 'SessionsController@create');
$app->get('/test/{id}', 'TeacherController@pending_request');