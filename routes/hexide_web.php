<?php


use HexideDigital\HexideAdmin\Http\Controllers\Frontend\LanguageController;

Route::get('setlocale/{locale?}', LanguageController::class)->name('front.setlocale');
