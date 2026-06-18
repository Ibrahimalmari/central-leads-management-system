<?php

use App\Http\Controllers\SystemFaviconController;
use App\Http\Controllers\SystemLogoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/integration-test/form', 'integration-test.form')->name('integration-test.form');

Route::get('/system-favicon', SystemFaviconController::class)->name('system.favicon');
Route::get('/system-logo', SystemLogoController::class)->name('system.logo');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar', 'en'], true), 404);

    session(['locale' => $locale]);
    cookie()->queue(cookie()->forever('locale', $locale, null, null, null, request()->isSecure(), false, false, 'lax'));
    app()->setLocale($locale);

    return redirect()->back();
})->name('language.switch');
