<?php

use HexideDigital\HexideAdmin\Http\Controllers\Frontend\LanguageController;
use Illuminate\Support\Facades\Route;


Route::get('set-locale/{locale?}', LanguageController::class)->name('front.set-locale');

Route::post('generate/slug', function (\Illuminate\Http\Request $request){
    return str_slug($request->get('value', ''));
})->name('generate.slug')->middleware(['ajax']);
