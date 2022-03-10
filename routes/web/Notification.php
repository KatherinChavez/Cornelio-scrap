<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::middleware('company')->group(function () {
        Route::post('NotificationClassification', 'Notification\ReportNotificationController@NotificationClassify')->name('Notification.Classification');
        Route::post('NotificationWhatsapp', 'Notification\ReportNotificationController@SendWhatsapp')->name('Notification.Whatsapp');
        Route::post('NotificationTelegram', 'Notification\ReportNotificationController@SendTelegram')->name('Notification.Telegram');
        Route::get('NotificationMail', 'Notification\ReportNotificationController@SendMail')->name('Notification.PDF');
    });

});
//Se encuentra a fuera ya que no será necesario tener registrado una compañia
Route::get('/ManagementPost/{com}/{post}/{sub}', 'Notification\ReportNotificationController@ManagementView')->name('Notification.ManagementView');
Route::post('NotificationMegacategory', 'Notification\ReportNotificationController@megacategoryNotifications')->name('Notification.Megacategory');
Route::get('SendReportLink/{id}/{start}/{end}', 'Notification\ReportNotificationController@SendReportLink')->name('Notification.SendReportLink');

Route::get('analysisLink/{id}/', 'Cornelio\AnalysisTop\AnalysisTopController@analysisLink')->name('Notification.analysisLink');
Route::post('analysisCloud/', 'Cornelio\AnalysisTop\AnalysisTopController@analysisCloud')->name('Notification.analysisCloud');
Route::post('analysisFeeling/', 'Cornelio\AnalysisTop\AnalysisTopController@analysisFeeling')->name('Notification.analysisFeeling');
Route::get('topicsComparator/{sub}/{start}/{end}', 'Cornelio\AnalysisTop\AnalysisTopController@topicsComparator')->name('Notification.topicsComparator');
Route::post('topicsComparatorCloud/', 'Cornelio\AnalysisTop\AnalysisTopController@topicsComparatorCloud')->name('Notification.topicsComparatorCloud');
Route::get('BublesContent/{comp}/{contents_encode}', 'Cornelio\AnalysisTop\AnalysisTopController@BublesContent')->name('Notification.BublesContent');
Route::post('wordBublesContent/', 'Cornelio\AnalysisTop\AnalysisTopController@wordBublesContent')->name('Notification.wordBublesContent');