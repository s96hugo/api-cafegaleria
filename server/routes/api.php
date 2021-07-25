<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Auth
Route::post('login', 'API\AuthController@login');
Route::post('register', 'API\AuthController@register');
Route::post('logout', 'API\AuthController@logout');

//Refactorizar
//Product CRUD Routes
Route::post('/products', 'API\ProductController@store');
Route::get('/products', 'API\ProductController@index');
Route::get('/products/{id}', 'API\ProductController@show');
Route::put('/products/{id}', 'API\ProductController@update');
Route::post('/products/{id}', 'API\ProductController@destroy');

//Category CRUD Routes
Route::post('/categories', 'API\CategoryController@store');
Route::get('/categories', 'API\CategoryController@index');
Route::get('/categories/{id}', 'API\CategoryController@show');
Route::put('/categories/{id}', 'API\CategoryController@update');
Route::post('/categories/{id}', 'API\CategoryController@destroy');

//Bien, sin middleware AUTH ni PERMISOS
//Table CRUD
Route::post('tables/create', 'API\TableController@create');
Route::get('tables/get/all', 'API\TableController@getAll');
Route::get('tables/get/{id}', 'API\TableController@get');
Route::post('tables/update/{id}', 'API\TableController@update');
Route::post('tables/delete/{id}', 'API\TableController@delete');

//Ticket functions
Route::post('tickets/create', 'API\TicketController@create');
Route::post('tickets/delete/{id}', 'API\TicketController@delete');
Route::post('tickets/cuenta/{id}', 'API\TicketController@cuenta');
Route::get('tickets/get/all', 'API\TicketController@getAll');
Route::get('tickets/get/{id}', 'API\TicketController@get');
Route::post('tickets/change/table/{id}', 'API\TicketController@changeTable');

//Order function
Route::post('orders/create', 'API\OrderController@create');