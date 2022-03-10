<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {


        //SCRAP TWITTER
        Route::get('/TwitterScrap', 'Cornelio\TwitterScraps\ScrapAllTwitterController@index')->name('twitterScrap.index');
        Route::post('/TwitterScrap/validate', 'Cornelio\TwitterScraps\ScrapAllTwitterController@validateUser')->name('twitterScrap.validate');
        Route::post('/TwitterScrap/scrapInfoTwitter', 'Cornelio\TwitterScraps\ScrapAllTwitterController@scrapInfoTwitter')->name('twitterScrap.scrapInfoTwitter');
        Route::post('/TwitterScrap/scrapTweet', 'Cornelio\TwitterScraps\ScrapAllTwitterController@scrapTweet')->name('twitterScrap.scrapTweet');
        Route::post('/TwitterScrap/commentTweet', 'Cornelio\TwitterScraps\ScrapAllTwitterController@commentTweet')->name('twitterScrap.commentTweet');
        Route::post('/TwitterScrap/reactionTweet', 'Cornelio\TwitterScraps\ScrapAllTwitterController@reactionTweet')->name('twitterScrap.reactionTweet');

        //SCRAP CONTENT
        Route::get('/TwitterScrap/Content', 'Cornelio\TwitterScraps\ScrapContentController@index')->name('scrapContent.index');
        Route::post('/TwitterScrap/Content/page', 'Cornelio\TwitterScraps\ScrapContentController@scrapTweetContent')->name('scrapContent.page');
        Route::post('/TwitterScrap/Content/reaction', 'Cornelio\TwitterScraps\ScrapContentController@scrapTweetReaction')->name('scrapContent.reaction');

        //CONTENT TWITTER
        Route::get('/Twitter', 'Cornelio\TwitterContents\TwitterContentController@index')->name('twitter.index');
        Route::get('/Twitter/syncTwitter', 'Cornelio\TwitterContents\TwitterContentController@syncTwitter')->name('twitter.syncTwitter');
        Route::post('/Twitter/get_info', 'Cornelio\TwitterContents\TwitterContentController@get_info')->name('twitter.get_info');
        Route::post('/Twitter/store', 'Cornelio\TwitterContents\TwitterContentController@store')->name('twitter.store');
        Route::post('/Twitter/saveScrapTwitter', 'Cornelio\TwitterContents\TwitterContentController@saveScrapTwitter')->name('twitter.saveScrapTwitter');
        Route::post('/Twitter/update', 'Cornelio\TwitterContents\TwitterContentController@update')->name('twitter.update');
        Route::post('/Twitter/destroyScrap', 'Cornelio\TwitterContents\TwitterContentController@destroyScrap')->name('twitter.destroyScrap');
        Route::put('Twitter/{twitterContent}', 'Cornelio\TwitterContents\TwitterContentController@destroyCategory')->name('twitter.destroyCategory');

        //CLASSIFICATION COMMENT
        Route::get('Classification/SelectComment/Twitter', 'Cornelio\TwitterClassification\CommentsController@indexComment')->name('classificarionTwitter.indexComment');
        Route::get('Classification/Comment/Twitter', 'Cornelio\TwitterClassification\CommentsController@getComment')->name('classificarionTwitter.getComment');
        Route::post('Classification/sentiment/Twitter', 'Cornelio\TwitterClassification\CommentsController@classificationSentiment')->name('classificarionTwitter.sentiment');
        Route::post('Classification/status/Twitter', 'Cornelio\TwitterClassification\CommentsController@statusSentiment')->name('classificarionTwitter.status');
        Route::post('Classification/getSentiment/Twitter', 'Cornelio\TwitterClassification\CommentsController@getSentiment')->name('classificarionTwitter.getSentiment');

        //CLASSIFICATION TWEET
        Route::get('Classification/Select/Twitter', 'Cornelio\TwitterClassification\InformationTwitterController@indexSelect')->name('classificarionTwitter.select');
        Route::get('Classification/Page/Twitter', 'Cornelio\TwitterClassification\InformationTwitterController@pageTwitter')->name('classificarionTwitter.page');
        Route::get('Classification/Content/Twitter', 'Cornelio\TwitterClassification\InformationTwitterController@contentTwitter')->name('classificarionTwitter.content');
        Route::post('Classification/classificationTweet/Twitter', 'Cornelio\TwitterClassification\InformationTwitterController@classificationTweet')->name('classificarionTwitter.classificationTweet');
        Route::post('Classification/sendWhatsappClassification/Twitter', 'Cornelio\TwitterClassification\InformationTwitterController@sendWhatsappClassification')->name('classificarionTwitter.sendWhatsappClassification');

        //CLASSIFICATION TOPICS
        Route::get('Classification/selectTopics/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@indexTopics')->name('classificarionTwitter.selectTopics');
        Route::get('Classification/getTopics/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@getTopics')->name('classificarionTwitter.getTopics');
        Route::post('Classification/updateReaction/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@updateReaction')->name('classificarionTwitter.updateReaction');
        Route::post('Classification/updateComment/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@updateComment')->name('classificarionTwitter.updateComment');
        Route::post('Classification/getSentimentTweet/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@getSentimentTweet')->name('classificarionTwitter.getSentimentTweet');
        Route::post('Classification/tweetComment/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@tweetComment')->name('classificarionTwitter.tweetComment');
        Route::post('Classification/getInformation/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@getInformation')->name('classificarionTwitter.getInformation');
        Route::post('Classification/cloudInformation/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@cloudInformation')->name('classificarionTwitter.cloudInformation');
        Route::post('Classification/commentInformation/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@commentInformation')->name('classificarionTwitter.commentInformation');
        Route::post('Classification/tweetClassification/Twitter', 'Cornelio\TwitterClassification\ClassificationTopicsController@tweetClassification')->name('classificarionTwitter.tweetClassification');

        //MENTION TWITTER
        Route::get('Mentions/Page', 'Cornelio\TwitterMention\MentionController@indexPage')->name('mention.indexPage');
        Route::get('Mentions/Data', 'Cornelio\TwitterMention\MentionController@getData')->name('mention.getData');

        //CLASSIFICACTION CONTENT TWITTER
        Route::get ('ClassifyTwitter/index', 'Cornelio\TwitterClassification\ClassifyTwitterController@index')->name('ClassifyTwitter.index');
        Route::post('ClassifyTwitter/classify', 'Cornelio\TwitterClassification\ClassifyTwitterController@classify')->name('ClassifyTwitter.classify');
        Route::post('ClassifyTwitter/edit', 'Cornelio\TwitterClassification\ClassifyTwitterController@edit')->name('ClassifyTwitter.edit');
        Route::post('ClassifyTwitter/update', 'Cornelio\TwitterClassification\ClassifyTwitterController@update')->name('ClassifyTwitter.update');
        Route::get ('ClassifyTwitter/delete/{twitterClassification}', 'Cornelio\TwitterClassification\ClassifyTwitterController@destroy')->name('ClassifyTwitter.destroy');

    });
});

//CLASIFICACION TWITTER
Route::get('/ManagementTweet/{com}/{post}/{sub}', 'Cornelio\TwitterClassification\ClassificationTweetController@ManagementTweet')->name('classificationTweet.ManagementTweet');
Route::post('/ToDisableTwitter/', 'Cornelio\TwitterClassification\ClassificationTweetController@ToDisableTwitter')->name('classificationTweet.ToDisableTwitter');
Route::post('/Telegram_sendTwitter/', 'Cornelio\TwitterClassification\ClassificationTweetController@Telegram_sendTwitter')->name('classificationTweet.Telegram_sendTwitter');
Route::post('/Whatsapp_sendTwitter/', 'Cornelio\TwitterClassification\ClassificationTweetController@Whatsapp_sendTwitter')->name('classificationTweet.Whatsapp_sendTwitter');
Route::post('/ReclassifyTwitter/', 'Cornelio\TwitterClassification\ClassificationTweetController@ReclassifyTwitter')->name('classificationTweet.ReclassifyTwitter');
Route::post('/DeclassifyTweet/', 'Cornelio\TwitterClassification\ClassificationTweetController@DeclassifyTweet')->name('classificationTweet.DeclassifyTweet');

