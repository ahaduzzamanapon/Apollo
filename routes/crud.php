<?php

use Illuminate\Support\Facades\Route;

// Generated CRUD routes will be appended here

Route::resource('centerDetails', App\Http\Controllers\Admin\CenterDetailsController::class)->names('admin.centerDetails');
Route::get('centerDetails/export/pdf', [App\Http\Controllers\Admin\CenterDetailsController::class, 'exportPdf'])->name('admin.centerDetails.export.pdf');
Route::get('centerDetails/export/excel', [App\Http\Controllers\Admin\CenterDetailsController::class, 'exportExcel'])->name('admin.centerDetails.export.excel');
