<?php

use App\Livewire\Customers;
use App\Livewire\Dashboard;
use App\Livewire\ServiceIntake;
use App\Livewire\ServiceList;
use Illuminate\Support\Facades\Route;

Route::get('/servis', ServiceList::class)->name('services.index');
Route::get('/servis/buat', ServiceIntake::class)
    ->name('services.create');
Route::get('/pelanggan', Customers::class)->name('customers.index');
Route::get('/', Dashboard::class);