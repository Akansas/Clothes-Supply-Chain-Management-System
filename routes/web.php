<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Counter;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/counter',Counter::class);
Route::get('/clothes', function(){
    $clothes=[
        ["name"=>"dress","color"=>"red","size"=>10],
         ["name"=>"pants","color"=>"grey","size"=>12],
    ];
    return view('clothes.index',["material"=> "cotton", "clothes"=> $clothes]);
});

Route::get('/clothes/create', function () {
    return view('clothes.create');
});
Route::get('/clothes/{size}', function($size){
   
    return view('clothes.show',["size"=> $size]);
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
