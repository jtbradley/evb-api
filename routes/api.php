<?php

Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::get('/user', 'AuthController@user');
Route::post('/logout', 'AuthController@logout');

Route::group(['prefix' => 'topics'], function () {
    Route::post('/', 'TopicController@store')->middleware('auth:api');
    Route::get('/', 'TopicController@index');
    Route::get('/{topic}', 'TopicController@show');
    Route::patch('/{topic}', 'TopicController@update')->middleware('auth:api');
    Route::delete('/{topic}', 'TopicController@destroy')->middleware('auth:api');
    //post route groups
    Route::group(['prefix' => '/{topic}/posts'], function () {
        Route::get('/{post}', 'PostController@show');
        Route::post('/', 'PostController@store')->middleware('auth:api');
        Route::patch('/{post}', 'PostController@update')->middleware('auth:api');
        Route::delete('/{post}', 'PostController@destroy')->middleware('auth:api');
        //likes
        Route::group(['prefix' => '/{post}/likes'], function () {
            Route::post('/', 'PostLikeController@store')->middleware('auth:api');
        });
    });
});

Route::group(['prefix' => 'projects'], function () {
    Route::get('/', 'ProjectController@index')->middleware('auth:api');
    Route::get('/{project}', 'ProjectController@show')->middleware('auth:api');
    Route::post('/', 'ProjectController@store')->middleware('auth:api');
    Route::delete('/{project}', 'ProjectController@destroy')->middleware('auth:api');
});

Route::group(['prefix' => 'tasks'], function () {
    Route::delete('/{task}', 'TaskController@close')->middleware('auth:api');
    Route::put('/{task}', 'TaskController@update')->middleware('auth:api');
});
