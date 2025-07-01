<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::name('auth.')
    ->group(function () {
        
        // Index/Home page (redirects to dashboard if already logged in)
        Route::middleware('guest')
        ->group(function () {
        // Authentication routes
            Route::get('/', [LoginController::class, 'login'])
                ->name('login');
            Route::post('/', [LoginController::class, 'authenticate'])
                ->name('authenticate');
                
            Route::get('/register', [RegisterController::class, 'index'])
                ->name('register');
            Route::post('/register', [RegisterController::class, 'store'])
                ->name('register');
        });

            // Authentication protected routes
            Route::middleware('auth')
                    ->group(function () {
                    // Logout action
                    Route::post('logout', [LoginController::class, 'unauthenticate'])
                        ->name('logout');
            });
    });


Route::middleware('auth')
    ->name('ui.')
    ->prefix('/ui')
    ->group(function () {

    // Payment
    Route::prefix('payment')
        ->name('payment.')
        ->group(function (){
            Route::get('', [PaymentController::class, 'index'])->name('index');
            Route::get('create', [PaymentController::class, 'create'])->name('create');
            Route::post('create', [PaymentController::class, 'store'])->name('store');
            Route::get('edit', [PaymentController::class, 'edit'])->name('edit');
            Route::get('{id}', [PaymentController::class, 'show'])->name('show');
            Route::put('{id}', [PaymentController::class, 'update'])->name('update');
            Route::delete('{id}', [PaymentController::class, 'destroy'])->name('destroy');
            Route::get('cancelled', [PaymentController::class, 'cancelled'])->name('cancelled');
        });
    
});

