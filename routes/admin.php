<?php


use HexideDigital\HexideAdmin\Http\Controllers\Backend\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('locale/{locale}', LanguageController::class)->name('locale');
