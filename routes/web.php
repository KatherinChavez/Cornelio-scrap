<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home', 'HomeController@setSession')->name('home.session');
    Route::get('/companies', 'HomeController@companies')->name('companies');

    //Ejecucion de comando
    Route::get('/Ejecucion', 'EjecucionController@index')->name('ejecucion');
    Route::get('/ClassificationContent', 'EjecucionController@ClassificationContent')->name('ejecucion');
    Route::get('/Comentario', 'EjecucionController@comment')->name('comment');
    Route::get('/MegaComentario', 'EjecucionController@mega')->name('comment');
    Route::get('/MonthComments', 'EjecucionController@MonthComments')->name('comment');
    Route::get('/PostSearch', 'EjecucionController@postS')->name('comment');
    Route::get('/Diario', 'EjecucionController@diario')->name('diario');
    Route::get('/Link', 'EjecucionController@link')->name('link');
    Route::get('/Mes', 'EjecucionController@month')->name('mes');
    Route::get('/ScrapComment', 'EjecucionController@ScrapComment')->name('reacciones');
    Route::get('/ScrapPost', 'EjecucionController@ScrapPost')->name('reacciones');
    Route::get('/ScrapReaction', 'EjecucionController@reacciones')->name('reacciones');
    Route::get('/Inbox', 'EjecucionController@inbox')->name('inbox');
    Route::get('/Fecha', 'EjecucionController@fecha')->name('fecha');
    Route::get('/alertTopics', 'EjecucionController@alertTopics')->name('alertTopics');
    Route::get('/ejecucionCron', 'EjecucionController@ejecucionCron')->name('ejecucionCron');
    Route::get('/Count', 'EjecucionController@count')->name('count');
    Route::get('/CountContent', 'EjecucionController@countC')->name('countC');
    Route::get('/CronScrap', 'EjecucionController@CronScrap')->name('CronScrap');
    Route::get('/DesclassificationContent', 'EjecucionController@DesclassificationContent')->name('DesclassificationContent');
    Route::get('/StatusPage', 'EjecucionController@StatusPage')->name('StatusPage');
    Route::get('/InactivePage', 'EjecucionController@InactivePage')->name('InactivePage');
    Route::get('/pruebaComando', 'EjecucionController@pruebaComando')->name('pruebaComando');


    Route::middleware('company')->group(function () {
        Route::get('/Tops', 'HomeController@tops')->name('get.tops');
        Route::post('/FeelingComments', 'HomeController@FeelingComments')->name('get.FeelingComments');
        Route::post('/wordContent', 'HomeController@wordContent')->name('get.wordContent');
        Route::post('/cloudWord', 'HomeController@cloudWord')->name('get.cloudWord');
        Route::post('/NetworkTopics', 'HomeController@NetworkTopics')->name('get.NetworkTopics');
        Route::post('/NetworkDetail', 'HomeController@NetworkDetail')->name('get.NetworkDetail');

        //Ruta de mis paginas
        Route::get('/index', 'HomeController@indexFb')->name('facebook.index');
        Route::post('/index/susbcribe', 'HomeController@susbcribe')->name('facebook.susbcribe');
        Route::post('/index/unsusbcribe', 'HomeController@unsubscribe')->name('facebook.unsubscribe');

        // Rutas para publicar
        Route::get('/post', 'Facebook\PostController@index')->name('post.index');
        Route::post('/post/create', 'Facebook\PostController@create')->name('post.create');
        Route::post('/GetPostDB', 'Facebook\PostController@GetPostDB')->name('post.selectPostDB');

        //Rutas para publicaciones y comentarios scrap
        Route::get('scrap/comments', 'Facebook\ScrapCommentsController@index')->name('scrapComments.index');

        //facebook_comments
        Route::post('/pages', 'Facebook\CommentsController@getPages')->name('pages.get');
        Route::get('/comments', 'Facebook\CommentsController@index')->name('comments.index');
        Route::post('/comments', 'Facebook\CommentsController@getComments')->name('comments.get');
        Route::post('/post', 'Facebook\CommentsController@getPost')->name('post.get');
        Route::post('/search', 'Facebook\CommentsController@Search')->name('comments.search');
        Route::post('/GetCommentDB', 'Facebook\CommentsController@GetCommentDB')->name('comment.selectDB');

        Route::post('comentarPublicacion', 'Facebook\FacebookcommentsController@comentarPublicacion')->name('comentar.publicacion');
        Route::get('detail_comments', 'Facebook\FacebookcommentsController@detail')->name('comments.detail_comments');

        Route::get('Instagram', 'Cornelio\Instagram\InstagramController@index')->name('instagram.index');

    });
});






