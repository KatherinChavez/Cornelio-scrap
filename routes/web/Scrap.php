<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::middleware('company')->group(function () {

        //Scrap Page
        Route::get('scraps', 'Cornelio\Scrap\ScrapPageController@index')->name('scrapsPage.index');
        Route::post('scraps/Save', 'Cornelio\Scrap\ScrapPageController@saveScrap')->name('scrapsPage.saveScrap');
        Route::post('scraps/delete', 'Cornelio\Scrap\ScrapPageController@delete')->name('scrapsPage.delete');
        Route::post('scraps/SaveValidation', 'Cornelio\Scrap\ScrapPageController@ScrapValidation')->name('scrapsPage.ScrapValidation');
        Route::get('scraps/Crud', 'Cornelio\Scrap\ScrapPageController@indexCRUD')->name('scrapsPage.indexCRUD');
        Route::delete('scraps/{scraps}', 'Cornelio\Scrap\ScrapPageController@destroy')->name('scrapsPage.destroy');
        Route::get('scraps/{scraps}', 'Cornelio\Scrap\ScrapPageController@showCRUD')->name('scrapsPage.showCRUD');

        //Scrap Post
        Route::get('scrapPost', 'Cornelio\Scrap\ScrapsPostController@index')->name('scrapsPost.index');
        Route::post('actionScrap', 'Cornelio\Scrap\ScrapsPostController@getPost')->name('scrapsPost.getPost');
        Route::post('scrapsPost', 'Cornelio\Scrap\ScrapsPostController@scrapPost')->name('scrapsPost.scrapPost');
        Route::post('scrapsComments', 'Cornelio\Scrap\ScrapsPostController@scrapsComments')->name('scrapsPost.scrapsComments');
        Route::post('ScrapReaction', 'Cornelio\Scrap\ScrapsPostController@ScrapReaction')->name('scrapsPost.ScrapReaction');
        Route::post('wordClassification', 'Cornelio\Scrap\ScrapsPostController@wordClassification')->name('scrapsPost.wordClassification');
        Route::post('commentsFeeling', 'Cornelio\Scrap\ScrapsPostController@commentsFeeling')->name('scrapsPost.commentsFeeling');


        //Scrap all
        Route::get('SelectPage', 'Cornelio\Scrap\ScrapsAllController@selectPage')->name('scrapsAll.selectPage');
        Route::post('infoPage', 'Cornelio\Scrap\ScrapsAllController@informationPage')->name('scrapsAll.infoPage');
        Route::get('ScrapAll', 'Cornelio\Scrap\ScrapsAllController@scrapAll')->name('scrapsAll.scrapAll');
        Route::post('getComments', 'Cornelio\Scrap\ScrapsAllController@getComments')->name('scrapsAll.getComments');
        Route::get('commentsAll', 'Cornelio\Scrap\ScrapsAllController@commentsAll')->name('scraps.commentsAll');
        Route::post('getPost', 'Cornelio\Scrap\ScrapsAllController@getPost')->name('scrapsAll.getPost');
        Route::post('Fan', 'Cornelio\Scrap\ScrapsAllController@fan')->name('scrapsAll.fan');
        Route::post('scrapReaction', 'Cornelio\Scrap\ScrapsAllController@ScrapReaction')->name('scrapsAll.scrapReacciones');

        //Last 100
        Route::get('LastScrap', 'Cornelio\Scrap\Last100Controller@ScrapLast')->name('scrapsLast.ScrapLast');
        Route::post('Last', 'Cornelio\Scrap\Last100Controller@lastPost')->name('scrapsLast.lastPost');
        Route::post('Pages', 'Cornelio\Scrap\Last100Controller@page')->name('scrapsLast.page');

        //Last 100 category
        Route::get('ScrapCategory', 'Cornelio\Scrap\LastCategoryController@scrapCategory')->name('scrapsCategoty.selectCategoria');
        Route::post('UpdateReaction', 'Cornelio\Scrap\LastCategoryController@updateReaction')->name('scrapsCategoty.updateReactions');
        Route::post('PageCategory', 'Cornelio\Scrap\LastCategoryController@pageCategory')->name('scrapsCategoty.pageCategory');
        Route::post('PostCategory', 'Cornelio\Scrap\LastCategoryController@postCategory')->name('scrapsCategoty.postCategory');
        Route::post('CommentCategory', 'Cornelio\Scrap\LastCategoryController@comments')->name('scrapsCategoty.commentCategory');

        //Scrap inbox
        Route::get('ScrapSelectInbox','Cornelio\Scrap\ScrapInboxController@selectInbox')->name('scrapsInbox.ScrapSelectInbox');
        Route::post('ScrapInbox','Cornelio\Scrap\ScrapInboxController@scrapInbox')->name('scrapsInbox.ScrapInbox');
    });
});
