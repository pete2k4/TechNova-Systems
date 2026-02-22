<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HelloWorldController::class, 'home']);
Route::get('/helloWorld', [HelloWorldController::class, 'helloWorld']);
