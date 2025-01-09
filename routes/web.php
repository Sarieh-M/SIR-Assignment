<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SearchPage;

Route::get('/', function () {
    return view('searchpage');
})->name('search');
Route::get('/add', fn() => view('addpage'))->name('add');