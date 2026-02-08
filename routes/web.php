<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MergeController;
use App\Http\Controllers\UserController;

Route::get('/', function(){ return redirect('/dashboard'); });
Route::get('/register', [AuthController::class,'showRegister'])->name('register');
Route::post('/register', [AuthController::class,'register']);
Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [MergeController::class,'dashboard'])->name('dashboard');
    Route::get('/merge', [MergeController::class,'showForm'])->name('merge.form');
    Route::post('/merge', [MergeController::class,'merge'])->name('merge');
    Route::get('/report', [MergeController::class,'report'])->name('report');
    Route::middleware('can:manage-users')->group(function(){
        Route::get('/users', [UserController::class,'index'])->name('users.index');
        Route::post('/users/{user}/toggle', [UserController::class,'toggle'])->name('users.toggle');
        Route::get('/matches', [MergeController::class,'matchIndex'])->name('matches.index');
        Route::post('/matches', [MergeController::class,'matchStore'])->name('matches.store');
        Route::delete('/matches/{id}', [MergeController::class,'matchDelete'])->name('matches.delete');
    });
});
