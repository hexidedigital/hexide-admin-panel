<?php

use HexideDigital\HexideAdmin\Http\Controllers\Backend\LanguageController;
use Illuminate\Support\Facades\Route;


Route::get('locale/{locale}', LanguageController::class)->name('locale');

Route::post('generate/slug', function (\Illuminate\Http\Request $request){
    return str_slug($request->get('value', ''), '-', 'uk');
})->name('generate.slug')->middleware(['auth:web']);
