<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();
Route::middleware("auth")
->namespace("Admin")->prefix("admin")->name("admin.")
    ->group(function() {
        Route::get('/', 'HomeController@index')->name('home');
        Route::get('/products', 'ProductController@index')->name('product.index');
        Route::get('/products/create', 'ProductController@index')->name('product.create');
        Route::get('/products/edit', 'ProductController@index')->name('product.edit');
        Route::resource("posts", "PostController");
        Route::get("users", "UserController@index")->name("users.index");
});

Route::get("{any?}", function() {
    return view("home");
})->where("any", ".*");