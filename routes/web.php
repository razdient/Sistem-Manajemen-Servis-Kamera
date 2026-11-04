<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Customers;

Route::get('/pelanggan', Customers::class)->name('customers.index');
Route::get('/', Dashboard::class);
