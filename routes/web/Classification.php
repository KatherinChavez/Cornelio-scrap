<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {

        //manage feeling
        Route::get('AdmSentiment_User', 'Cornelio\Classification\AdminSentimentController@index')->name('AdminSentiment_User.index');
        Route::post('Sentiment_User/store', 'Cornelio\Classification\AdminSentimentController@store')->name('AdminSentiment_user.store');
        Route::get('Sentiment_User/create', 'Cornelio\Classification\AdminSentimentController@create')->name('AdminSentiment_user.create');
        Route::put('Sentiment_User/{sentiment_user}', 'Cornelio\Classification\AdminSentimentController@update')->name('AdminSentiment_user.update');
        Route::get('Sentiment_User/{sentiment_user}/edit', 'Cornelio\Classification\AdminSentimentController@edit')->name('AdminSentiment_user.edit');
        Route::get('Sentiment_User/{sentiment_user}', 'Cornelio\Classification\AdminSentimentController@destroy')->name('AdminSentiment_user.destroy');
        Route::post('Sentiment_User/search', 'Cornelio\Classification\AdminSentimentController@search')->name('AdminSentiment_user.search');

        //AutoClassify
        Route::get('AutoClassify', 'Cornelio\Classification\AutoClassifyController@Auto_Classification')->name('Classification.Auto_Classification');
        Route::post('ClassificationSentiment', 'Cornelio\Classification\AutoClassifyController@classificationSentiment')->name('Classification.Sentiment');

        //Classification category
        Route::get('ClassifyCategory', 'Cornelio\Classification\ClassificationCategoryController@indexCategory')->name('ClassifyCategory.indexCategoria');
        Route::get('PostCategory', 'Cornelio\Classification\ClassificationCategoryController@PostCategory')->name('ClassifyCategory.PostCategory');
        Route::post('Category', 'Cornelio\Classification\ClassificationCategoryController@Category')->name('ClassifyCategory.Category');
        Route::post('SelectCategory', 'Cornelio\Classification\ClassificationCategoryController@SelectCategory')->name('ClassifyCategory.SelectCategory');
        Route::post('CountSelectCategory', 'Cornelio\Classification\ClassificationCategoryController@CountSelectCategory')->name('ClassifyCategory.CountSelectCategory');
        Route::post('subcategory', 'Cornelio\Classification\ClassificationCategoryController@subcategory')->name('ClassifyCategory.subcategory');
        Route::post('SelectMegacategory', 'Cornelio\Classification\ClassificationCategoryController@SelectMegacategory')->name('ClassifyCategory.SelectMegacategory');
        Route::post('selectSubcategory', 'Cornelio\Classification\ClassificationCategoryController@selectSubcategory')->name('ClassifyCategory.selectSubcategory');
        Route::post('classification', 'Cornelio\Classification\ClassificationCategoryController@classification')->name('ClassifyCategory.classification');
        Route::post('sub_Category', 'Cornelio\Classification\ClassificationCategoryController@sub_Category')->name('ClassifyCategory.sub_Category');
        Route::post('getClassification', 'Cornelio\Classification\ClassificationCategoryController@getClassification')->name('ClassifyCategory.getClassification');
        Route::get('CategorySentiment','Cornelio\Classification\ClassificationCategoryController@CategorySentiment')->name('ClassifyCategory.CategorySentiment');
        Route::post('SelectCategorySentiment','Cornelio\Classification\ClassificationCategoryController@SelectCategorySentiment')->name('ClassifyCategory.SelectCategorySentiment');
        Route::post('Posts','Cornelio\Classification\ClassificationCategoryController@Posts')->name('ClassifyCategory.Posts');
        Route::post('SentimentPost','Cornelio\Classification\ClassificationCategoryController@SentimentPost')->name('ClassifyCategory.SentimentPost');
        Route::post('countComment','Cornelio\Classification\ClassificationCategoryController@countComment')->name('ClassifyCategory.countComment');
        Route::post('comment','Cornelio\Classification\ClassificationCategoryController@comment')->name('ClassifyCategory.comment');
        Route::post('DeclassifyCategory','Cornelio\Classification\ClassificationCategoryController@DeclassifyCategory')->name('ClassifyCategory.DeclassifyCategory');
        Route::post('report','Cornelio\Classification\ClassificationCategoryController@report')->name('ClassifyCategory.report');
        Route::post('cloudReport','Cornelio\Classification\ClassificationCategoryController@cloudReport')->name('ClassifyCategory.cloudReport');
        Route::post('reactionCategoryCount','Cornelio\Classification\ClassificationCategoryController@reactionCategoryCount')->name('ClassifyCategory.reactionCategoryCount');
        Route::post('reactionCategory','Cornelio\Classification\ClassificationCategoryController@reactionCategory')->name('ClassifyCategory.reactionCategory');
        Route::post('postPage','Cornelio\Classification\ClassificationCategoryController@postPage')->name('ClassifyCategory.postPage');
        Route::post('TelegramCategory','Cornelio\Classification\ClassificationCategoryController@TelegramCategory')->name('ClassifyCategory.TelegramCategory');
        Route::post('SendCategory','Cornelio\Classification\ClassificationCategoryController@SendCategory')->name('ClassifyCategory.SendCategory');
        Route::post('WordComment','Cornelio\Classification\ClassificationCategoryController@WordComment')->name('ClassifyCategory.WordComment');
        Route::post('cloudPost','Cornelio\Classification\ClassificationCategoryController@cloudPost')->name('ClassifyCategory.cloudPost');

        
        //Individual fanpage post
        Route::get('clasification/post', 'Cornelio\Classification\InfoIndividualPageController@SelectInfoPage')->name('InfoPage.selectFanPage');
        Route::get('InfoPage', 'Cornelio\Classification\InfoIndividualPageController@InfoPage')->name('InfoPage.InfoPage');
        Route::post('getPage', 'Cornelio\Classification\InfoIndividualPageController@getPage')->name('InfoPage.getPage');
        Route::post('comparatorClassification', 'Cornelio\Classification\InfoIndividualPageController@comparatorClassification')->name('InfoPage.comparatorClassification');


        //Classification sentiment 
        Route::get('PageSentiment', 'Cornelio\Classification\SentimentController@pageSentiment')->name('ClassifyFeeling.pageSentiment');
        Route::post('page', 'Cornelio\Classification\SentimentController@page')->name('ClassifyFeeling.page');
        Route::get('selectSentiment', 'Cornelio\Classification\SentimentController@selectSentiment')->name('ClassifyFeeling.selectSentiment');
        Route::post('SentimentComment', 'Cornelio\Classification\SentimentController@SentimentComment')->name('ClassifyFeeling.SentimentComment');
        Route::post('personalizedFeeling', 'Cornelio\Classification\SentimentController@personalizedFeeling')->name('ClassifyFeeling.personalizedFeeling');
        Route::post('updateSentiment', 'Cornelio\Classification\SentimentController@updateSentiment')->name('ClassifyFeeling.updateSentiment');
        Route::post('statusSentiment', 'Cornelio\Classification\SentimentController@statusSentiment')->name('ClassifyFeeling.statusSentiment');
        Route::post('statusCheck', 'Cornelio\Classification\SentimentController@statusSentiment')->name('ClassifyFeeling.statusSentiment');
        Route::post('SentimentAll', 'Cornelio\Classification\SentimentController@Sentiment')->name('ClassifyFeeling.Sentiment');
        Route::post('check', 'Cornelio\Classification\SentimentController@check')->name('ClassifyFeeling.check');

        //Sentiment inbox
        Route::get('PageInbox', 'Cornelio\Classification\InboxSentimentController@pageInbox')->name('SentimentInbox.pageInbox');
        Route::get('SelectInbox', 'Cornelio\Classification\InboxSentimentController@selectInbox')->name('SentimentInbox.selectInbox');
        Route::post('Conversation', 'Cornelio\Classification\InboxSentimentController@conversation')->name('SentimentInbox.conversation');
        Route::post('Message','Cornelio\Classification\InboxSentimentController@message')->name('SentimentInbox.message');
        Route::post('sentimentInboxstore','Cornelio\Classification\InboxSentimentController@store')->name('SentimentInbox.store');
        Route::post('sentimentInbox','Cornelio\Classification\InboxSentimentController@sentimentInbox')->name('SentimentInbox.sentimentInbox');
        Route::post('status','Cornelio\Classification\InboxSentimentController@status')->name('SentimentInbox.status');    

        //Sentiment subcategory
        Route::get('SentimentSubCategory', 'Cornelio\Classification\SentimentSubcategoryController@sentimentSubCategory')->name('SentimentSub.sentimentSubCategory');
        Route::post('personalizedFeelingSub', 'Cornelio\Classification\SentimentSubcategoryController@personalizedFeelingSub')->name('SentimentSub.personalizedFeelingSub');
        Route::post('reactionPost', 'Cornelio\Classification\SentimentSubcategoryController@reactionPost')->name('SentimentSub.reactionPost');
        
        //Classification word
        Route::get('ClassificationWord', 'Cornelio\Classification\ClassificationWordController@index')->name('ClassificationWord.index');
        Route::post('ClassificationWord/store', 'Cornelio\Classification\ClassificationWordController@store')->name('ClassificationWord.store');
        Route::get('ClassificationWord/create', 'Cornelio\Classification\ClassificationWordController@create')->name('ClassificationWord.create');
        Route::put('ClassificationWord/{compare}', 'Cornelio\Classification\ClassificationWordController@update')->name('ClassificationWord.update');
        Route::get('ClassificationWord/{compare}/edit', 'Cornelio\Classification\ClassificationWordController@edit')->name('ClassificationWord.edit');
        Route::get('ClassificationWord/{compare}', 'Cornelio\Classification\ClassificationWordController@destroy')->name('ClassificationWord.destroy');
        Route::post('ClassificationWord/search', 'Cornelio\Classification\ClassificationWordController@search')->name('ClassificationWord.search');

        //Sentiment word
        Route::get('SentimentWord', 'Cornelio\Classification\SentimentWordController@index')->name('SentimentWord.index');
        Route::post('SentimentWord/store', 'Cornelio\Classification\SentimentWordController@store')->name('SentimentWord.store');
        Route::post('SentimentWord/edit', 'Cornelio\Classification\SentimentWordController@edit')->name('SentimentWord.edit');
        Route::post('SentimentWord/update', 'Cornelio\Classification\SentimentWordController@update')->name('SentimentWord.update');
        Route::get('SentimentWord/delete/{word}', 'Cornelio\Classification\SentimentWordController@destroy')->name('SentimentWord.destroy');

    });
});