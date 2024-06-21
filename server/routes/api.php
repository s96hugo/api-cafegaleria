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
Route::get('logout', 'API\AuthController@logout')->middleware('jwtAuth');
Route::get('users', 'API\AuthController@getUsers')->middleware('jwtAuth');
Route::post('user/{id}/edit', 'API\AuthController@editUser')->middleware('jwtAuth');
Route::get('user/{id}/get', 'API\AuthController@getCurrent')->middleware('jwtAuth');
Route::post('user/{id}/delete', 'API\AuthController@deleteUser')->middleware('jwtAuth');

Route::middleware(['jwtAuth'])->group(function () {
    
//Product functions Routes
Route::post('products/create', 'API\ProductController@create');
Route::get('products/{id}/get', 'API\ProductController@get');
Route::post('products/{id}/update', 'API\ProductController@update');
Route::post('products/{id}/delete', 'API\ProductController@delete');
Route::post('products/{id}/invisible', 'API\ProductController@invisible');
Route::post('products/{id}/visible', 'API\ProductController@visible');
Route::get('products/category/{id}', 'API\ProductController@getProductByCategoryId');
Route::get('products/popular', 'API\ProductController@mostPopular');
Route::get('products', 'API\ProductController@productCategory');
Route::get('products/{id}/dataSet', 'API\ProductController@productsDataSet');



//Category CRUD Routes
Route::post('categories/create', 'API\CategoryController@create');
Route::get('categories', 'API\CategoryController@getAll');
Route::get('categories/{id}/get', 'API\CategoryController@get');
Route::post('categories/{id}/update', 'API\CategoryController@update');
Route::post('categories/{id}/delete', 'API\CategoryController@delete');
Route::get('category/{id}/HasProduct', 'API\ProductController@checkCategoryHasProduct');

//Tables CRUD Routes
Route::post('tables/create', 'API\TableController@create');
Route::get('tables', 'API\TableController@getAll');
Route::get('tables/{id}/get', 'API\TableController@get');
Route::post('tables/{id}/update', 'API\TableController@update');
Route::post('tables/{id}/delete', 'API\TableController@delete');


//Ticket functions
Route::post('tickets/create', 'API\TicketController@create');
Route::post('tickets/{id}/delete', 'API\TicketController@delete');
Route::post('tickets/{id}/cuenta', 'API\TicketController@cuenta');
Route::get('ticketsOpen', 'API\TicketController@getCurrentTickets');
Route::get('ticketsClosed', 'API\TicketController@getClosedTickets');
Route::get('tickets/{id}/get/', 'API\TicketController@get');
Route::post('tickets/{id}/changeTable/', 'API\TicketController@changeTable');
Route::get('tickets/{id}/showBill/', 'API\TicketController@showBill');
Route::post('facturacion/{id}', 'API\TicketController@calcFacturacion');


//Order function
Route::post('orders/create', 'API\OrderController@create');
Route::get('orders', 'API\OrderController@getAll');
Route::post('orders/{id}/delete', 'API\OrderController@delete');

//ProductOrder function
Route::post('productOrders/create', 'API\ProductOrderController@create');
Route::post('productOrders/crear', 'API\ProductOrderController@crear');
Route::get('productOrders/{id}/info', 'API\ProductOrderController@ticketProductOrdersInfo');
Route::post('productOrders/{id}/delete', 'API\ProductOrderController@deleteProductOrder');
Route::post('productOrders/{id}/update', 'API\ProductOrderController@update');
Route::post('productOrders/{id}/status', 'API\ProductOrderController@changeStatus');

});
