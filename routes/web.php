<?php

use App\Livewire\Catalogs;
use App\Livewire\Customers;
use App\Livewire\Dashboard;
use App\Livewire\Expenses;
use App\Livewire\Payments;
use App\Livewire\Reports;
use App\Livewire\ServiceDetail;
use App\Livewire\ServiceEstimate;
use App\Livewire\ServiceIntake;
use App\Livewire\ServiceList;
use App\Livewire\ServicePayment;
use App\Livewire\ServiceWork;
use Illuminate\Support\Facades\Route;

Route::get('/servis', ServiceList::class)->name('services.index');
Route::get('/servis/buat', ServiceIntake::class)
    ->name('services.create');
Route::get('/pelanggan', Customers::class)->name('customers.index');
Route::get('/katalog', Catalogs::class)->name('catalogs.index');
Route::get('/pembayaran', Payments::class)->name('payments.index');
Route::get('/pengeluaran', Expenses::class)->name('expenses.index');
Route::get('/laporan', Reports::class)->name('reports.index');
Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/servis/{id}/estimasi', ServiceEstimate::class)
    ->name('services.estimate');
Route::get('/servis/{id}/pengerjaan', ServiceWork::class)
    ->name('services.work');
Route::get('/servis/{id}/pembayaran', ServicePayment::class)
    ->name('services.payment');
Route::get('/servis/{id}', ServiceDetail::class)
    ->name('services.show');