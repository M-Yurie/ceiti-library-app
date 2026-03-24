<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/favorites/{book}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});

Route::middleware(['auth', 'role:librarian,admin'])->group(function () {
    Route::resource('books', BookController::class)->except(['index', 'show']);
    Route::get('/books/{book}/copies', [BookCopyController::class, 'index'])->name('copies.index');
    Route::post('/books/{book}/copies', [BookCopyController::class, 'store'])->name('copies.store');
    Route::delete('/copies/{copy}', [BookCopyController::class, 'destroy'])->name('copies.destroy');
    Route::post('/loans/{copy}', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{copy}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::get('/librarian/dashboard', [DashboardController::class, 'librarian'])
        ->name('librarian.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');
});

require __DIR__.'/auth.php';
