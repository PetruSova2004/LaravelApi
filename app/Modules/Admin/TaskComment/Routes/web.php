<?php

Route::group(['prefix' => 'task-comments', 'middleware' => []], function () {
    Route::get('/', 'TaskCommentController@index')->name('task-comments.index');
    Route::get('/create', 'TaskCommentController@create')->name('task-comments.create');
    Route::post('/', 'TaskCommentController@store')->name('task-comments.store');
    Route::get('/{taskComment}', 'TaskCommentController@show')->name('task-comments.read');
    Route::get('/edit/{taskComment}', 'TaskCommentController@edit')->name('task-comments.edit');
    Route::put('/{taskComment}', 'TaskCommentController@update')->name('task-comments.update');
    Route::delete('/{taskComment}', 'TaskCommentController@destroy')->name('task-comments.delete');
});