<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\RestaurantController;
Route::get('/', [RestaurantController::class, 'index']); 
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/search/{keyword?}', [RestaurantController::class, 'search']);
