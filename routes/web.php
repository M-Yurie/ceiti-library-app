<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\LoanController;

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

Route::middleware(['auth', 'role:librarian,admin'])->group(function () {
    Route::get('/books/{book}/copies', [BookCopyController::class, 'index'])->name('copies.index');
    Route::post('/books/{book}/copies', [BookCopyController::class, 'store'])->name('copies.store');
    Route::delete('/copies/{copy}', [BookCopyController::class, 'destroy'])->name('copies.destroy');
});

Route::middleware(['auth', 'role:librarian,admin'])->group(function () {
    Route::post('/loans/{copy}', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{copy}/return', [LoanController::class, 'returnBook'])->name('loans.return');
});

require __DIR__.'/auth.php';
