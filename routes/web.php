<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// login, register, reset password routes
Auth::routes();

// redirect: CHuyển hướng
Route::get('/', function () {
    return redirect()->route('login');
});

//
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [UserController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
});
