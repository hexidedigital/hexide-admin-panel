<?php

use App\Http\Controllers\Backend\HomeController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'web'],
    function () {
        Route::get('locale/{locale}', [HomeController::class, 'setLocale'])->name('locale');

        Route::group(['middleware' => ['auth:admin', 'language:admin']],
            function () {
//                Route::get('/', 'HomeController@index')->name('home');

                // translation module
//                Route::get('translations/{group}','TranslationController@index')->name('translations.index');
//                Route::post('translations/{group}', 'TranslationController@update')->name('translations.update');

                // variable module
//                Route::get('variables/list', 'VariableController@list')->name('variables.list.index');
//                Route::resource('variables', 'VariableController')->except('show');

                // security modules
//                Route::resource('users', 'UserController');
//                Route::resource('permissions', 'PermissionController');
//                Route::resource('roles', 'RoleController');

            }
        );
    }
);
