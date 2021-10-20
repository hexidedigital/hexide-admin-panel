<?php


use HexideDigital\HexideAdmin\Http\Controllers\Backend\LanguageController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'admin', 'as' => 'admin.'
], function () {
    Route::get('locale/{locale}', LanguageController::class)->name('locale');


});
