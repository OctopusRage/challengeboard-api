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
       $app->get('/pending_request', 'TeacherController@pending_request');
       $app->get('/pending_request/{id}', 'TeacherController@pending_request_by_id');
       $app->post('/approve_request/{id}', 'TeacherController@approve_request');
    }); 
    $app->group(['prefix' => 'subjects'], function () use ($app) {
       $app->post('/', 'SubjectController@create');
       $app->get('/{id}', 'SubjectController@get');
    });
    $app->group(['prefix' => 'schedules'], function () use ($app) {
       $app->post('/', 'ScheduleController@create');
       $app->get('/{id}', 'ScheduleController@get');
    });
    $app->group(['prefix' => 'trophies'], function () use ($app) {
       $app->post('/', 'TrophyController@create');
    });
});

$app->group(['prefix' => 'challenges'], function () use ($app) {
    $app->get('/', 'ChallengesController@index');
    $app->post('/join/{id}', 'ChallengesController@join');
});

$app->group(['prefix' => 'trophies'], function () use ($app) {
    $app->get('/', 'TrophyController@get');
    $app->get('/{id}', 'TrophyController@get_by_id');
});

$app->post('/login', 'SessionsController@create');
