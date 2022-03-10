<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {

        //ClassifyTopics
        Route::get('ClassifyTopics/index', 'Cornelio\ClassifyTopics\ClassifyTopicsController@index')->name('ClassifyTopics.index');
        Route::post('ClassifyTopics/classify', 'Cornelio\ClassifyTopics\ClassifyTopicsController@classify')->name('ClassifyTopics.classify');
        Route::post('ClassifyTopics/edit', 'Cornelio\ClassifyTopics\ClassifyTopicsController@edit')->name('ClassifyTopics.edit');
        Route::post('ClassifyTopics/update', 'Cornelio\ClassifyTopics\ClassifyTopicsController@update')->name('ClassifyTopics.update');
        Route::get('ClassifyTopics/delete/{classification_Category}', 'Cornelio\ClassifyTopics\ClassifyTopicsController@destroy')->name('ClassifyTopics.destroy');

    });
});