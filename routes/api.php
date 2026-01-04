<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/user/register' , [UserController::class , 'register']);
Route::post('/user/login' , [UserController::class , 'login']);

Route::post('/admin/register' , [AdminController::class , 'register']);
Route::post('/admin/login' , [AdminController::class , 'login']);

Route::group(['middleware' => 'auth:admin'], function () {
    //CRUD for BaseProducts
    Route::get('admin/products' , [AdminController::class , 'shopProducts']);
    Route::post('admin/addProduct' , [AdminController::class , 'addProductBase']);
    Route::put('admin/editProduct/{id}' , [AdminController::class , 'editProducts']);
    Route::delete('admin/deleteProduct/{id}' , [AdminController::class , 'deleteProduct']);

    //add image to the product
    Route::post('admin/addImage/{id}' , [AdminController::class , 'addImageToProduct']);

    //CRUD for product sizeColor
    Route::post('admin/addProductSizeColor/{id}' , [AdminController::class , 'addProductBranch']);
    Route::put('admin/editProductSizeColor/{id}' , [AdminController::class , 'editProductSizeColor']);
    Route::delete('admin/deleteProductSizeColor/{id}' , [AdminController::class , 'deleteProductSizeColor']);

    Route::post('/admin/logout' , [AdminController::class , 'logout']);
});

Route::group(['middleware' => 'auth:user'], function(){
    Route::get('user/shop' , [UserController::class , 'viewShop']);

    //CRUD system for orders
    Route::get('/user/orders', [OrderController::class , 'getOrder']);
    Route::post('/user/order/{id}', [OrderController::class , 'addOrder']);
    Route::put('/user/order/edit/{id}' , [OrderController::class , 'editOrder']);
    Route::delete('/user/order/delete/{id}' , [OrderController::class , 'deleteOrder']);

    Route::post('/user/buyOrder' , [OrderController::class , 'buyOrders']);

    Route::post('/user/logout' , [UserController::class , 'logout']);
});

//“ Simplicity is the ultimate sophistication. ”
//  — Leonardo da Vinci
