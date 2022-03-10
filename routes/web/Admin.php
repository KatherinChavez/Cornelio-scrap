<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('users/{user}/profile', 'Admin\UserController@show')->name('users.profile');
    Route::get('users/profile/edit', 'Admin\UserController@edit')->name('profile.edit');
    Route::put('users/profile/update', 'Admin\UserController@update')->name('profile.update');


    Route::prefix('admin')->group(function () {

        //APP_FB
        Route::get('AppFacebook', 'Admin\AppFbController@index')->name('app.index');
        Route::post('app/store', 'Admin\AppFbController@store')->name('app.store');
        Route::post('app/edit', 'Admin\AppFbController@edit')->name('app.edit');
        Route::post('app/editTwitter', 'Admin\AppFbController@editTwitter')->name('app.editTwitter');
        Route::post('app/update', 'Admin\AppFbController@update')->name('app.update');
        Route::get('App/delete/{app}/Facebook', 'Admin\AppFbController@destroy')->name('app.destroy');
        Route::get('App/delete/{app}/Twitter', 'Admin\AppFbController@destroyTwitter')->name('app.destroyTwitter');

        //API WAPIAD
        Route::get('apiWhatsapp/index', 'Cornelio\ApiWhatsapp\ApiWhatsappController@index')->name('apiWhatsapp.index');
        Route::post('/apiWhatsapp/store', 'Cornelio\ApiWhatsapp\ApiWhatsappController@store')->name('apiWhatsapp.store');
        Route::post('/apiWhatsapp/edit', 'Cornelio\ApiWhatsapp\ApiWhatsappController@edit')->name('apiWhatsapp.edit');
        Route::post('/apiWhatsapp/update', 'Cornelio\ApiWhatsapp\ApiWhatsappController@update')->name('apiWhatsapp.update');
        Route::get('apiWhatsapp/delete/{apiWhatsapp}', 'Cornelio\ApiWhatsapp\ApiWhatsappController@destroy')->name('apiWhatsapp.destroy');


        //Roles
        Route::post('roles/store', 'Admin\RoleController@store')->name('roles.store');
        Route::get('roles', 'Admin\RoleController@index')->name('roles.index');
        Route::get('roles/create', 'Admin\RoleController@create')->name('roles.create');
        Route::put('roles/{role}', 'Admin\RoleController@update')->name('roles.update');
        Route::get('roles/{role}', 'Admin\RoleController@show')->name('roles.show');
        Route::delete('roles/{role}', 'Admin\RoleController@destroy')->name('roles.destroy');
        Route::get('roles/{role}/edit', 'Admin\RoleController@edit')->name('roles.edit');

        //Permissions
        Route::post('permissions/store', 'Admin\PermissionController@store')->name('permissions.store')->middleware('can:permissions.create');
        Route::get('permissions', 'Admin\PermissionController@index')->name('permissions.index')->middleware('can:permissions.index');
        Route::get('permissions/create', 'Admin\PermissionController@create')->name('permissions.create')->middleware('can:permissions.create');
        Route::put('permissions/{permission}', 'Admin\PermissionController@update')->name('permissions.update')->middleware('can:permissions.edit');
        Route::get('permissions/{permission}', 'Admin\PermissionController@show')->name('permissions.show')->middleware('can:permissions.show');
        Route::delete('permissions/{permission}', 'Admin\PermissionController@destroy')->name('permissions.destroy')->middleware('can:permissions.destroy');
        Route::get('permissions/{permission}/edit', 'Admin\PermissionController@edit')->name('permissions.edit')->middleware('can:permissions.edit');

        //Users
        Route::get('users', 'Admin\UserController@index')->name('users.index')->middleware('can:users.index');
        Route::get('users/crea', 'Admin\UserController@crea')->name('users.crea');
        Route::put('users/{user}', 'Admin\UserController@update')->name('users.update')->middleware('can:users.edit');
        Route::get('users/{user}', 'Admin\UserController@show')->name('users.show')->middleware('can:users.show');
        Route::delete('users/{user}', 'Admin\UserController@destroy')->name('users.destroy')->middleware('can:users.destroy');
        Route::get('users/{user}/edit', 'Admin\UserController@edit')->name('users.edit')->middleware('can:users.edit');
        Route::post('users/store', 'Admin\UserController@store')->name('users.store')->middleware('can:users.store');

         //EMPRESAS, COMPANY

        Route::post('companies/store', 'Admin\CompaniesController@store')->name('companies.store')->middleware('can:companies.create');
        Route::get('companies', 'Admin\CompaniesController@index')->name('companies.index')->middleware('can:companies.index');
        Route::get('companies/create', 'Admin\CompaniesController@create')->name('companies.create')->middleware('can:companies.create');
        Route::put('companies/{companies}', 'Admin\CompaniesController@update')->name('companies.update')->middleware('can:companies.edit');
        Route::get('companies/{companies}/edit', 'Admin\CompaniesController@edit')->name('companies.edit')->middleware('can:companies.edit');
        Route::delete('companies/{companies}', 'Admin\CompaniesController@destroy')->name('companies.destroy')->middleware('can:companies.destroy');
    });
});