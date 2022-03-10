<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {
        Route::get('Cron', 'Cornelio\Cron\CronController@index')->name('Cron.index');
        Route::get('CreateCron', 'Cornelio\Cron\CronController@create')->name('Cron.create');
        Route::post('cron/store','Cornelio\Cron\CronController@store')->name('Cron.store');
        Route::get('cron/{cron}/edit','Cornelio\Cron\CronController@edit')->name('Cron.edit');
        Route::put('cron/{cron}','Cornelio\Cron\CronController@update')->name('Cron.update');
        Route::get('Cron/delete/{Cron}','Cornelio\Cron\CronController@delete')->name('Cron.delete');
        Route::get('execution', 'Cornelio\Cron\CronController@execution')->name('Cron.execution');
        Route::get('pruebaScrap', 'Cornelio\Cron\CronController@pruebaScrap')->name('Cron.pruebaScrap');
        Route::get('cron/{cron}/Stop','Cornelio\Cron\CronController@stop')->name('Cron.stop');
        Route::get('cron/{cron}/play','Cornelio\Cron\CronController@play')->name('Cron.play');

    });
});