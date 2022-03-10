<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {

        //Report megacategory
        Route::get('ReportMegacategory', 'Cornelio\Report\ReportMegacategoryController@ReportMegacategory')->name('Report.Megacategory');
        Route::get('/MegacategoriaReporte/{id}/{start}/{end}','Cornelio\Report\ReportMegacategoryController@getReportMegacategory')->name('reporte.cloud');

        //Report subcategory
        Route::get('ReportSubcategory', 'Cornelio\Report\ReportSubcategoryController@ReportSubcategory')->name('Report.Subcategory');
        Route::post('Megacategory', 'Cornelio\Report\ReportSubcategoryController@megacategory')->name('Report.ItemMegacategory');
        Route::post('ChartReport', 'Cornelio\Report\ReportSubcategoryController@chartReportePost')->name('Report.chartReportePost');
        Route::get('/SubcategoriaReporte/{sub}/{start}/{end}','Cornelio\Report\ReportSubcategoryController@getReportSubcategory')->name('Report.getReportSubcategory');
        Route::get('ExportTopics/{sub}/{start}/{end}', 'Cornelio\Report\ReportSubcategoryController@PDF_Topics')->name('Report.pdf');

        //MegacategoryReview
        Route::get('MegacategoryReview', 'Cornelio\Report\MegacategoryReviewController@Megacategory')->name('Review.Megacategory');
        Route::get('/MecategoryToday/{id}','Cornelio\Report\MegacategoryReviewController@MecategoryToday')->name('Review.Today');
        Route::get('/ValidateMegategory/{id}','Cornelio\Report\MegacategoryReviewController@ValidateMegategory')->name('Review.Validate');
        Route::post('AllUpdateScrap','Cornelio\Report\MegacategoryReviewController@AllUpdateScrap')->name('Review.AllUpdateScrap');
        Route::post('AllUpdate','Cornelio\Report\MegacategoryReviewController@AllUpdate')->name('Review.AllUpdate');
        Route::get('/ReportDetail/{post}/{sub}','Cornelio\Report\MegacategoryReviewController@ReportDetail')->name('Review.ReportDetail');
        Route::post('CloudPost','Cornelio\Report\MegacategoryReviewController@CloudPost')->name('Review.CloudPost');
        Route::post('ReportToday','Cornelio\Report\MegacategoryReviewController@ReportToday')->name('Review.ReportToday');
    });
});

    //Se encuentra a fuera ya que no será necesario tener registrado una compañia
    Route::post('Telegram','Cornelio\Report\MegacategoryReviewController@Telegram')->name('Review.Telegram');
    Route::post('Reclassify','Cornelio\Report\MegacategoryReviewController@Reclassify')->name('Review.Reclassify');
    Route::post('Declassify','Cornelio\Report\MegacategoryReviewController@Declassify')->name('Review.Declassify');

    //Report megacategory
    Route::post('ReportInteraction', 'Cornelio\Report\ReportMegacategoryController@ReportInteraction')->name('Report.Interaction');
    Route::post('ReportCloudComment', 'Cornelio\Report\ReportMegacategoryController@reportCloudComments')->name('Report.Comment');
    Route::post('ReportCloudCommentPost', 'Cornelio\Report\ReportMegacategoryController@reportCloudCommentsPost')->name('Report.CommentPost');
    Route::post('MessageRandom', 'Cornelio\Report\ReportMegacategoryController@messageRandom')->name('Report.MessageRandom');
    Route::post('ReportImpacto', 'Cornelio\Report\ReportMegacategoryController@ReportImpact')->name('Report.Impact');

    //Report MegacategoryReview
    Route::post('ReportWordCloud','Cornelio\Report\MegacategoryReviewController@ReportWordCloud')->name('Review.ReportWordCloud');
    Route::post('ReportImpactPost','Cornelio\Report\MegacategoryReviewController@ReportImpactPost')->name('Review.ReportImpactPost');
    Route::post('commentsRandom','Cornelio\Report\MegacategoryReviewController@commentsRandom')->name('Review.commentsRandom');

    Route::post('chartReporteInteraction', 'Cornelio\Report\ReportSubcategoryController@chartReporteInteraction')->name('Report.chartReporteInteraction');

    Route::post('ToDisable','Cornelio\Report\MegacategoryReviewController@ToDisable')->name('Review.ToDisable');


    Route::post('Subcategory', 'Cornelio\Report\ReportSubcategoryController@subcategory')->name('Report.ItemSubcategory');
