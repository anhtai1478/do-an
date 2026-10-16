<?php

use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogController;

// login, register, reset password routes
Auth::routes();



Route::get('/', function () {
    return view('frontend.home.home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [UserController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/admin/country', [CountryController::class, 'index'])->name('admin.country.index');
    Route::post('/admin/country', [CountryController::class, 'store'])->name('admin.country.store');
    Route::delete('/admin/country/{id}', [CountryController::class, 'delete'])->name('admin.country.delete');

    Route::get('/admin/blogs', [BlogController::class, 'index'])->name('admin.blogs.index');
    Route::get('/admin/blogs/create', [BlogController::class, 'create'])->name('admin.blogs.create');
    Route::post('/admin/blogs', [BlogController::class, 'store'])->name('admin.blogs.store');

    Route::get('/admin/blogs/{id}/edit', [BlogController::class, 'edit'])->name('admin.blogs.edit');
    Route::put('/admin/blogs/{id}', [BlogController::class, 'update'])->name('admin.blogs.update');
    Route::delete('/admin/blogs/{id}', [BlogController::class, 'delete'])->name('admin.blogs.delete');


    // Route  Frontend
    Route::get('/frontend/home', function () {
        return view('frontend.home.home');
    });
});