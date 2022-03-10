<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('{company}')->middleware('company')->group(function () {
        
        //Search
        Route::get('Search', 'Cornelio\Search\SearchWordsController@index')->name('Search.Words');
        Route::post('search/words', 'Cornelio\Search\SearchWordsController@search')->name('Search.searchWords');

        //Search user 
        Route::get('user', 'Cornelio\Search\SearchUserController@indexUser')->name('Search.user');
        Route::post('search/User', 'Cornelio\Search\SearchUserController@searchUser')->name('Search.searchUser');
    });
});
    