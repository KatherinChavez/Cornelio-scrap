<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {

        //ClassifyTopics
        Route::get('Telephone/index', 'Cornelio\Telephone\TelephoneController@index')->name('Telephone.index');
        Route::get('/Telephone/syncTelephone', 'Cornelio\Telephone\TelephoneController@syncNumber')->name('Telephone.syncTelephone');
        Route::post('/Telephone/store', 'Cornelio\Telephone\TelephoneController@store')->name('Telephone.store');
        Route::post('/Telephone/edit', 'Cornelio\Telephone\TelephoneController@edit')->name('Telephone.edit');
        Route::post('/Telephone/update', 'Cornelio\Telephone\TelephoneController@update')->name('Telephone.update');
        Route::get('Telephone/delete/{numberWhatsapp}', 'Cornelio\Telephone\TelephoneController@destroy')->name('Telephone.destroy');
        Route::get('Telephone/ExportTop/', 'Cornelio\Telephone\TelephoneController@exportPdf')->name('Telephone.exportPdf');
        Route::post('/Telephone/sendPdf', 'Cornelio\Telephone\TelephoneController@sendPdf')->name('Telephone.sendPdf');
        Route::get('Telephone/typeReportPdf/{numberWhatsapp}', 'Cornelio\Telephone\TelephoneController@typeReportPdf')->name('Telephone.typeReportPdf');
        Route::get('Telephone/PdfTopTen/{numberWhatsapp}', 'Cornelio\Telephone\TelephoneController@PdfTopTen')->name('Telephone.PdfTopTen');

    });
});