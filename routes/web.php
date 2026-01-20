<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    Route::post('/chapters/{chapter}/select', [ChapterController::class, 'select'])->name('chapters.select');
    Route::delete('/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');
    Route::post('/chapters/{chapter}/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
