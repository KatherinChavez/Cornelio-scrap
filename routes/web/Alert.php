<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {
        
        //Alert
        Route::get('Alerts', 'Cornelio\Alert\AlertsController@index')->name('alerts.index');
        Route::post('Alerts/status', 'Cornelio\Alert\AlertsController@status')->name('alerts.status');
        Route::post('Alerts/statusOff', 'Cornelio\Alert\AlertsController@statusOff')->name('alerts.statusOff');
        Route::post('Alerts/notification', 'Cornelio\Alert\AlertsController@notification')->name('alerts.notification');
        Route::post('Alerts/notificationOff', 'Cornelio\Alert\AlertsController@notificationOff')->name('alerts.notificationOff');
        Route::post('Alerts/report', 'Cornelio\Alert\AlertsController@report')->name('alerts.report');
        Route::post('Alerts/reportOff', 'Cornelio\Alert\AlertsController@reportOff')->name('alerts.reportOff');
        Route::post('Alerts/consult', 'Cornelio\Alert\AlertsController@consult')->name('alerts.consult');
        Route::post('Alerts/sentiment', 'Cornelio\Alert\AlertsController@sentiment')->name('alerts.sentiment');
        Route::post('Alerts/sentimentOff', 'Cornelio\Alert\AlertsController@sentimentOff')->name('alerts.sentimentOff');

        Route::get('Analysis', 'Cornelio\Analysis\AnalysisController@index')->name('analysis.index');


        Route::get('Sync_Up/', 'Cornelio\Sync_Up\Sync_UpController@index')->name('sync_up.index');
        Route::post('Sync_Up/Reconnect', 'Cornelio\Sync_Up\Sync_UpController@Reconnect')->name('sync_up.Reconnect');
        Route::post('Sync_Up/Reboot', 'Cornelio\Sync_Up\Sync_UpController@Reboot')->name('sync_up.Reboot');


        Route::get('Bubles/', 'Cornelio\Alert\BublesController@index')->name('bubles.index');
        Route::post('Bubles/show', 'Cornelio\Alert\BublesController@show')->name('bubles.show');
        Route::get('Bubles/{numberWhatsapp}/edit', 'Cornelio\Alert\BublesController@edit')->name('bubles.edit');
        Route::put('Bubles/{numberWhatsapp}', 'Cornelio\Alert\BublesController@update')->name('bubles.update');


        Route::get('StatusMessage/', 'Cornelio\MessageStatus\MessageStatusController@index')->name('messageStatus.index');
        Route::get('StatusMessage/resend/{messageFb}', 'Cornelio\MessageStatus\MessageStatusController@resend')->name('messageStatus.resend');
    });
});