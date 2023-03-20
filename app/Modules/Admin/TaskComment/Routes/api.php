<?php

Route::group(['prefix' => 'task-comments', 'middleware' => []], function () {
    Route::get('/', 'Api\TaskCommentController@index')->name('api.task-comments.index');
    Route::post('/', 'Api\TaskCommentController@store')->name('api.task-comments.store');
    Route::get('/{taskComment}', 'Api\TaskCommentController@show')->name('api.task-comments.read');
    Route::put('/{taskComment}', 'Api\TaskCommentController@update')->name('api.task-comments.update');
    Route::delete('/{taskComment}', 'Api\TaskCommentController@destroy')->name('api.task-comments.delete');
});