<?php

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

Route::group(['prefix'=>'user', 'namespace'=>'\App\Domains\User\Controller'], function(){
    Route::any('/list','UserController@list')->name('user/list'); //人员列表
    Route::any('/add','UserController@add')->name('user/add');   //人员添加
    Route::any('/edit','UserController@edit')->name('user/edit'); //人员修改
    Route::any('/del','UserController@del')->name('user/del');   //人员删除
    Route::any('/export','UserController@export')->name('user/export');   //人员导出
    Route::any('/import','UserController@import')->name('user/import');   //表格导入
});

