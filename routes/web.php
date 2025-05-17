<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('home');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/hal-register', [LoginController::class, 'indexRegister'])->name('halRegister');
    Route::post('/register', [LoginController::class, 'register'])->name('register');
    Route::get('/hal-forgot-password', [LoginController::class, 'indexForgotPassword'])->name('index.forgot.password');
    Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot.password');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/user', [UserController::class, 'index'])->name('index.user');
    Route::get('/form-user', [UserController::class, 'formUser'])->name('form.user');
    Route::post('/add-user', [UserController::class, 'store'])->name('add.user');
    Route::get('/detail/{id}', [UserController::class, 'show'])->name('detail');
    Route::delete('/user/{id}/{permanent?}', [UserController::class, 'destroy'])->name('delete.user');
    Route::get('/user/{id}', [UserController::class, 'restore'])->name('restore.user');
    Route::get('/profile/{id}', [UserController::class, 'profile'])->name('profile');
});
