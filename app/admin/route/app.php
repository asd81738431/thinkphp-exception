<?php
use think\facade\Route;


//Route::group('',function (){

    #Route::group('',function (){
        Route::any('index', 'Index/index');
    #})->middleware('wxauth');

//})->middleware('token')->ext('');