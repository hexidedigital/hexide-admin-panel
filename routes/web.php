<?php

use HexideDigital\HexideAdmin\Http\Controllers\Frontend\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('setlocale/{locale?}', LanguageController::class)->name('front.setlocale');
