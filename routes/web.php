<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\InvoiceController;
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

    // Invoice routes
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('/invoices/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    
    // Payment routes
    Route::get('/invoices/{invoice}/payment', [InvoiceController::class, 'showPaymentForm'])->name('invoices.payment');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'processPayment'])->name('invoices.process-payment');
    
    // Revenue and receipt routes
    Route::get('/revenue-report', [InvoiceController::class, 'revenueReport'])->name('revenue.report');
    Route::get('/receipts/{receipt}/download', [InvoiceController::class, 'downloadReceipt'])->name('receipts.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
