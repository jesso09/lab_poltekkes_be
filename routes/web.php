<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

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

Route::get('/set-permissions', function () {
    $storagePath = $_SERVER['DOCUMENT_ROOT'].'/storage';
    $publicStoragePath = $_SERVER['DOCUMENT_ROOT'].'/public/storage';
    
    // Set permission to 775 for the storage directory
    if (File::exists($storagePath)) {
        chmod($storagePath, 0775);
        echo "Permissions for storage directory set to 775<br>";
    } else {
        echo "Storage directory not found<br>";
    }

    // Set permission to 775 for the public/storage directory
    if (File::exists($publicStoragePath)) {
        chmod($publicStoragePath, 0775);
        echo "Permissions for public/storage directory set to 775<br>";
    } else {
        echo "Public storage directory not found<br>";
    }

    return "Permissions updated.";
});
