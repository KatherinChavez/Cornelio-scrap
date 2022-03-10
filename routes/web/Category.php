<?php

use Illuminate\Support\Facades\Route;

Route::post('Contact', 'Cornelio\Category\NumberWhatsappController@Contact')->name('whatsapp.Contact');


Route::middleware(['auth'])->group(function () {
    Route::middleware('company')->group(function () {

        //Tema
        Route::get('/topics/index', 'Cornelio\Topics\TopicsController@index')->name('topics.index');
        Route::get('/topics/syncTopics', 'Cornelio\Topics\TopicsController@syncTopics')->name('topics.syncTopics');
        Route::post('/topics/store', 'Cornelio\Topics\TopicsController@store')->name('topics.store');
        Route::post('/topics/update', 'Cornelio\Topics\TopicsController@update')->name('topics.update');
        Route::post('/topics/delete', 'Cornelio\Topics\TopicsController@delete')->name('topics.delete');
        Route::post('/topics/deleteWord', 'Cornelio\Topics\TopicsController@deleteWord')->name('topics.deleteWord');
        Route::get('/topics/deleteTopic/{id}', 'Cornelio\Topics\TopicsController@deleteTopic')->name('topics.deleteTopic');


        //category
        Route::get('contenido', 'Cornelio\Category\PageCategoryController@index')->name('Category.index');
        Route::post('Category/store', 'Cornelio\Category\PageCategoryController@store')->name('Category.store');
        Route::get('Category/create', 'Cornelio\Category\PageCategoryController@create')->name('Category.create');
        Route::post('Category/update', 'Cornelio\Category\PageCategoryController@update')->name('Category.update');
        Route::get('Category/{categorias}/edit', 'Cornelio\Category\PageCategoryController@edit')->name('Category.edit');
        Route::put('Category/{categorias}', 'Cornelio\Category\PageCategoryController@destroy')->name('Category.destroy');
        Route::post('Category/search', 'Cornelio\Category\PageCategoryController@search')->name('Category.search');
        Route::post('Category/theme', 'Cornelio\Category\PageCategoryController@showTheme')->name('Category.showTheme');
        Route::get('Subcategory/{subcategory}', 'Cornelio\Category\PageCategoryController@destroyTheme')->name('Subcategory.destroy');


        //Number whatsapp
        Route::get('whatsapp', 'Cornelio\Category\NumberWhatsappController@index')->name('whatsapp.index');
        Route::post('whatsapp/store', 'Cornelio\Category\NumberWhatsappController@store')->name('whatsapp.store');
        Route::get('whatsapp/create', 'Cornelio\Category\NumberWhatsappController@create')->name('whatsapp.create');
        Route::put('whatsapp/{whatsapp}', 'Cornelio\Category\NumberWhatsappController@update')->name('whatsapp.update');
        Route::get('whatsapp/{whatsapp}/edit', 'Cornelio\Category\NumberWhatsappController@edit')->name('whatsapp.edit')->middleware('can:whatsapp.edit');
        Route::post('whatsapp/ShowNumber', 'Cornelio\Category\NumberWhatsappController@ShowNumber')->name('whatsapp.ShowNumber')->middleware('can:whatsapp.ShowNumber');
        //Route::delete('whatsapp/{whatsapp}', 'Cornelio\Category\NumberWhatsappController@destroy')->name('whatsapp.destroy')->middleware('can:whatsapp.destroy');
        Route::get('whatsapp/{whatsapp}', 'Cornelio\Category\NumberWhatsappController@destroy')->name('whatsapp.destroy')->middleware('can:whatsapp.destroy');

        //MegaCategory
        Route::get('Megacategory', 'Cornelio\Category\MegaCategoryController@index')->name('megacategory.index');
        Route::post('Megacategory/store', 'Cornelio\Category\MegaCategoryController@store')->name('megacategory.store');
        Route::get('Megacategory/create', 'Cornelio\Category\MegaCategoryController@create')->name('megacategory.create');
        Route::put('Megacategory/{megacategorias}', 'Cornelio\Category\MegaCategoryController@update')->name('megacategory.update');
        Route::get('Megacategory/{megacategorias}/edit', 'Cornelio\Category\MegaCategoryController@edit')->name('megacategory.edit');
        Route::get('Megacategory/{megacategorias}', 'Cornelio\Category\MegaCategoryController@destroy')->name('megacategory.destroy');
        Route::post('Megacategory/search', 'Cornelio\Category\MegaCategoryController@search')->name('megacategory.search');


        //SubCategory
        Route::post('Subcategory/get', 'Cornelio\Category\SubCategoryController@get')->name('subcategorias.get');
        Route::get('Subcategory', 'Cornelio\Category\SubCategoryController@index')->name('subcategorias.index');
        Route::post('Subcategory/store', 'Cornelio\Category\SubCategoryController@store')->name('subcategory.store');
        Route::get('Subcategory/create', 'Cornelio\Category\SubCategoryController@create')->name('subcategory.create');
        Route::put('Subcategory/{subcategorias}', 'Cornelio\Category\SubCategoryController@update')->name('subcategory.update');
        Route::get('Subcategory/{subcategorias}/edit', 'Cornelio\Category\SubCategoryController@edit')->name('subcategory.edit');
        Route::get('Subcategory/{subcategorias}', 'Cornelio\Category\SubCategoryController@destroy')->name('subcategory.destroy');
        Route::post('Subcategory/search', 'Cornelio\Category\SubCategoryController@search')->name('subcategory.search');

        //tags
        Route::get('TagsComment', 'Cornelio\Category\TagsCommentController@index')->name('tags.index');
        Route::post('TagsComment/store', 'Cornelio\Category\TagsCommentController@store')->name('tags.store');
        Route::get('TagsComment/create', 'Cornelio\Category\TagsCommentController@create')->name('tags.create');
        Route::put('TagsComment/{tags}', 'Cornelio\Category\TagsCommentController@update')->name('tags.update');
        Route::get('TagsComment/{tags}/edit', 'Cornelio\Category\TagsCommentController@edit')->name('tags.edit');
        Route::get('TagsComment/{tags}', 'Cornelio\Category\TagsCommentController@destroy')->name('tags.destroy');
        Route::post('TagsComment/search', 'Cornelio\Category\TagsCommentController@search')->name('tags.search');


        //words
        Route::get('Words', 'Cornelio\Category\WordsController@index')->name('words.index');
        Route::post('Words/store', 'Cornelio\Category\WordsController@store')->name('words.store');
        Route::get('Words/create', 'Cornelio\Category\WordsController@create')->name('words.create');
        Route::put('Words/{words}', 'Cornelio\Category\WordsController@update')->name('words.update');
        Route::get('Words/{words}/edit', 'Cornelio\Category\WordsController@edit')->name('words.edit');
        Route::get('Words/{words}', 'Cornelio\Category\WordsController@destroy')->name('words.destroy');
        Route::post('Words/search', 'Cornelio\Category\WordsController@search')->name('words.search');
    });
});
//Se encuentra a fuera ya que no será necesario tener registrado una compañia
Route::post('Send', 'Cornelio\Category\NumberWhatsappController@Send')->name('whatsapp.Send');
