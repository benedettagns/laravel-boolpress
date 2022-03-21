<?php

use Illuminate\Support\Facades\Route;

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
        Route::get('/create', 'ProductController@index')->name('product.create');
        Route::get('/edit', 'ProductController@index')->name('product.edit');
        Route::get('/posts', 'PostController@index')->name('post.index');
        Route::get('/posts/create', 'PostController@index')->name('post.create');
        Route::get('/posts/edit', 'PostController@index')->name('post.edit');
    });

Route::get("{any?}", function() {
    return view("guest.home");
})->where("any", ".*");