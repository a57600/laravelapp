<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/store', 'Store@index' ) ->name('store');

Route::get('/store/index/{id?}', 'Store@index' ) ->name('storeindexid');



Route::get('/store/cart/{id}', 'Store@cartItemInsert' ) ->name('addtocartid');


Route::get('/store/register', 'Store@register' ) -> name('store/register');
Route::post('/store/register', 'Store@register_action' ) -> name('register_action');


Route::get('/store/login', 'Store@login' ) -> name('store/login');
Route::post('/store/login', 'Store@login_action' ) -> name('login_action');


Route::get('/store/logout', 'Store@logout' ) -> name('logout');


Route::get('/store/checkout', 'Store@checkout' ) -> name('checkout');
Route::post('/store/checkout', 'Store@checkoutAction' ) -> name('checkoutaction');

Route::get('/store/checkout/{id?}', 'Store@checkoutIncrease') -> name('increase');

Route::get('/store/checkout/decrease/{coisoid}', 'Store@checkoutDecrease') -> name('decrease');

Route::get('/store/checkout/remove/what/{id}', 'Store@checkoutRemove') -> name('removefromcart');



Route::get('/store/orders', 'Store@orders' ) -> name('orders');


Route::get('/store/message/{id?}', 'Store@message' ) -> name('store/message');
