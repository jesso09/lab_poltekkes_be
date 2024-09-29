<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/symlink', function () {
    $target =$_SERVER['DOCUMENT_ROOT'].'/storage/app/public';
    $link = $_SERVER['DOCUMENT_ROOT'].'/public/storage';
    symlink($target, $link);
    echo "Done";
 });

 Route::get('/unlink-symlink', function () {
    $link = $_SERVER['DOCUMENT_ROOT'].'/public/storage';
    
    if (file_exists($link)) {
        unlink($link);
        echo "Symbolic link deleted";
    } else {
        echo "Symbolic link not found";
    }
});

Route::get('/clear-all', function () {
    // Clear the configuration cache
    Artisan::call('config:cache');
    echo "Configuration cache cleared.<br>";

    // Clear the route cache
    Artisan::call('route:clear');
    echo "Route cache cleared.<br>";

    // Clear the view cache
    Artisan::call('view:clear');
    echo "View cache cleared.<br>";

    // Clear the application cache
    Artisan::call('cache:clear');
    echo "Application cache cleared.<br>";

    return "All caches cleared!";
});