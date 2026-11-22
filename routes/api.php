<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\delete;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/users', [UserController::class, 'register'])->name('api.users.register');
Route::post('/users/login', [UserController::class, 'login'])->name('api.users.login');

Route::middleware(['tokenMiddleware'])->group(function(){
    Route::get('/users/current', [UserController::class, 'get'])->name('api.user.get');
    Route::patch('/users/current', [UserController::class, 'update'])->name('api.users.update');
    Route::delete('/users/logout', [UserController::class, 'logout'])->name('api.users.logout');

    Route::post('/contacts', [ContactController::class, 'create'])->name('api.contacts.create');
    Route::get('/contacts/{contact}', [ContactController::class, 'get'])->name('api.contacts.get');
    Route::patch('/contacts/{contact}', [ContactController::class, 'update'])->name('api.contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('api.contacts.destroy');
});