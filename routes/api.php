<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/user/register' , [UserController::class , 'register']);
Route::post('/user/login' , [UserController::class , 'login']);

Route::group(['middleware' => 'auth:user'], function(){
    Route::get('store/myProducts' , [ProductController::class , 'get_my_products']);

    Route::post('store/addProduct' , [ProductController::class , 'add_product_model']);
    Route::put('store/editProduct' , [ProductController::class , 'edit_product']);
    Route::delete('store/deleteProduct' , [ProductController::class , 'remove_product']);

    Route::post('store/addProductBranch/{id}' , [ProductController::class , 'add_product_branch']);
    Route::put('store/editProductBranch/{id}' , [ProductController::class , 'edit_product']);
    Route::delete('store/deleteProductBranch/{id}' , [ProductController::class , 'delete_product_branch']);
    Route::post('store/addImage/{id}' , [ProductController::class , 'add_image']);

    Route::get('/allProducts' , [ProductController::class , 'get_all_products']);

    Route::post('/user/order/{id}', [ProductController::class , 'add_order']);
    Route::put('/user/order/edit/{id}' , [ProductController::class , 'edit_order']);
    Route::delete('/user/order/delete/{id}' , [ProductController::class , 'remove_order']);

    Route::get('/user/orders', [ProductController::class , 'get_orders']);

    Route::post('/user/buyOrder' , [ProductController::class , 'buy_order']);

    Route::post('/user/logout' , [UserController::class , 'logout']);
});
