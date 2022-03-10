<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {

        //Statistics Page
        Route::get('SelectStatisticsPage', 'Cornelio\Statistics\StatisticsPageController@SelectStaticsPage')->name('Statistics.page');
        Route::post('StatisticsPage', 'Cornelio\Statistics\StatisticsPageController@StaticsPage')->name('Statistics.StatisticsPage');

        //Statistics Subcategory
        Route::get('SelectStatisticsSubCategory', 'Cornelio\Statistics\StatisticsSubCategoryController@SelectStaticsSubC')->name('Statistics.subcategoria');
        Route::post('StatisticsSubC', 'Cornelio\Statistics\StatisticsSubCategoryController@StaticsSubC')->name('Statistics.StatisticsSubC');
        Route::post('getSubC', 'Cornelio\Statistics\StatisticsSubCategoryController@getSubC')->name('Statistics.getSubC');

        //Statistics Interaction Subcategory
        Route::get('SelectStatisticsInteraction', 'Cornelio\Statistics\StatisticsInteractionController@SelectStaticsInteraction')->name('Statistics.selectInteraction');
        Route::post('StatisticsInteraction', 'Cornelio\Statistics\StatisticsInteractionController@StaticsInteraction')->name('Statistics.Interaction');
        Route::post('StaticsInteractionReaction', 'Cornelio\Statistics\StatisticsInteractionController@StaticsInteractionReaction')->name('Statistics.InteractionR');
        Route::post('StaticsInteractionComment', 'Cornelio\Statistics\StatisticsInteractionController@StaticsInteractionComment')->name('Statistics.InteractionC');

        //Sentiment Interaction
        Route::get('SentimentInteraction', 'Cornelio\Statistics\SentimentInteractionController@SelectTopics')->name('Statistics.SelectTopics');
        Route::post('SentimentInteraction/getTopics', 'Cornelio\Statistics\SentimentInteractionController@getTopics')->name('Statistics.getTopics');


    });
});