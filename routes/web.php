<?php

use App\Http\Controllers\AccountProductController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// login, register, reset password routes
Auth::routes();

Route::get('/', function () {
    return view('frontend.home.home');
});
// blog routes
Route::get('/blogs', [FrontendBlogController::class, 'index'])->name('frontend.blogs.index');
Route::get('/blogs/{id}', [FrontendBlogController::class, 'show'])->name('frontend.blogs.show');

Route::middleware(['auth'])->group(function () {

    Route::post('/blog/rate/ajax', [FrontendBlogController::class, 'rate'])->name('frontend.blogs.rate.ajax');
    Route::post('/blog/comment/ajax', [FrontendBlogController::class, 'comment'])->name('frontend.blogs.comment.ajax');
    // account
    Route::get('/account/update', [ProfileController::class, 'edit'])->name('account.edit');
    Route::put('account/update', [ProfileController::class, 'update'])->name('account.update');
    Route::get('/account/my-product', [AccountProductController::class, 'index'])->name('account.my-product');
    Route::get('/account/add-product', [AccountProductController::class, 'create'])->name('account.add-product');
    Route::post('/account/my-product', [AccountProductController::class, 'store'])->name('account.products.store');
    Route::get('/account/my-product/{product}/edit', [AccountProductController::class, 'edit'])
        ->whereNumber('product')
        ->name('account.products.edit');
    Route::put('/account/my-product/{product}', [AccountProductController::class, 'update'])
        ->whereNumber('product')
        ->name('account.products.update');
    Route::delete('/account/my-product/{product}', [AccountProductController::class, 'destroy'])
        ->whereNumber('product')
        ->name('account.products.destroy');

    Route::middleware(['admin'])->group(function () {
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
    });

    Route::get('/frontend/home', function () {
        return view('frontend.home.home');
    });

});
