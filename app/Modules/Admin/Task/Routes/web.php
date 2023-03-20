<?php

Route::group(['prefix' => 'tasks', 'middleware' => []], function () {
    Route::get('/', 'TaskController@index')->name('tasks.index');
    Route::get('/create', 'TaskController@create')->name('tasks.create');
    Route::post('/', 'TaskController@store')->name('tasks.store');
    Route::get('/{task}', 'TaskController@show')->name('tasks.read');
    Route::get('/edit/{task}', 'TaskController@edit')->name('tasks.edit');
    Route::put('/{task}', 'TaskController@update')->name('tasks.update');
    Route::delete('/{task}', 'TaskController@destroy')->name('tasks.delete');
});