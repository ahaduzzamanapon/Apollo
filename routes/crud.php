<?php

use Illuminate\Support\Facades\Route;

// Generated CRUD routes will be appended here

Route::resource('centerDetails', App\Http\Controllers\Admin\CenterDetailsController::class)->names('admin.centerDetails');
Route::get('centerDetails/export/pdf', [App\Http\Controllers\Admin\CenterDetailsController::class, 'exportPdf'])->name('admin.centerDetails.export.pdf');
Route::get('centerDetails/export/excel', [App\Http\Controllers\Admin\CenterDetailsController::class, 'exportExcel'])->name('admin.centerDetails.export.excel');

Route::resource('testCategories', App\Http\Controllers\Admin\TestCategoryController::class)->names('admin.testCategories');
Route::get('testCategories/export/pdf', [App\Http\Controllers\Admin\TestCategoryController::class, 'exportPdf'])->name('admin.testCategories.export.pdf');
Route::get('testCategories/export/excel', [App\Http\Controllers\Admin\TestCategoryController::class, 'exportExcel'])->name('admin.testCategories.export.excel');

Route::resource('testFields', App\Http\Controllers\Admin\TestFieldController::class)->names('admin.testFields');
Route::get('testFields/export/pdf', [App\Http\Controllers\Admin\TestFieldController::class, 'exportPdf'])->name('admin.testFields.export.pdf');
Route::get('testFields/export/excel', [App\Http\Controllers\Admin\TestFieldController::class, 'exportExcel'])->name('admin.testFields.export.excel');
Route::get('testFields/by-test/{test}', [App\Http\Controllers\Admin\TestFieldController::class, 'byTest'])->name('admin.testFields.byTest');

