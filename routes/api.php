<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('AdminApi')->match(['post'],'/category/create','cateController@addCate');
Route::middleware('AdminApi')->match(['get'],'/category','cateController@showCate');
Route::middleware('AdminApi')->match(['post'],'/category/update','cateController@updateCate');
Route::middleware('AdminApi')->match(['post'],'/category/delete','cateController@deleteCate');

Route::middleware('AdminApi')->match(['get','post'],'/product','ProductController@showProduct');
Route::middleware('AdminApi')->match(['post'],'/product/create','productController@addProduct');
Route::middleware('AdminApi')->match(['post'],'/product/updata','productController@updataProduct');
Route::middleware('AdminApi')->match(['post'],'/product/delete','productController@deleteProduct');

Route::middleware('AdminApi')->match(['post'],'/sku/create','skuController@createSku');
Route::middleware('AdminApi')->match(['post'],'/sku/updata','skuController@updataSku');
Route::middleware('AdminApi')->match(['post'],'/sku/delete','skuController@deleteSku');
Route::middleware('AdminApi')->match(['post'],'/sku','skuController@showSku');



Route::middleware('AdminApi')->match(['post'],'/tag/create','tagController@createTag');
Route::middleware('AdminApi')->match(['post'],'/tag/updata','tagController@updataTag');
Route::middleware('AdminApi')->match(['post'],'/tag/delete','tagController@deleteTag');
Route::middleware('AdminApi')->match(['post'],'/tag','tagController@showTag');


Route::middleware('AdminApi')->match(['get','post'],'/nav','navController@showNav');
Route::middleware('AdminApi')->match(['post'],'/nav/create','navController@createNav');
Route::middleware('AdminApi')->match(['post'],'/nav/updata','navController@updataNav');
Route::middleware('AdminApi')->match(['post'],'/nav/delete','navController@deleteNav');

Route::middleware('AdminApi')->match(['post'],'/tag/picUpload','tagController@picUpload');
Route::middleware('AdminApi')->match(['post'],'/tag/pushUpload','tagController@pushPicUpload');

Route::middleware('loginMiddleware')->match(['post'],'/register','RegisterController@register');
Route::middleware('loginMiddleware')->match(['post'],'/login','loginController@login');


Route::match(["post"],'/order',"OrderService@create");




