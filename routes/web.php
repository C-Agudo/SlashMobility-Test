<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UserAuthMiddleware;

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

Route::post('/user/register', 'UserController@register');
Route::get('/register/verify/{code}', 'UserController@verify');
Route::post('/user/login', 'UserController@login');
Route::post('/user/logout', 'UserController@logout');
Route::put('/user/updatePassword', 'UserController@updatePassword')->middleware(UserAuthMiddleware::class);
Route::get('/user/users', 'UserController@list')->middleware(UserAuthMiddleware::class);
Route::get('/user/users/{userName}', 'UserController@detail')->middleware(UserAuthMiddleware::class);
Route::put('/user/update', 'UserController@update')->middleware(UserAuthMiddleware::class);

Route::post('/provider/register', 'ProviderController@register')->middleware(UserAuthMiddleware::class);
Route::get('/provider/providers', 'ProviderController@list')->middleware(UserAuthMiddleware::class);
Route::get('/provider/providers/{name}', 'ProviderController@detail')->middleware(UserAuthMiddleware::class);
Route::put('/provider/update', 'ProviderController@update')->middleware(UserAuthMiddleware::class);

Route::post('/product/register', 'ProductController@register')->middleware(UserAuthMiddleware::class);
Route::get('/product/products', 'ProductController@list')->middleware(UserAuthMiddleware::class);
Route::get('/product/products/{name}', 'ProductController@detail')->middleware(UserAuthMiddleware::class);
Route::put('/product/update', 'ProductController@update')->middleware(UserAuthMiddleware::class);
Route::get('/product/{type}', 'ProductController@listType')->middleware(UserAuthMiddleware::class);
Route::get('/product/provider/{city}', 'ProductController@listByProviderCity')->middleware(UserAuthMiddleware::class);




