<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {
        Route::get('ComparatorPage', 'Cornelio\Comparator\ComparatorController@ComparatorPage')->name('Comparator.Megacategory');
        Route::post('ComparatorPage', 'Cornelio\Comparator\ComparatorController@CheckPage')->name('Comparator.CheckPage');
        Route::post('Fans', 'Cornelio\Comparator\ComparatorController@Fans')->name('Comparator.Fans');
        Route::post('Talking', 'Cornelio\Comparator\ComparatorController@Talking')->name('Comparator.Talking');
        Route::post('DailyPost', 'Cornelio\Comparator\ComparatorController@DailyPost')->name('Comparator.DailyPost');
        Route::post('Comments', 'Cornelio\Comparator\ComparatorController@Comments')->name('Comparator.Comments');
        Route::post('TypePost', 'Cornelio\Comparator\ComparatorController@TypePost')->name('Comparator.TypePost');
        Route::post('Engagement', 'Cornelio\Comparator\ComparatorController@Engagement')->name('Comparator.Engagement');
        Route::post('TopPost', 'Cornelio\Comparator\ComparatorController@TopPost')->name('Comparator.TopPost');
        Route::post('getDatos', 'Cornelio\Comparator\ComparatorController@getDatos')->name('Comparator.getDatos');

        Route::get('Competence/', 'Cornelio\Competence\CompetenceController@index')->name('Competence.index');
        Route::post('Competence/store', 'Cornelio\Competence\CompetenceController@store')->name('Competence.store');
        Route::get('Competence/delete/{scraps}', 'Cornelio\Competence\CompetenceController@delete')->name('Competence.delete');
        Route::post('Competence/cloudWordPage', 'Cornelio\Competence\CompetenceController@cloudWordPage')->name('Competence.cloudWordPage');
        Route::post('Competence/FeelingPageComments', 'Cornelio\Competence\CompetenceController@FeelingPageComments')->name('Competence.FeelingPageComments');
        Route::post('Competence/StaticsPost', 'Cornelio\Competence\CompetenceController@StaticsPost')->name('Competence.StaticsPost');
        Route::post('Competence/StaticsComments', 'Cornelio\Competence\CompetenceController@StaticsComments')->name('Competence.StaticsComments');
        Route::post('Competence/TopPagePost', 'Cornelio\Competence\CompetenceController@TopPagePost')->name('Competence.TopPagePost');
        Route::post('Competence/GetInformation', 'Cornelio\Competence\CompetenceController@getInformation')->name('Competence.getInformation');
    });
});