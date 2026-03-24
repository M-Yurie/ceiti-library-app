<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:librarian,admin'])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');});
});

Route::middleware(['auth', 'role:librarian,admin'])->group(function () {
    Route::resource('books', \App\Http\Controllers\BookController::class);
});

require __DIR__.'/auth.php';
